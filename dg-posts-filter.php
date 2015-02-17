<?php
/**
 * Plugin Name: Post Filters by Digitize
 * Plugin URI: http://www.digitize-info.com
 * Description: post management made easy by this plugin provides you with the various post filters!
 * Version: 1.0.0
 * Author: Digitize Info System
 * Author URI: http://www.digitize-info.com
 * Text Domain: dg_posts_filter
 * Domain Path: /languages/
 * Network: false
 * License: GPL2
 */

defined('ABSPATH') or die('No script kiddies please!'); // Security check don't allow to direct executing

define('DG_TEXT_DOMAIN', 'dg_posts_filter');

load_plugin_textdomain(DG_TEXT_DOMAIN, false, basename(dirname(__FILE__ )) . '/languages'); // Load Text Domain for plugin

wp_enqueue_style('jquery-ui', plugins_url('/css/jquery-ui.css', __FILE__));
wp_enqueue_script('jquery-ui-datepicker', array('jquery'));
wp_enqueue_script('dg-posts-filter', plugins_url('/js/dg-posts-filter.js', __FILE__), array('jquery-ui-datepicker'), '1.0.0', true);

// Add coupon filter for the admin management
add_action('restrict_manage_posts', 'dg_post_filters');
function dg_post_filters($defaults)
{
    global $typenow, $pagenow, $wpdb;

    if(is_admin() && $typenow == 'post' && $pagenow == 'edit.php')
    {
        $blog_id = get_current_blog_id(); // Get curret blog id
        $users = get_users(array( // Fire custom query to get Admin and Editors
            'fields' => 'ID',
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key' => $wpdb->get_blog_prefix($blog_id) . 'capabilities',
                    'value' => 'administrator',
                    'compare' => 'like'
                ),
                array(
                    'key' => $wpdb->get_blog_prefix($blog_id) . 'capabilities',
                    'value' => 'editor',
                    'compare' => 'like'
                )
            )
        ));
        $include = implode(',', $users);

        wp_dropdown_users(array(
            //'show_option_all'         => null, // string
            'show_option_none'        => __('Select Author' , DG_TEXT_DOMAIN), // string
            //'hide_if_only_one_author' => yes, // string
            'orderby'                 => 'ID',
            'order'                   => 'ASC',
            'include'                 => $include, // string
            //'exclude'                 => null, // string
            //'multi'                   => false,
            'show'                    => 'display_name',
            'echo'                    => true,
            //'selected'                => $_GET['author'],
            'include_selected'        => true,
            'name'                    => 'author', // string
            //'id'                      => null, // integer
            'class'                   => 'postform', // string
            //'blog_id'                 => $GLOBALS['blog_id'],
            //'who'                     => 'administrator,editor' // string
        ));

        printf('<input type="text" class="postform" id="start_date" name="start_date" value="%s" placeholder="' . __('From' , DG_TEXT_DOMAIN) . '" />', $_GET['start_date']);
        printf('<input type="text" class="postform" id="end_date" name="end_date" value="%s" placeholder="' . __('To' , DG_TEXT_DOMAIN) . '" />', $_GET['end_date']);
    }
}

add_filter('parse_query','parse_query_between_start_and_end_date');
function parse_query_between_start_and_end_date($query)
{
    global $typenow, $pagenow;

    if (is_admin() && $typenow == 'post' && $pagenow == 'edit.php')
    {
        $arr_filter = '';
        if(!empty($_GET['start_date']) && !empty($_GET['end_date'])) // Filter by start and end date
        {
            $arr_filter = array(
                'after' => $_GET['start_date'],
                'before' => $_GET['end_date'],
                'inclusive' => true,
            );
        }
        elseif(!empty($_GET['start_date'])) // Filter by start date
        {
            $arr_filter = array(
                'after' => $_GET['start_date'],
                'inclusive' => true,
            );
        }
        elseif(!empty($_GET['end_date'])) // Filter by end date
        {
            $arr_filter = array(
                'before' => $_GET['end_date'],
                'inclusive' => true,
            );
        }

        if(!empty($arr_filter))
            $query->set('date_query', $arr_filter);
    }
}