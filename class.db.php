<?php
if (!defined('ABSPATH')) exit;

class Db
{
    public function __construct()
    {
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        global $wpdb;

        $this->operation_db = $wpdb->prefix . 'oppDB';
        $this->charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $this->operation_db (
                job_id mediumint(9) NOT NULL AUTO_INCREMENT,
                time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                user text NOT NULL,
                operation text NOT NULL,
                instance_id text NOT NULL,
                PRIMARY KEY  (job_id)
                ) $this->charset_collate;";

        dbDelta($sql);

        $this->record_operation("install");

        $this->health_db = $wpdb->prefix . 'healthDB';
        $sql = "CREATE TABLE $this->health_db (
                instance_id varchar(200) UNIQUE NOT NULL,
                time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                php_state text NOT NULL,
                health text NOT NULL,
                PRIMARY KEY (instance_id)
                ) $this->charset_collate;";

        dbDelta($sql);

        return $this->ping_instance();
    }


    public
        $charset_collate,
        $operation_db,
        $health_db;

    public function record_operation($poly_operation)
    {
        global $wpdb;

        $wpdb->insert($this->operation_db, array(
                'time' => current_time('mysql'),
                'user' => wp_get_current_user()->user_email,
                'operation' => $poly_operation,
                'instance_id' => gethostname(),
            )
        );
    }

    function ping_instance()
    {
        global $wpdb;
        $instance_id = gethostname();
        $time = current_time('mysql');
        $php_state = PolyscriptingState::get_live_state() ? 'polyscript on' : 'polyscript off';
        $health = "active";
        $sql = "INSERT INTO {$this->health_db} (time, php_state, instance_id, health) 
                VALUES ('$time', '$php_state', '$instance_id', '$health') 
                ON DUPLICATE KEY UPDATE time='$time', php_state='$php_state', health='$health'";
        $wpdb->query($sql);

        $sql = "UPDATE {$this->health_db} SET health='expired' WHERE time<=CURRENT_DATE - INTERVAL 30 DAY";
        $wpdb->query($sql);
        $sql = "UPDATE {$this->health_db} SET health='inactive' WHERE time<=CURRENT_DATE - INTERVAL 1 DAY";
        $wpdb->query($sql);
    }

    function delete_db()
    {
        global $wpdb;
        $sql = "DROP TABLE IF EXISTS $this->operation_db";
        $wpdb->query($sql);
        $sql = "DROP TABLE IF EXISTS $this->health_db";
        $wpdb->query($sql);
    }
 }