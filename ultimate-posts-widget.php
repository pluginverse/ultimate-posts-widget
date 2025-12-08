<?php
/*
Plugin Name: Ultimate Posts Widget
Plugin URI: http://wordpress.org/plugins/ultimate-posts-widget/
Description: The ultimate widget for displaying posts, custom post types or sticky posts with an array of options.
Version: 2.3.2
Author: Clever Widgets
Author URI: https://themecheck.info
Text Domain: ultimate-posts-widget
License: MIT
*/

use UltimatePostsWidget\WP_Widget_Ultimate_Posts;

if(!defined('ABSPATH')){
    exit; // Exit if accessed directly
}

if ( ! class_exists(WP_Widget_Ultimate_Posts::class)) {
    require_once __DIR__.'/src/WP_Widget_Ultimate_Posts.php';

    function init_wp_widget_ultimate_posts()
    {
        register_widget(WP_Widget_Ultimate_Posts::class);
    }

    add_action('widgets_init', 'init_wp_widget_ultimate_posts');
}
