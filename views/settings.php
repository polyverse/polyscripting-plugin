<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
if (PolyscriptingState::check_warning()) {
    Polyscript::view('polyscript_warning');
}
if (isset($_POST['submit'])) {
    check_admin_referer('polyscript-admin');
    Polyscript::update_options(sanitize_text_field($_POST['notification-answer']), sanitize_text_field($_POST['widget-enable'])) ?>
    <div class="akismet-setup-instructions">
        <p><?php esc_html_e('Settings saved.', 'polyscript-settings'); ?></p>
    </div>
    <script>window.location.reload()</script>
    <?php
//echo '<div id="message" class="updated"><p>' . __( 'Options updated. Changes to the Admin Menu and Admin Bar will not
//appear until you leave or reload this page.', 'polyscript-settings' );'</p></div>';
  }
global $wpdb;
$db_instance = get_option('polyscript_db');
//Get the active tab from the $_GET param
$default_tab = null;
$tab = isset($_GET['tab']) ? $_GET['tab'] : $default_tab;

?>
<!-- Our admin page content should all be inside .wrap -->
<div class="wrap">
    <!-- Print the page title -->
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <!-- Here are our tabs -->
    <nav class="nav-tab-wrapper">
        <a href="?page=polyscript"
           class="nav-tab <?php if ($tab === null): ?>nav-tab-active<?php endif; ?>">Settings</a>
        <a href="?page=polyscript&tab=history"
           class="nav-tab <?php if ($tab === 'history'): ?>nav-tab-active<?php endif; ?>">History</a>
        <a href="?page=polyscript&tab=health"
           class="nav-tab <?php if ($tab === 'health'): ?>nav-tab-active<?php endif; ?>">Health</a>
    </nav>

    <div class="tab-content">
        <?php switch ($tab) :
            case 'history':
                ?>
                <div>
                    <?php
                    $retrieve_data = $wpdb->get_results("SELECT instance_id, operation, time FROM $db_instance->operation_db ORDER BY time DESC LIMIT 100")
                    ?>
                    <h2>Instance History and State</h2>
                    <table class="pstable">
                        <tr class="pstr">
                            <th class="psth">Instance ID</th>
                            <th class="psth">Operation</th>
                            <th class="psth">Time</th>
                        </tr>
                        <?php
                        foreach ($retrieve_data as $retrieved_data) { ?>
                            <tr class="pstr">
                                <td class="pstd"><?php echo $retrieved_data->instance_id; ?></td>
                                <td class="pstd"><?php echo $retrieved_data->operation; ?></td>
                                <td class="pstd"><?php echo $retrieved_data->time; ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                </div>
                <?php
                break;
            case 'health':
                ?>
                <div>
                    <?php
                    $retrieve_data = $wpdb->get_results("SELECT * FROM $db_instance->health_db");
                    ?>
                    <h2>Instance Health</h2>
                    <table class="pstable">
                        <tr class="pstr">
                            <th class="psth">InstanceID</th>
                            <th class="psth">State</th>
                            <th class="psth">Health</th>
                            <th class="psth">Time</th>
                        </tr>
                        <?php
                        foreach ($retrieve_data as $retrieved_data) { ?>
                            <tr class="pstr">
                                <td class="pstd"><?php echo $retrieved_data->instance_id; ?></td>
                                <td class="pstd"><?php echo $retrieved_data->php_state; ?></td>
                                <td class="pstd"><?php echo $retrieved_data->health; ?></td>
                                <td class="pstd"><?php echo $retrieved_data->time; ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                </div>
                <?php
                break;
            default:
                ?>
                <div class="wrap">
                    <h1><?php echo esc_html_x('Polyscript Settings', 'settings page title', 'polyscript-settings'); ?></h1>
                    <h2><?php echo esc_html_x('Polyscript Toggle', 'toggle title', 'polyscript-settings'); ?></h2>
                    <?php Polyscript::view('buttons', array('state' => $state)); ?>
                    <form action="" method="post" id="polyscript-settings">
                        <h2><?php echo esc_html_x('Notification settings', 'settings page title', 'polyscript-settings'); ?></h2>
                        <ul>
                            <li><label for="Add notification to admin bar."><input type="radio" id="admin-bar"
                                                                                   name="notification-answer"
                                                                                   value="admin-bar" <?php checked($header_type == 'admin-bar'); ?> />
                                    <label><?php esc_html_e('Add notification to admin bar.', 'polyscript-settings'); ?></label>
                                </label>
                            </li>
                            <li><label for="Add notification to dashboard header."><input type="radio"
                                                                                          id="dashboard-header"
                                                                                          name="notification-answer"
                                                                                          value="header" <?php checked($header_type == 'header'); ?> />
                                    <label><?php esc_html_e('Add notification to dashboard header.', 'polyscript-settingss'); ?></label>
                                </label>
                            </li>
                            <li><label for="Disable header notification."><input type="radio" id="dashboard-header"
                                                                                 name="notification-answer"
                                                                                 value="disabled" <?php checked(!get_option('polyscript_header_set') || $header_type == 'disabled'); ?> />
                                    <label><?php esc_html_e('Disable polyscript notification.', 'polyscript-settingss'); ?></label>
                                </label>
                            </li>
                        </ul>
                        <h2><?php echo esc_html_x('Widget settings', 'widget', 'polyscript-widget'); ?></h2>
                        <ul>
                            <li><label for="Enable Polyscripting Widget on Dashboard."><input type="hidden"
                                                                                              id="widget-enable"
                                                                                              name="widget-enable"
                                                                                              value="false"/>
                                    <input type="checkbox" id="widget-enable"
                                           name="widget-enable"
                                           value="true" <?php checked($widget_set) ?> />
                                    <label><?php esc_html_e('Enable Polyscripting Widget on Dashboard.', 'polyscript-settings'); ?></label>
                                </label>
                            </li>
                        </ul>
                        <?php wp_nonce_field('polyscript-admin'); ?>
                        <input class="ps-button ps-accept" type="submit" name="submit"
                               value="<?php esc_attr_e('Save Changes', 'polyscript-settings'); ?>">
                    </form>
                </div>
                <?php
                break;
        endswitch; ?>
    </div>
</div>