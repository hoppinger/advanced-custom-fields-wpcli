<?php
/*
Plugin Name: Advanced Custom Fields wp-cli Extension
Plugin URI: http://www.advancedcustomfields.com/add-ons/acf-wpcli/
Description: This extension for Advanced Custom Fields makes it possible to manage your field_groups through the console of wp-cli.
Version: 0.1
Author: Hoppinger
Author URI: http://www.hoppinger.com/
License: GPL
Copyright: Hoppinger
*/

if ( defined( 'WP_CLI' ) && WP_CLI ) {
  //Since version 5 uses json instead of xml, we have a new ACF command for it.
  if ( substr( get_option( 'acf_version' ), 0, 1 ) == 5 ) {
    // Include and register the class as the 'example' command handler
    include 'ACF5_Command.php';
    WP_CLI::add_command( 'acf', 'ACF5_Command' );
  } else {
    // Include and register the class as the 'example' command handler
    include 'ACFCommand.php';
    WP_CLI::add_command( 'acf', 'ACFCommand' );
  }
}

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
      'child_themes_shared' => ABSPATH . 'field-groups/shared-childs/',
    );

    $paths    = apply_filters( 'acfwpcli_fieldgroup_paths', $paths );
    $patterns = array();

    foreach ( $paths as $key => $value ) {
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
