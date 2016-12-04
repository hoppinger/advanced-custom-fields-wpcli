<?php

namespace ACFWPCLI;

use WP_CLI;

class FieldGroup {

  const FILENAME = 'data';
  const EXTENSION = '.json';

  public static function import( $file ) {
    $field_groups = self::from_json_file( $file );

    foreach ( $field_groups as $field_group ) {
      $fields = acf_extract_var( $field_group, 'fields' );
      $fields = acf_prepare_fields_for_import( $fields );

      $field_group = acf_update_field_group( $field_group );

      foreach ( $fields as $field ) {
        Field::import( $field, $field_group );
      }
    }

    return $field_groups;
  }

  public static function all() {
    return get_posts([
        'numberposts' => -1,
        'post_type'   => 'acf-field-group',
        'sort_column' => 'menu_order',
        'order'       => 'ASC',
    ]);
  }

  public static function find_by_name( $name ) {
    global $wpdb;
    $query = "SELECT * FROM {$wpdb->posts} WHERE post_type='acf-field-group' AND post_excerpt = %s";

    $results = $wpdb->get_results( $wpdb->prepare( $query, $name ) );

    return $results;
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

  public static function from_json_file( $file ) {
    if ( ! is_readable( $file ) || ! is_file( $file ) ) {
      return false; }

    $json = file_get_contents( $file );

    return json_decode( $json, true );
  }

  public static function to_json_file( $field_group, $file ) {
    if ( empty( $field_group ) || empty( $file ) ) {
      return false;
    }

    $content = acf_json_encode( $field_group );

    file_put_contents( $file, $content );
  }

  public static function to_array( $id ) {
    $field_group = acf_get_field_group( $id );

    // load fields
    $fields = acf_get_fields( $field_group );

    // prepare fields
    $fields = acf_prepare_fields_for_export( $fields );

    // extract field group ID
    acf_extract_var( $field_group, 'ID' );

    // add to field group
    $field_group['fields'] = $fields;

    return $field_group;
  }
}
