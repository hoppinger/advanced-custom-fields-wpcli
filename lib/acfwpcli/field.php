<?php

namespace ACFWPCLI;

class Field {

  public static function import( $field, $field_group ) {
    $order = [];

     // add parent
    if ( empty( $field['parent'] ) ) {
      $field['parent'] = $field_group['ID'];
    } elseif ( isset( $ref[ $field['parent'] ] ) ) {
      $field['parent'] = $ref[ $field['parent'] ];
    }

    // add field menu_order
    if ( ! isset( $order[ $field['parent'] ] ) ) {
      $order[ $field['parent'] ] = 0;
    }

    $field['menu_order'] = $order[ $field['parent'] ];
    $order[ $field['parent'] ]++;

    // save field
    $field = acf_update_field( $field );

    // add to ref
    $ref[ $field['key'] ] = $field['ID'];
  }

  public static function all() {
    return get_posts(array(
			'posts_per_page'			=> -1,
			'post_type'					=> 'acf-field',
			'orderby'					=> 'menu_order',
			'order'						=> 'ASC',
			'suppress_filters'			=> true, // DO NOT allow WPML to modify the query
			'post_status'				=> 'publish, trash', // 'any' won't get trashed fields
			'update_post_meta_cache'	=> false,
			'update_post_term_cache'	=> false
    ));
  }

  public static function destroy( $id ) {
    global $wpdb;

    $query = "DELETE FROM {$wpdb->postmeta} WHERE post_id = %d";
    $prepared = $wpdb->prepare( $query, $id );
    $wpdb->query( $prepared );

    $query = "DELETE FROM {$wpdb->posts} WHERE ID = %d";
    $prepared = $wpdb->prepare( $query, $id );
    $wpdb->query( $prepared );
  }
}
