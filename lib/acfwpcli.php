<?php

class ACFWPCLI {

  private $added_groups = [];

  public function __construct() {
    $this->register_cli_command();
    $this->actions();
  }

  public function actions() {
    if ( ! defined( 'WP_CLI' ) ) {
      add_action( 'plugins_loaded', array( $this, 'add_runtime_fieldgroups' ) );
    }
  }

  public function add_runtime_fieldgroups() {
    global $blog_id;
    global $wpdb;

    $db_field_groups = ACFWPCLI\FieldGroup::all();

    $db_field_group_titles = [];
    foreach ( $db_field_groups as $db_group ) {
      $db_field_group_titles[] = $db_group->post_title;
    }

    $paths    = [];
    $paths    = apply_filters( 'acfwpcli_fieldgroup_paths', $paths );
    $patterns = [];

    foreach ( $paths as $key => $value ) {
      if ( ! is_dir( $value ) ) {
        continue;
      }
      $patterns[ $key ] = trailingslashit( $value ) . '*.json';
    }

    $added_groups = [];
    foreach ( $patterns as $pattern ) {
      // register the field groups specific for this subsite
      foreach ( glob( $pattern ) as $file ) {
        $groups = ACFWPCLI\FieldGroup::from_json_file( $file );

        foreach ( $groups as $group ) {
          // Don't register group when the group is already in the DB
          if ( ! in_array( $group['title'] , $db_field_group_titles ) ) {
            acf_add_local_field_group( $group );
          }

          $added_groups[] = $group['title'];
        }
      }
    }

    $this->added_groups = $added_groups;
  }

  private function register_cli_command() {
    if ( defined( 'WP_CLI' ) && WP_CLI ) {
      WP_CLI::add_command( 'acf', 'ACFWPCLI\CLI' );
    }
  }

  public function get_added_groups() {
    return $this->added_groups;
  }
}
