<?php

/**

Plugin Name: Geolocalization of user IP
Plugin URI: http://kaplonski.pl/
Description: Plugin and widget which check geolokalisation of user and displays it on Google Maps
    Plugin is based on data from Santyago's TrackIP database and fregeoip.net database
Version: 1.0.1
Author: Sławek Kapłoński
Author URI: http://kaplonski.pl/
License: GPLv2 or later
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

include_once 'user_geoip_widget_class.php';

load_plugin_textdomain('user_geoip', false, basename( dirname( __FILE__ ) ) . '/languages/');

function geoip_initWidget($args){
    $widget = new user_geoip_widget();
    $widget->drawWidget($args);
}

wp_register_sidebar_widget('UserGeolocalization', __('User localization','user_geoip'), 'geoip_initWidget');

?>
