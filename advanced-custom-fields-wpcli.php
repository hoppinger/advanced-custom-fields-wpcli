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

if ( defined('WP_CLI') && WP_CLI ) {
  
  // Include and register the class as the 'example' command handler
  include('ACFCommand.php');
  WP_CLI::addCommand( 'acf', 'ACFCommand' );
}

/* 
 * add the php field_groups to our wordpress installation on runtime
 */

if (!defined('WP_CLI') ) {
  function acf_wpcli_register_groups() {
    global $blog_id;
    if(function_exists("register_field_group")) :
      $db_field_groups = get_posts(array(
        'post_type'   =>  'acf',
      ));
      
      $db_field_group_titles = array();
      foreach($db_field_groups as $db_group ) :
        $db_field_group_titles[] = $db_group->post_title;
      endforeach;
      
      $path_pattern = ABSPATH . 'field_groups/' . $blog_id . '/*/data.php';
      
      function get_data($f)
      {
        if (!is_readable($f) || !is_file($f))
          return false;
        
        include $f;
        return $group;
      }
      
      foreach (glob($path_pattern) as $file) {
        $group = get_data($file);
        if(!in_array($group['title'] , $db_field_group_titles))
          register_field_group($group);
      }
    endif;
  }
  add_action('plugins_loaded', 'acf_wpcli_register_groups');
}