<?php

class user_geoip_widget {
    
    private $ip = null;
    
    private $title = null;
    
    private $errorMsg = null;
    private $city = null;
    private $ndeg = null;
    private $edeg = null;
    private $source = null;
    
    public function __construct() {
        $this->ip = $_SERVER['REMOTE_ADDR'];
        load_plugin_textdomain('user_geoip', false, basename( dirname( __FILE__ ) ) . '/languages/');
        $this->title = __("Your IP: ",'user_geoip');
        $this->title .= $this->ip;
    }
    
    
    public function drawWidget($args){
        extract($args);
        $this->prepareWidget();
        ?>
        <?php echo $before_widget; ?>
            <?php echo $before_title
                . $this->title
                . $after_title; ?>
            <?php echo $this->content; ?>
        <?php echo $after_widget; ?>
    <?php
    }
    
    private function prepareWidget(){
        if ($this->getLocalization()){
            $css = "<link href=\"".plugins_url( 'style.css', __FILE__ )."\" rel=\"stylesheet\" type=\"text/css\" />";
            $js = "<script type=\"text/javascript\" src=\"http://maps.googleapis.com/maps/api/js?sensor=false\"></script>
                         <script type=\"text/javascript\">
                            function initialize() {
                                var latlng = new google.maps.LatLng($this->ndeg,$this->edeg);
                                var myOptions = {
                                    zoom: 8,
                                    center: latlng,
                                    mapTypeId: google.maps.MapTypeId.ROADMAP
                                };
                                var map = new google.maps.Map(document.getElementById(\"geoip_map_canvas\"),myOptions);
                                var marker = new google.maps.Marker({
                                    position: latlng,
                                    map: map,
                                    title: '$this->ip'
                                });
                            }
                            initialize();
                            //jQuery(document).ready(initialize());
                        </script>";
            if ($this->source)
                $sourceInfo = "<div id=\"geoip_source_info\">".__('Source: ', "user_geoip").$this->source."</div>";
            else
                $sourceInfo = "";
            
            $info = "<div id=\"geoip_info\">";
            if ($this->city[0])
                $info .= $this->city.", ";
            if ($this->country[0])
                $info .= $this->country;
            $info .= "</div>";
            
            $this->content = $css.$info."<div id=\"geoip_map_canvas\"></div>".$sourceInfo.$js;
        } else {
            $this->content = "<div id=\geoip_error\">$this->errorMsg</div>";
        }
    } 
    
    private function getLocalization(){
        if ($this->checkApacheData())
            return True;
        else if ($this->checkSantyagoDB())
            return True;
        else if ($this->checkFreeGeoIpDB())
            return True;
        else
            return False;
    }
    
    
    private function checkApacheData(){
        if (apache_mod_loaded("mod_geoip")){
            $this->country = $_SERVER['GEOIP_COUNTRY_NAME'];
            $this->city = $_SERVER['GEOIP_CITY'];
            $this->ndeg = $_SERVER['GEOIPT_LATITUDE'];
            $this->edeg = $_SERVER['GEOIPT_LONGITUDE'];
            $this->source = "Apache Geoip module";
            return True;
        } else {
            return False;
        }
    }
    
    private function checkSantyagoDB(){
        $xml_data = simplexml_load_file("http://trackip.santyago.pl/api/get/xml/$this->ip");

        if ($xml_data->result == 'true'){
            $this->city = $xml_data->place;
            $this->country = "Poland";
            $this->ndeg = $xml_data->ndeg;
            $this->edeg = $xml_data->edeg;
            $this->ndeg = str_replace(",",".",$this->ndeg);
            $this->edeg = str_replace(",",".",$this->edeg);
            $this->source = "<a href=\"http://trackip.santyago.pl\">TrackIP</a>";
            return True;
        } else {
            return False;
        }
    }
    
    private function checkFreeGeoIpDB(){
        $xml_data = simplexml_load_file("http://freegeoip.net/xml/$this->ip");
        if (isset($xml_data->errcode)){
            $this->errorMsg = $xml_data->err;
            return False;
        } else {
            $this->city = $xml_data->City;
            $this->country = $xml_data->CountryName;
            $this->ndeg = $xml_data->Latitude;
            $this->edeg = $xml_data->Longitude;
            $this->source = "<a href=\"http://freegeoip.net/static/index.html\">freegeoip.net</a>";
            return True;
        }
    }
}

?>
