<?php

/*
Plugin Name: Polyscripting
Plugin URI: http://github
Description: A brief description of the Plugin.
Version: 2.0
Author: bluegaston
License:
Copyright (c) 2020 Polyverse Corporation
*/
// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly



define( 'POLYSCRIPT_VERSION', '1.0.0' );
define( 'POLYSCRIPT__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

register_activation_hook( __FILE__, array( 'Polyscript', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'Polyscript', 'plugin_deactivation' ) );
register_uninstall_hook( __FILE__, array( 'Polyscript', 'plugin_uninstall' ) );

require_once( POLYSCRIPT__PLUGIN_DIR . 'class.polyscript.php' );
require_once( POLYSCRIPT__PLUGIN_DIR . 'class.db.php' );
require_once( POLYSCRIPT__PLUGIN_DIR . 'polyscriptingstate.php' );


add_action( 'init', array( 'PolyscriptingState', 'init' ) );
add_filter("plugin_action_links_" . plugin_basename( __FILE__ ), 'polyscript_action_links');
add_action( 'init', array( 'Polyscript', 'init' ) );

function polyscript_action_links($links)
{
    $links = array_merge(array(
        '<a href="' . esc_url(admin_url('options-general.php?page=polyscript')) . '">' . __('Settings', 'textdomain') . '</a>'),
        $links);

    return $links;
}