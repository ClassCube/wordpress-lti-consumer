<?php

/*
  Plugin Name:    LTI Consumer
  Plugin URI:     https://classcube.com
  Description:    Use your WordPress site as an LTI consumer
  Version:        0.0.1
  Author:         ClassCube
  Author URI:     https://classcube.com
  Text Domain:    cc-lti
  Domain Path:    /languages
 */

include(plugin_dir_path( __FILE__ ) . 'inc/class-lti-consumer.php');

if ( !class_exists( '\ClassCube\Smashing_Updater' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . 'inc/SmashingUpdater.php' );
}
$updater = new \ClassCube\Smashing_Updater( __FILE__ );
$updater->set_username( 'ClassCube' );
$updater->set_repository( 'wordpress-lti-consumer' );
$updater->initialize();
