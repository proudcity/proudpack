<?php
/** proudpack.php
 *
 * Plugin Name: ProudPack
 * Plugin URI:  http://en.obenland.it/wp-proud-search/#utm_source=wordpress&utm_medium=plugin&utm_campaign=wp-proud-search
 * Description: Provides title suggestions while typing a search query, using the built in jQuery suggest script.
 * Version:     2.1.0
 * Author:      Konstantin Obenland
 * Author URI:  http://en.obenland.it/#utm_source=wordpress&utm_medium=plugin&utm_campaign=wp-proud-search
 * Text Domain: wp-proud-search
 * Domain Path: /lang
 * License:     GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */


add_action( 'rest_api_init', function () {
	  register_rest_route( 'wp/v2', '/menu', array(
		    'methods' => 'GET',
		    'callback' => 'rest_get_plugins',
		    /*'permission_callback' => function () {
		        return current_user_can( 'activate_plugins' );
		    }*/
	  ) );
	  register_rest_route( 'wp/v2', '/plugins', array(
		    'methods' => 'GET',
		    'callback' => 'rest_get_plugins',
		    /*'permission_callback' => function () {
		        return current_user_can( 'activate_plugins' );
		    }*/
	  ) );
	  register_rest_route( 'wp/v2', '/plugins', array(
		    'methods' => 'POST',
		    'callback' => 'rest_post_plugins',
		    /*'permission_callback' => function () {
		        return current_user_can( 'activate_plugins' );
		    }*/
	  ) );
} );

function rest_get_plugins() {
		// Check if get_plugins() function exists. This is required on the front end of the
		// site, since it is in a file that is normally only loaded in the admin.
		if ( ! function_exists( 'get_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		return get_plugins();
}

function rest_post_plugins() {
		return($_REQUEST);
		if ($_REQUEST['action'] == 'activate') {
				activate_plugins($_REQUEST['plugins']);
		}
		else {
				deactivate_plugins($_REQUEST['plugins']);
		}
}


function get_rest_plugins() {
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