<?php

namespace ACFWPCLI;

class Field {

  public static function import( $field, $field_group ) {
    $order = array();

     // add parent
    if ( empty( $field['parent'] ) ) {
      $field['parent'] = $field_group['ID'];
    } elseif ( isset( $ref[ $field['parent'] ] ) ) {
      $field['parent'] = $ref[ $field['parent'] ];
    }

    // add field menu_order
    if ( !isset( $order[ $field['parent'] ] ) ) {
      $order[ $field['parent'] ] = 0;
    }

    $field['menu_order'] = $order[ $field['parent'] ];
    $order[ $field['parent'] ]++;

    // save field
    $field = acf_update_field( $field );

    // add to ref
    $ref[ $field['key'] ] = $field['ID'];
  }

}
