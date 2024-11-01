<?php
/*
Plugin Name: Snap Skout Sharebar + Recommended Offers
Plugin URI: http://snapskout.com
Description: Snap Skout is a digital concierge that helps connect consumers with the brands they love, through the stories they read online. Publishers partner with Snap Skout to add the deal button to the Share Bar above articles on their site. Anytime there's a deal or promotion that matches the editorial content on the page, the Snap Skout button will appear. No distracting pop-ups, no irrelevant banner ads. Just a quick and easy drop-down tool that matches readers with the deals, steals and brand details of most interest to them, based on the articles they are reading.
Version: 1.0.35
Author: TalktUp LLC
Author URI: http://snapskout.com
License: GPL2


Copyright 2012 Michael Beacom  (email : michael.beacom@gmail.com)

Many thanks to Michael Beacom, whose plugin served as a big starting point for this one, which is a refactored
version which splits things into classes and has somewhat different functionality.

Copyright 2013, 2014 SnapSkout.com  (email : info@talktup.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
define( 'OUP_PLUGIN_FILE', __FILE__ );
define( 'OUP_DIR_URL', plugin_dir_url( __FILE__ ) );

if ( ! class_exists( 'OupSplClassLoader' ) ) {
    require_once( 'OupSplClassLoader.php' );
}

$loader = new OupSplClassLoader( 'Oup_WpPlugin', dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'src' );
//
// The _ namespace separator is to make this work in php 5.2
//
$loader->setNamespaceSeparator( '_' );
$loader->register();

$loader = new OupSplClassLoader( 'Katzgrau', dirname( __FILE__ ) . '/vendor/KLogger/src' );
$loader->setNamespaceSeparator( '_' );
$loader->register();
$oup_logdir = ( substr( PHP_OS, 0, 3 ) == 'WIN' ? getenv( 'TEMP' ) : '/tmp' ) . '/oup';
Oup_WpPlugin_ContentInjectors::$_logger = Katzgrau_Logger::instance( $oup_logdir, Oup_WpPlugin_Utility::get_logging_level() );


register_activation_hook( __FILE__, 'Oup_WpPlugin_Admin::load_options' );

if ( strstr( $_SERVER['REQUEST_URI'], 'wp-admin' ) ) {
    $adm = new Oup_WpPlugin_Admin();
}
else {
    $render = new Oup_WpPlugin_ContentInjectors();
    $render->setup_hooks();
}
