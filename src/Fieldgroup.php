<?php

namespace ACFWPCLI;

use WP_CLI;

class Fieldgroup {

  public static function import( $file ) {
    // read file
    $json = file_get_contents( $file );

    // decode json
    $json = json_decode( $json, true );

    // if importing an auto-json, wrap field group in array
    if ( isset( $json['key'] ) ) {
      $json = array( $json );
    }

    // vars
    $ref    = array();
    $order  = array();

    foreach ( $json as $field_group ) {
      // remove fields
      $fields = acf_extract_var( $field_group, 'fields' );

      // format fields
      $fields = acf_prepare_fields_for_import( $fields );

      // save field group
      $field_group = acf_update_field_group( $field_group );

      // add to ref
      $ref[ $field_group['key'] ] = $field_group['ID'];

      // add to order
      $order[ $field_group['ID'] ] = 0;

      // add fields
      foreach ( $fields as $field ) {
        Field::import( $field, $field_group );
      }

      WP_CLI::success( 'imported the data.json for field_group "' . $field_group['title'] .'" into the dabatase!' );
    }
  }

}
