<?php
/**
 * Implements ProudPack Phone Home command.
 */
class ProudPackCommand extends WP_CLI_Command {

    /**
     * Calls back to ProudCity API to get initial config settings 
     * 
     * ## OPTIONS
     * 
     * ## EXAMPLES
     * 
     *     wp proudpack phonehome
     *
     * @synopsis 
     */
    function phonehome( $args, $assoc_args ) {
        //list( $name ) = $args;
        $request = wp_remote_get(PROUD_URL . '/sites/' . PROUD_ID . '/launched');
        $response = json_decode( wp_remote_retrieve_body( $request ) );

        // Set options
        update_option( 'blogname', $response->location->city );
        update_option( 'lat', $response->location->lat );
        update_option( 'lng', $response->location->lng );
        update_option( 'city', $response->location->city );
        update_option( 'state', $response->location->stateFull );

        // Set theme settings
        $mods = get_option( 'theme_mods_wp-proud-theme', array() );
        if (!empty($response->settings->colors->main)) {
           $mods['color_main'] = $response->settings->colors->main; 
        }
        if (!empty($response->settings->colors->secondary)) {
           $mods['color_secondary'] = $response->settings->colors->secondary; 
        }
        if (!empty($response->settings->colors->highlight)) {
           $mods['color_highlight'] = $response->settings->colors->highlight; 
        }
        update_option( 'theme_mods_wp-proud-theme', $mods );
        


        // Print a success message
        WP_CLI::success( print_r($response,1));

        WP_CLI::success( PROUD_URL . '/sites/' . PROUD_ID . '/launched'.$response->color->highlight.print_r(get_option( 'theme_mods_wp-proud-theme', array() ),1));
    }
}

WP_CLI::add_command( 'proudpack', 'ProudPackCommand' );