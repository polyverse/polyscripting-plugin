<?php
/*
   Copyright (c) 2020 Polyverse Corporation
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Polyscript
{
    const WIDGET_KEY = "Polyscript_Widget";

    private static $initiated = false;

    public static function init()
    {
        if (!self::$initiated) {
            self::init_polyscript();
        }
    }


    private static function init_polyscript()
    {
        self::$initiated = true;

        add_action('wp_dashboard_setup', array('Polyscript', 'register_dashboard_widget'));
        add_action('admin_enqueue_scripts', array('Polyscript', 'polyscript_load_styles'));
        add_action('admin_head', array('Polyscript', 'poly_css'));
        add_action('admin_notices', array('Polyscript', 'set_header_status'));
        add_action('admin_menu', array('Polyscript', 'register_settings_page'));
        add_action('admin_bar_menu', array('Polyscript', 'add_icon_to_admin_bar'), 999);
        add_filter('plugin_action_links_polyscripting/polyscripting.php', array('Polyscript', 'polyscript_action_links'));
    }

    public static function register_dashboard_widget()
    {
        global $wp_meta_boxes;
        if (self::widget_set()) {

            wp_add_dashboard_widget(
                    self::WIDGET_KEY,
                    esc_html__('Polyscripting', 'polyscripting_for_wordpress'),
                    array('Polyscript', 'dashboard_widget_content')
                    );

            $normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
            $widget_instance = array(self::WIDGET_KEY => $normal_dashboard[self::WIDGET_KEY]);
            unset($normal_dashboard[self::WIDGET_KEY]);
            $sorted_dashboard = array_merge($widget_instance, $normal_dashboard);
            $wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
        }
    }

    public static function dashboard_widget_content()
    {
        Polyscript::view('widget', array('state' => PolyscriptingState::get_saved_state()));
    }

    public static function polyscript_load_styles()
    {
        wp_register_style('polyscript.css', plugin_dir_url(__FILE__) . 'includes/polyscript.css', array(), POLYSCRIPT_VERSION);
        wp_enqueue_style('polyscript.css');

    }

    public static function polyscript_action_links($links)
    {

        $links = array_merge(array(
                    '<a href="' . esc_url(admin_url('options-general.php?page=polyscript')) . '">' . __('Settings', 'textdomain') . '</a>'),
                $links);

        return $links;

    }

    public static function update_options($mode, $widget)
    {
        update_option('polyscript_header_set', $mode);
        update_option('polyscript_widget', $widget);

    }

    public static function add_icon_to_admin_bar($wp_admin_bar)
    {
        if (self::header_type() == 'admin-bar') {
            $args = array(
                    'id' => 'Polyscript',
                    'title' => __('Polyscript ' . self::polyscript_enabled(), 'textdomain'),
                    'href' => esc_url(admin_url('options-general.php?page=polyscript'))
                    );
            $wp_admin_bar->add_node($args);
        }
    }

    public static function view($name, array $args = array())
    {
        PolyscriptingState::sanitize_state();
        foreach ($args AS $key => $val) {
            $$key = $val;
        }
        load_plugin_textdomain('polyscript');

        $file = POLYSCRIPT__PLUGIN_DIR . 'views/' . $name . '.php';

        include($file);
    }


    public static function set_header_status()
    {
        if (self::header_type() == 'header') {
            $lang = '';
            if ('en_' !== substr(get_user_locale(), 0, 3)) {
                $lang = ' lang="en"';
            }

            printf(
                    '<p id="poly"><span class="screen-reader-text">%s </span><span dir="ltr"%s>%s</span></p>',
                    __('Polyscript Status:'),
                    $lang,
                    __('Polyscript Status: ' . self::polyscript_enabled(), 'textdomain'));
        }
    }

    public static function poly_css()
    {
        Polyscript::view('header_notification');
    }


    public static function register_settings_page()
    {
        add_options_page('Polyscripting For WordPress', 'Polyscripting', 'manage_options', 'polyscript', array('Polyscript', 'load_settings'));
    }

    public static function load_settings()
    {
        if (!self::dependencies_check()) {
            Polyscript::view('no-polyscript');
        } else {
            Polyscript::view('settings', array(
                        'header_type' => self::header_type(),
                        'widget_set' => self::widget_set(),
                        'state' => PolyscriptingState::get_saved_state()));
        }
    }

    private static function polyscript_enabled()
    {
        try {
            $result = eval("if (true) { echo ''; return 1; }");
        } catch (ParseError $result) {
            return 'Enabled';
        }
        return 'Disabled';
    }

    public static function plugin_activation()
    {
        add_option('polyscript_header_set', 'admin-bar');
        PolyscriptingState::get_live_state() == 'Enabled'
            ? update_option('polyscript_state', 'on')
            :  update_option('polyscript_state', 'off') ;
        global $wpdb;
        $table_name = $wpdb->prefix . 'occDB';
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                user text NOT NULL,
                operation text NOT NULL,
                PRIMARY KEY  (id)
                ) $charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
        $wpdb->insert( $table_name, array( 
		    'time' => current_time( 'mysql' ), 
		    'user' => wp_get_current_user()->user_email,  
            'operation' => 'install',
	        ) 
        );
    }

    public static function widget_set()
    {
        return get_option('polyscript_widget') == 'true';
    }

    public static function header_type()
    {
        return get_option('polyscript_header_set');
    }

    public static function dependencies_check() {
        return file_exists("/usr/local/bin/polyscripting/build-scrambled.sh");
    }
}
