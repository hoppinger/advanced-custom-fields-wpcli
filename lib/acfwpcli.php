<?php

class ACFWPCLI {

  private $added_groups = [];

  public function __construct() {
    $this->register_cli_command();
    $this->actions();
  }

  public function actions() {
    if ( ! defined( 'WP_CLI' ) ) {
      add_action( 'acf/init', array( $this, 'add_runtime_fieldgroups' ) );
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

    foreach ( $paths as $path ) {
      if ( ! is_dir( $path ) ) {
        continue;
      }
      $patterns[] = trailingslashit( $path ) . '*.json';
    }

    $added_groups = [];

    $files = array_reduce($patterns, function($merged, $pattern){ return array_merge($merged, glob( $pattern )); }, []);

    $sha = sha1(implode($files));
    $transient_key = "sk_acf-groups_$sha";
    
    $groups = get_transient($transient_key); # Use cached groups when available
    if (empty($groups)) {
      $groups = array_reduce($files, function($merged, $file){ return array_merge($merged, ACFWPCLI\FieldGroup::from_json_file($file)); }, []);
      set_transient($transient_key, $groups, 24 * HOUR_IN_SECONDS);
    }

    foreach ($groups as $group) {
      // Don't register group when the group is already in the DB
      if (!in_array($group['title'], $db_field_group_titles)) {
        acf_add_local_field_group($group);
      }

      $added_groups[] = $group['title'];
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
