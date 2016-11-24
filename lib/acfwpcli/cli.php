<?php

namespace ACFWPCLI;

use WP_CLI;
use WP_CLI_Command;
use ACFWPCLI;
use ACFWPCLI\Fieldgroup;
use ACFWPCLI\JSON;

/**
 * Implement ACF command
 *
 * @package wp-cli
 * @subpackage commands/community
 * @maintainer Hoppinger (http://www.hoppinger.com)
 */

class CLI extends WP_CLI_Command {
  private $paths = [];

  function __construct() {
    $wpcli_config = WP_CLI::get_config();

    $this->paths = [];
    $this->paths = apply_filters( 'acfwpcli_fieldgroup_paths', $this->paths );

    if ( is_multisite( ) && ! isset( $wpcli_config['url'] ) ) {
      WP_CLI::error( 'Use the --url=<url> parameter in a multisite.' );
    }
  }

  static function help() {
    WP_CLI::line( 'Welcome to Advanced Custom Fields WPCLI' );
    WP_CLI::line( 'possible subcommands: status, export, import, clean' );
  }

  /**
  * Export ACF field groups to local files
  *
  * ## OPTIONS
  *
  * [--group=<group>]
  * : The field group to export, can be used with "My Field Group" or "my-field-group".
  *
  * [--export_path=<path>]
  * : The field groups directory path to export towards.
  *
  * [--all]
  * : Export all the fieldgroups.
  *
  * @subcommand export
  * @synopsis [--field_group=<field_group>] [--export_path=<export_path>] [--all]
  */
  function export( $args, $assoc_args ) {
    extract( $assoc_args );

    $field_groups = [];

    if ( isset( $field_group ) ) {
      $name = sanitize_title( $field_group );
      $field_groups = \ACFWPCLI\FieldGroup::find_by_name( $name );

      if ( empty( $field_groups ) ) {
        WP_CLI::error( 'No fieldgroups found that match this field group name' );
      }
    } else if ( isset( $all ) ) {
      $field_groups = \ACFWPCLI\FieldGroup::all();
    } else {
      $choice = $this->menu_choice_export_field_group();

      if ( $choice == 'all' ) {
        $field_groups = \ACFWPCLI\FieldGroup::all();
      } else {
        $field_groups = \ACFWPCLI\FieldGroup::find_by_name( $choice );
      }
    }

    if ( empty( $export_path ) ) {
      $export_path = $this->menu_choice_export_path();
    }

    $export_path = \ACFWPCLI\CLIUtils::expand_tilde( $export_path );

    if ( ! is_dir( $export_path ) && ! mkdir( $export_path, 0755, false ) ) {
      WP_CLI::error( 'fieldgroup directory exists or cant be created!' );
    }

    foreach ( $field_groups as $post ) {
      $field_group = \ACFWPCLI\FieldGroup::to_array( $post );
      $file = $export_path . sanitize_title( $post->post_title ) . '.json';

      \ACFWPCLI\FieldGroup::to_json_file( [$field_group], $file );
      WP_CLI::success( "Exported field group: {$post->post_title}" );
    }
  }

  /**
  * Remove everything ACF from the database
  *
  * ## OPTIONS
  *
  * [--network]
  * : Clean the fieldgroups in all the sites in the network
  *
  * @subcommand clean
  *
  */
  function clean( $args, $assoc_args ) {
    extract( $assoc_args );

    $field_groups = \ACFWPCLI\FieldGroup::all();
    $fields       = \ACFWPCLI\Field::all();

    if ( empty( $field_groups[0] ) && empty( $fields ) ) {
      WP_CLI::warning( 'No field groups or fields found to clean up.' );
    }

    foreach ( $fields as $field ) {
      \ACFWPCLI\Field::destroy( $field->ID );
    }

    foreach ( $field_groups as $field_group ) {
      \ACFWPCLI\FieldGroup::destroy( $field_group->ID );
      WP_CLI::success( "Removed field group: {$field_group->post_title}" );
    }
  }

  /**
  * Import ACF field groups from local files to database
  *
  * ## OPTIONS
  *
  * [--json_file=<json_file>]
  * : The path to the json file.
  *
  * [--all]
  * : Import all the fieldgroups
  *
  * @subcommand import
  *
  */
  function import( $args, $assoc_args ) {
    extract( $assoc_args );

    if ( isset( $json_file ) ) {
      $choice = \ACFWPCLI\CLIUtils::expand_tilde( $json_file );
    } else {
      $choice = $this->menu_choice_import_field_group();
    }

    $patterns = [];
    if ( $choice == 'all' ) {
      foreach ( $this->paths as $key => $value ) {
        $patterns[ $key ] = trailingslashit( $value ) . '*.json'; }
    } else {
      $patterns[] = $choice;
    }

    foreach ( $patterns as $pattern ) {
      foreach ( glob( $pattern ) as $file ) {
        $field_groups = \ACFWPCLI\FieldGroup::import( $file );

        foreach ( $field_groups as $field_group ) {
          WP_CLI::success( "Imported field group: {$field_group['title']}" );
        }
      }
    }
  }

  private function menu_choice_export_field_group() {
    $field_groups = \ACFWPCLI\FieldGroup::all();

    if ( empty( $field_groups ) ) {
      WP_CLI::error( 'No field groups found to export' );
    }

    $choices = [ 'all' => 'all' ];

    foreach ( $field_groups as $group ) {
      $choices[ $group->post_excerpt ] = $group->post_title;
    }

    return $this->menu_choice( $choices, 'Choose a field group to export' );
  }

  protected function menu_choice_export_path() {
    if ( count( $this->paths ) == 1 ) {
      return array_shift( $this->paths );
    }

    $choices  = [];

    foreach ( $this->paths as $key => $value ) {
      $choices[ $value ] = $key . ': ' . $value;
    }

    return $this->menu_choice( $choices, 'Choose a path to export the field group to' );
  }

  private function menu_choice_import_field_group() {
    $choices = [];
    $choices['all'] = 'all';

    $patterns = [];

    foreach ( $this->paths as $path ) {
      $patterns[] = trailingslashit( $path ) . '*.json';
    }

    foreach ( $patterns as $pattern ) {
      foreach ( glob( $pattern ) as $file ) {
        $field_group = pathinfo( $file, PATHINFO_FILENAME );
        $choices[ $file ] = $field_group;
      }
    }

    return $this->menu_choice( $choices, 'Choose a field group to import' );
  }

  private function menu_choice( $choices, $question = 'Choose something' ) {
    while ( true ) {
      $choice = \cli\menu( $choices, null, $question );
      \cli\line();

      break;
    }

    return $choice;
  }
}
