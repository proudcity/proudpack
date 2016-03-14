<?php
/** proudpack.php
 *
 * Plugin Name: ProudPack
 * Description: Provides integration between the WordPress ReST API and Calypso (via the proudcity-api).
 * Version:   2.1.0
 * Author:    ProudCity
 * Author URI:  https://proudcity.com
 * Text Domain: wp-proud-search
 * License:   Affero GPL v3
 */

namespace Proudpack;

if ( defined('WP_CLI') && WP_CLI ) {
  include __DIR__ . '/cli/proudpack-phone-home.php';
}

class Proudpack {

  public function __construct() {
    $this->rest_router();

    $this->options = [
      'blogname',
      'blogdescription',

      'city',
      'state',
      'lat',
      'lng',

      'external_link_window',
      'agency_label',

      'google_analytics_key',

      'search_service',
      'search_google_key',

      'payment_service',
      'payment_stripe_type',
      'payment_stripe_key',
      'payment_stripe_secret',

      '311_service',
      '311_link_create',
      '311_link_status',

      'mapbox_token',
      'mapbox_map',

      'embed_code',
      'validation_metatags',

      'alert_active',
      'alert_message',
      'alert_severity',
    ];
  }

  public function rest_router() {
    add_action( 'rest_api_init', function () {
      /*register_rest_route( 'wp/v2', '/menu', array(
        'methods' => 'GET',
        'callback' => [$this, 'rest_get_plugins'],
        /*'permission_callback' => function () {
          return current_user_can( 'activate_plugins' );
        }
      ) );*/
      register_rest_route( 'wp/v2', '/plugins', array(
        'methods' => 'GET',
        'callback' => [$this, 'rest_get_plugins'],
        'permission_callback' => function () {
          return current_user_can( 'activate_plugins' );
        }
      ) );
      register_rest_route( 'wp/v2', '/plugins', array(
        'methods' => 'POST',
        'callback' => [$this, 'rest_post_plugins'],
        'permission_callback' => function () {
          return current_user_can( 'activate_plugins' );
        }
      ) );
      register_rest_route( 'wp/v2', '/options', array(
        'methods' => 'GET',
        'callback' => [$this, 'rest_get_options'],
        'permission_callback' => function () {
          return current_user_can( 'edit_proud_options' );
        }
      ) );
      register_rest_route( 'wp/v2', '/options', array(
        'methods' => 'POST',
        'callback' => [$this, 'rest_post_options'],
        'permission_callback' => function () {
          return current_user_can( 'edit_proud_options' );
        }
      ) );
    } );
  }
  
  public function rest_get_plugins() {
    // Check if get_plugins() function exists. This is required on the front end of the
    // site, since it is in a file that is normally only loaded in the admin.
    if ( ! function_exists( 'get_plugins' ) ) {
      require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    $plugins = get_plugins();
    foreach ($plugins as $key => $plugin) {
      $plugins[$key]['Active'] = is_plugin_active($key);
    }

    return $plugins;
  }

  public function rest_post_plugins() {
    if ($_POST['action'] == 'activate') {
      activate_plugin($_POST['plugin']);
    }
    else {
      deactivate_plugins($_POST['plugin']);
    }
    return($_POST);
  }


  public function get_rest_plugins() {
     // Get the nav menu based on $menu_name (same as 'theme_location' or 'menu' arg to wp_nav_menu)
    // This code based on wp_nav_menu's code to get Menu ID from menu slug

    $menu_name = 'custom_menu_slug';

    if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[ $menu_name ] ) ) {
    $menu = wp_get_nav_menu_object( $locations[ $menu_name ] );

    $menu_items = wp_get_nav_menu_items($menu->term_id);

    $menu_list = '<ul id="menu-' . $menu_name . '">';

    foreach ( (array) $menu_items as $key => $menu_item ) {
      $title = $menu_item->title;
      $url = $menu_item->url;
      $menu_list .= '<li><a href="' . $url . '">' . $title . '</a></li>';
    }
    $menu_list .= '</ul>';
    } else {
    $menu_list = '<ul><li>Menu "' . $menu_name . '" not defined.</li></ul>';
    }
    // $menu_list now ready to output
  }

  public function rest_get_options() {
    $return = [];
    foreach ($this->options as $option) {
      $return[$option] = get_option($option);
    }
    return $return;
  }

  public function rest_post_options($data) {
    $out = [];
    foreach ($_POST as $key => $value) {
      if (in_array($key, $this->options)) {
        //@todo: sanitization
        update_option($key, $value);
        $out[$key] = $value;
      }
    }
    return $out;
  }


} // Class


new Proudpack();