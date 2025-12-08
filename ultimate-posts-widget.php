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

if (!class_exists(WP_Widget_Ultimate_Posts::class)) {
  require_once __DIR__ . '/src/WP_Widget_Ultimate_Posts.php';

  function init_wp_widget_ultimate_posts() {
    register_widget(WP_Widget_Ultimate_Posts::class);
  }

  add_action( 'widgets_init', 'init_wp_widget_ultimate_posts' );
}

add_action('admin_init', function () {
  require_once 'banner/misc.php';
});

add_action( 'wp_ajax_upw_hide_admin_notification', 'upw_hide_admin_notification_callback' );

function upw_hide_admin_notification_callback() {

  if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field($_POST['nonce']), 'upw_hide_admin_notification')) {
    wp_send_json_error();
    die;
  }

  $option_name = 'upw_hide_admin_notification';
  $new_value = 'yes';

  if ( get_option( $option_name ) !== false ) {
    update_option( $option_name, $new_value );
  } else {
    $deprecated = null;
    $autoload = 'no';
    add_option( $option_name, $new_value, $deprecated, $autoload );
  }
  wp_send_json_success();
  die;
}

// Activation of tryOutPlugins module
add_action('plugins_loaded', function () {

  if (!(class_exists('\Inisev\Subs\Inisev_Try_Out_Plugins') || class_exists('Inisev\Subs\Inisev_Try_Out_Plugins') || class_exists('Inisev_Try_Out_Plugins'))) {
    require_once __DIR__ . '/modules/tryOutPlugins/tryOutPlugins.php';
    $try_out_plugins = new \Inisev\Subs\Inisev_Try_Out_Plugins(__FILE__, __DIR__, 'Ultimate Posts Widget', 'plugins.php?s=Ultimate%20Posts%20Widget&plugin_status=all');
  }

});

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), function ($links) {

  $tifm_action = array('<a href="#!" id="upw_tifm_disable">' . __('Disable Plugin Test Feature', 'ultimate-posts-widget') . '</a>');
  if (get_option('_tifm_feature_enabled') === 'disabled') {
    $tifm_action = array('<a href="#!" id="upw_tifm_enable">' . __('Enable Plugin Test Feature', 'ultimate-posts-widget') . '</a>');
  }

  return array_merge($links, $tifm_action);

});

add_action('admin_footer', function () {

  global $pagenow;
  if ($pagenow === 'plugins.php') {
    ?>
    <script type="text/javascript">
      (function () {
        let nonceTIFM = "<?php echo wp_create_nonce('tifm_notice_nonce') ?>";
        jQuery('#upw_tifm_enable').on('click', (e) => {
          e.preventDefault();
          jQuery.post(ajaxurl, { action: 'tifm_save_decision', decision: 'true', nonce: nonceTIFM }).done(() => {
            window.location.reload();
          }).fail(() => {
            alert('There was an error and we could not update this option.');
          });
        });
        jQuery('#upw_tifm_disable').on('click', (e) => {
          e.preventDefault();
          jQuery.post(ajaxurl, { action: 'tifm_save_decision', decision: 'false', nonce: nonceTIFM }).done(() => {
            window.location.reload();
          }).fail(() => {
            alert('There was an error and we could not update this option.');
          });
        });
      })();
    </script>
    <?php
  }

});

if (!has_action('wp_ajax_tifm_save_decision')) {
  add_action('wp_ajax_tifm_save_decision', function () {

    // Nonce verification
    if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field($_POST['nonce']), 'tifm_notice_nonce')) {
      wp_send_json_error();
      return;
    }

    if (isset($_POST['decision'])) {

      if ($_POST['decision'] == 'true') {
        update_option('_tifm_feature_enabled', 'enabled');
        delete_option('_tifm_disable_feature_forever', true);
        wp_send_json_success();
        exit;
      } else if ($_POST['decision'] == 'false') {
        update_option('_tifm_feature_enabled', 'disabled');
        update_option('_tifm_disable_feature_forever', true);
        wp_send_json_success();
        exit;
      } else if ($_POST['decision'] == 'reset') {
        delete_option('_tifm_feature_enabled');
        delete_option('_tifm_hide_notice_forever');
        delete_option('_tifm_disable_feature_forever');
        wp_send_json_success();
        exit;
      }

      wp_send_json_error();
      exit;

    }

  });
}
