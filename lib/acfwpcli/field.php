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
    return get_posts([
        'numberposts' => -1,
        'post_type'   => array( 'acf', 'acf-field' ),
        'sort_column' => 'menu_order',
        'order'       => 'ASC',
    ]);
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
