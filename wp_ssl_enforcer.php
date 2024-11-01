<?php
/*
 * Plugin Name: WordPress SSL (HTTPS) Enforcer
 * Plugin URI: https://www.base29.com
 * Description: A simple wordpress plugin to enforce HTTPS if the SSL certificate is installed for the domain.
 * Author: Base29
 * Author URI: http://www.base29.com
 * Version: 1.0.2
 * License: GPL2
 * Text Domain: wp-ssl-enforcer
 * Domain Path: /languages
 * 
 * #####################################################################################################
 * #                                                                                                   # 
 * # WordPress SSL (HTTPS) Enforcer is free software: you can redistribute it and/or modify            # 
 * # it under the terms of the GNU General Public License as published by                              # 
 * # the Free Software Foundation, either version 2 of the License, or                                 # 
 * # any later version.                                                                                # 
 * #                                                                                                   # 
 * # WordPress SSL (HTTPS) Enforcer is distributed in the hope that it will be useful,                 # 
 * # but WITHOUT ANY WARRANTY; without even the implied warranty of                                    # 
 * # MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the                                      #         
 * # GNU General Public License for more details.                                                      # 
 * #                                                                                                   #
 * # You should have received a copy of the GNU General Public License                                 #
 * # along with WordPress SSL (HTTPS) Enforcer. If not, see https://www.gnu.org/licenses/gpl-2.0.html. #
 * #####################################################################################################
 */

class WSE {
    
    function __construct(){
        add_action('admin_menu', array($this, 'wse_admin_menu'));
        add_action('admin_init', array($this, 'wse_settings'));
        add_action('init', array($this, 'wse_force_https'));
        add_action('admin_init', array($this, 'wse_force_admin_https'));
    }
    
    function wse_settings(){
        register_setting('wse_plugin_settings', 'wse_domain_to_force_https');
        register_setting('wse_plugin_settings', 'wse_force_https_on_admin');
        register_setting('wse_plugin_settings', 'wse_force_https_on_frontend');
    }
    
    // Add admin menu
    function wse_admin_menu() {
        add_submenu_page('options-general.php', 'WP SSL Enforcer Settings', 'WP SSL Enforcer', 'manage_options', 'wse_settings', array($this, 'wse_settings_page'));
    }
    
        // Settings page
    function wse_settings_page() {

        echo '<div class="wrap">';
        echo '<h1>' . get_admin_page_title() . '</h1>';
        ?>
        <form action="options.php" method="post">

            <?php settings_fields('wse_plugin_settings'); ?>
            <table width="50%" border="0" cellspacing="0" cellpadding="0" style="margin-top: 18px;">
                <?php if(is_multisite()): ?>
                <tr>
                    <td><h4><label><?php _e('Domain Name', 'wp-ssl-enforcer') ?></label></h4></td>
                    <td><input type="text" name="wse_domain_to_force_https" value="<?php echo get_option('wse_domain_to_force_https') ?>"></td>
                </tr>
                <?php endif ?>
                
                                <tr>
                    <td style="padding:0 0 30px"><h4><label><?php _e('Force HTTPS on Frontend', 'wp-ssl-enforcer') ?></label></h4></td>
                    <td style="padding:0 0 30px"><input type="checkbox" value="1" <?php checked(1, get_option('wse_force_https_on_frontend'), TRUE); ?> name="wse_force_https_on_frontend" ></td>
                </tr>
                
                <tr>
                    <td style="padding:0 0 30px"><h4><label><?php _e('Force HTTPS on Dashboard', 'wp-ssl-enforcer') ?></label></h4></td>
                    <td style="padding:0 0 30px"><input type="checkbox" value="1" <?php checked(1, get_option('wse_force_https_on_admin'), TRUE); ?> name="wse_force_https_on_admin" ></td>
                </tr>
                
                <tr>
                    <td><input type="submit" value="<?php _e('Save Changes', 'wp-ssl-enforcer'); ?>" name="submit" id="submit" class="button button-primary"></td>
                </tr>
            </table>
        </form>
        <?php
        echo '</div>';
    }
    //Forcing SSL on the frontend
    function wse_force_https(){
        //Checking option for forcing https on frontend
        $checkFrontend = get_option('wse_force_https_on_frontend');
        if($checkFrontend == '1'){
            //Checking if the site is a multisite
            if(is_multisite()){

                $frontendUrl = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                $domain = get_option('wse_domain_to_force_https');

                if(!is_ssl() && strpos($frontendUrl, $domain)){
                    wp_redirect('https://' . $frontendUrl, 301);
                    exit();
                }
            } 
                
            if(!is_ssl()){
                    wp_redirect('https://' . $_SERVER['HTTP_POST'] . $_SERVER['REQUEST_URI'], 301);
                    exit();
            }
            
        }

    }
    //Forcing SSL on admin area
    function wse_force_admin_https(){
        $adminUrl = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $checkAdmin = get_option('wse_force_https_on_admin');
        if($checkAdmin == '1' && strpos($adminUrl, '/wp-admin/')){
            force_ssl_admin(TRUE);
        }
    }
}

new WSE;
