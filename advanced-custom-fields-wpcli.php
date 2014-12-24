<?php
/*
Plugin Name: Advanced Custom Fields WP-CLI
Plugin URI: https://github.com/hoppinger/advanced-custom-fields-wpcli
Description: Manage your ACF field groups in WP-CLI
Version: 2.0.0
Author: Hoppinger
Author URI: http://www.hoppinger.com/
License: MIT
https://github.com/hoppinger/advanced-custom-fields-wpcli/blob/master/LICENCE.txt
*/

load_textdomain( 'acf-wpcli', __DIR__ . '/languages/' . WPLANG . '.mo' );

define( 'ACF_WPCLI_ROOT', __DIR__ );
define( 'ACF_WPCLI_URL', plugin_dir_url( __FILE__ ) );

require 'vendor/autoload.php';

// instantiate loader and register namespaces
$loader = new \Aura\Autoload\Loader;
$loader->register();
$loader->addPrefix( 'ACFWPCLI', __DIR__ . '/src/' );

// instantiate this plugin
$plugin = new \ACFWPCLI\Plugin;


// [LEGACY] below this line

/*
 * add the php field_groups to our wordpress installation on runtime
 */

if ( ! defined( 'WP_CLI' ) ) {
  function acf_wpcli_register_groups() {
    global $blog_id;
    if ( function_exists( "register_field_group" ) ) :
      global $wpdb;
    $db_field_groups = $wpdb->get_results( "SELECT post_title FROM {$wpdb->posts} WHERE post_type='acf' AND post_status='publish';" );

    $db_field_group_titles = array();
    foreach ( $db_field_groups as $db_group ) :
      $db_field_group_titles[] = $db_group->post_title;
    endforeach;


    $paths = array(
      'active_theme'        => get_template_directory() . '/field-groups/',
      'active_child_theme'  => get_stylesheet_directory() . '/field-groups/',
    );

    $paths    = apply_filters( 'acfwpcli_fieldgroup_paths', $paths );
    $patterns = array();

    foreach ( $paths as $key => $value ) {
      if( ! is_dir($value) ){
        continue;
      }
      $patterns[ $key ] = trailingslashit( $value ) . '*/data.php';
    }

    $added_groups = array();
    foreach ( $patterns as $pattern ) {
      // register the field groups specific for this subsite
      foreach ( glob( $pattern ) as $file ) {
        $group = acf_wpcli_get_file_data( $file );

        // Don't register group when the group is already in the DB
        if ( ! in_array( $group['title'] , $db_field_group_titles ) )
          register_field_group( $group );
        $added_groups[] = $group['title'];
      }
    }

    endif;
  }
  add_action( 'plugins_loaded', 'acf_wpcli_register_groups' );

  function acf_wpcli_get_file_data( $file ) {
    if ( ! is_readable( $file ) || ! is_file( $file ) )
      return false;

    include $file;
    return $group;
  }
}
