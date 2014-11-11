<?php
/**
 * Implement ACF command
 *
 * @package wp-cli
 * @subpackage commands/community
 * @maintainer Hoppinger (http://www.hoppinger.com)
 */

class ACF5_Command extends WP_CLI_Command {
  private $paths = array();

  function __construct() {
    $this->paths = array(
      'active_theme'        => get_template_directory() . '/field-groups/',
      'active_child_theme'  => get_stylesheet_directory() . '/field-groups/',
      'child_themes_shared' => ABSPATH . 'field-groups/shared-childs/',
    );

    $this->paths = apply_filters( 'acfwpcli_fieldgroup_paths', $this->paths );
  }

  /**
   * Example subcommand
   *
   * @param array   $args
   */
  function status( $args, $assoc_args ) {
    if ( is_multisite() ) {
      $blog_list = get_blog_list( 0, 'all' );
    }
    else {
      $blog_list   = array();
      $blog_list[] = array( 'blog_id' => 1 );
    }

    foreach ( $blog_list as $blog ) :

      if ( is_multisite() ) switch_to_blog( $blog['blog_id'] ) ;

      $field_groups = get_posts(
        array(
          'numberposts' =>  -1,
          'post_type'   =>  'acf-field-group',
          'sort_column' => 'menu_order',
          'order'       => 'ASC',
        )
      );

    WP_CLI::line( ' ' );
    WP_CLI::line( count( $field_groups ) . ' field groups found for blog_id ' . $blog['blog_id'] );

    if ( ! empty( $field_groups ) ) {
      foreach ( $field_groups as $group ) WP_CLI::line( '- ' . sanitize_title( $group->post_title ) );
    }

    WP_CLI::line( ' ' );

    if ( is_multisite() ) restore_current_blog();

    endforeach;

  }

  function export( $args, $assoc_args ) {

    // if empty it will show export all fields
    $export_field = '';

    if ( is_multisite( ) ) {
      $choice = $this->select_blog();
      switch_to_blog( $choice );
    }

    //if export all is used skip the question popup
    if ( empty( $args ) || ( $args[0] != 'all' ) ) {
      $export_field = $this->select_acf_field();
    }

    if ( empty( $export_field ) ) {
      WP_CLI::success( "Exporting all fieldgroups \n" );
    } else {
      WP_CLI::success( "Exporting fieldgroup \n" );
    }

    $field_groups = get_posts( array(
        'numberposts' =>  -1,
        'post_type'   =>  'acf-field-group',
        'sort_column' => 'menu_order',
        'order'       => 'ASC',
        'include'     => $export_field,
      ) );

    if ( $field_groups ) {

      $export_path = $this->select_export_path();

      $acf_fld_grp = new acf_field();

      if ( ! is_dir( $export_path ) && !mkdir( $export_path, 0755, false ) ) {
        WP_CLI::line( 'fieldgroup directory exists or cant be created!' );
      }

      foreach ( $field_groups as $group ) :
        $title            = get_the_title( $group->ID );
      $sanitized_title  = sanitize_title( $title );
      $subpath          = $export_path . $sanitized_title;
      $uniquid_path     = $subpath .'/uniqid';
      $field_group_array = array();

      $field_group = acf_get_field_group( $group->ID ) ;


      // validate field group
      if ( empty( $field_group ) ) {

        continue;

      }

      // load fields
      $fields = acf_get_fields( $field_group );


      // prepare fields
      $fields = acf_prepare_fields_for_export( $fields );


      // add to field group
      $field_group['fields'] = $fields;


      // extract field group ID
      $id = acf_extract_var( $field_group, 'ID' );


      $json = acf_json_encode( $field_group );


      // retrieve the uniquid from the file if it exists else we make a new one
      $uniqid = ( file_exists( $uniquid_path ) ) ? file_get_contents( $uniquid_path ) : uniqid();


      // each field_group gets it's own folder by field_group name
      if ( ! is_dir( $subpath ) && !mkdir( $subpath, 0755, false ) ) {
        WP_CLI::line( 'fieldgroup subdirectory exists or cant be created!' );
      }else {

        // let's write the array to a data.php file so it can be used later on
        $fp     = fopen( $subpath . '/' ."data.php", "w" );
        $output = "<?php \n\$group = " . var_export( $field_group , true ) . ';';
        fwrite( $fp, $output );
        fclose( $fp );


        $fp     = fopen( $subpath . '/' ."data.json", "w" );
        $output = $json;
        fwrite( $fp, $output );
        fclose( $fp );

        // write the uniquid file if it doesn't exist
        if ( ! file_exists( $uniquid_path ) ) :
          $fp     = fopen( $subpath . '/' ."uniqid", "w" );
        $output = $uniqid;
        fwrite( $fp, $output );
        fclose( $fp );
        endif;
        WP_CLI::success( "Fieldgroup ".$title." exported " );
      }

      endforeach;
    }
    else {
      //error seems to be returning and break out of my loop
      //WP_CLI::error( 'No field groups were found in the database' );
      echo 'No field groups were found in the database';
      echo ' ';
    }
    if ( is_multisite() ) restore_current_blog();

  }

  function clean( $args = array() ) {
    if ( is_multisite() ) {
      $blog_list = wp_get_sites();
    } else {
      $blog_list   = array();
      $blog_list[] = array( 'blog_id' => 1 );
    }

    foreach ( $blog_list as $blog ) :
      if ( is_multisite() ) switch_to_blog( $blog['blog_id'] );

      $field_groups = get_posts( array(
          'numberposts' =>  -1,
          'post_type'   =>  array('acf-field-group', 'acf', 'acf-field'),
          'sort_column' => 'menu_order',
          'order'       => 'ASC',
        ) );

    foreach ( $field_groups as $group ) :
      global $wpdb;
    $wpdb->query( "DELETE FROM $wpdb->postmeta WHERE post_id = $group->ID" );
    $wpdb->query( "DELETE FROM $wpdb->posts WHERE ID = $group->ID" );
    endforeach;

    if ( is_multisite() ) restore_current_blog();
    endforeach;
    WP_CLI::success( 'cleaned up everything ACF related in the database' );
  }


  function import( $args, $assoc_args ) {
    if ( is_multisite() ) {

      $choice           = $this->select_blog();
      switch_to_blog( $choice );

 if ( ! isset( $args[0] ) ) {

        $choices = array();
        $choices['all'] = 'all';

        foreach ( $this->paths as $path ) {

          if ( ! file_exists( $path ) ) continue;

          if ( $dir = opendir( $path ) ) {
            while ( false !== ( $folder = readdir( $dir ) ) ) {
              if ( $folder != '.' && $folder != '..' ) {
                $key = trailingslashit( $path . $folder );
                $choices[ $key ] = $folder;
              }
            }
          }
        }

        while ( true ) {
          $choice = \cli\menu( $choices, null, __( 'Choose a fieldgroup to import', 'acf-wpcli' ) );
          \cli\line();

          break;
        }
      }

      $patterns = array();
      if ( $choice == 'all' ) {
        foreach ( $this->paths as $key => $value )
          $patterns[ $key ] = trailingslashit( $value ) . '*/data.json';
      } else {
        $patterns[] = $choice . 'data.json';
      }

      foreach ( $patterns as $pattern ) {
        $i = 0;
        //echo $pattern."\n";
        foreach ( glob( $pattern ) as $file ) {
          //Start acf 5 import
          // read file
          $json = file_get_contents( $file );


          // decode json
          $json = json_decode( $json, true );

          // if importing an auto-json, wrap field group in array
          if ( isset( $json['key'] ) ) {
            $json = array( $json );
          }

          // vars
          $ref      = array();
          $order    = array();

          foreach ( $json as $field_group ) :

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
          foreach ( $fields as $field ) :

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

          endforeach;

          WP_CLI::success( 'imported the data.json for field_group ' . $field_group['title'] .'" into the dabatase!' );
          endforeach;

        }
        $i++;
        if ($i===1) break;
      }




    } else {

      if ( ! isset( $args[0] ) ) {

        $choices = array();
        $choices['all'] = 'all';

        foreach ( $this->paths as $path ) {

          if ( ! file_exists( $path ) ) continue;

          if ( $dir = opendir( $path ) ) {
            while ( false !== ( $folder = readdir( $dir ) ) ) {
              if ( $folder != '.' && $folder != '..' ) {
                $key = trailingslashit( $path . $folder );
                $choices[ $key ] = $folder;
              }

            }
          }
        }
        while ( true ) {
          $choice = \cli\menu( $choices, null, 'Pick a fieldgroup to import' );
          \cli\line();

          break;
        }
      }

      $patterns = array();
      if ( $choice == 'all' ) {
        foreach ( $this->paths as $key => $value )
          $patterns[ $key ] = trailingslashit( $value ) . '*/data.json';
      } else {
        $patterns[] = $choice . 'data.json';
      }

      foreach ( $patterns as $pattern ) {
        foreach ( glob( $pattern ) as $file ) {
          //Start acf 5 import
          // read file
          $json = file_get_contents( $file );


          // decode json
          $json = json_decode( $json, true );

          // if importing an auto-json, wrap field group in array
          if ( isset( $json['key'] ) ) {

            $json = array( $json );

          }

          // vars
          $ref      = array();
          $order    = array();

          foreach ( $json as $field_group ) :

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
          foreach ( $fields as $field ) :

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

          endforeach;

          WP_CLI::success( 'imported the data.json for field_group ' . $field_group['title'] .'" into the dabatase!' );
          endforeach;

        }
      }
    }
  }

  static function help() {
    WP_CLI::line( 'Welcome to advanced-custom-field-wpcli' );
    WP_CLI::line( 'This tool and plugin builds a bridge between WP-CLI and the Advanced Custom Fields' );
    WP_CLI::line( 'possible subcommands: status, export, clean, import' );
  }

  protected function select_acf_xml() {
    $this->paths = apply_filters( 'acfwpcli_fieldgroup_paths', $this->paths );
    $patterns = array();
    $choices  = array();

    foreach ( $this->paths as $key => $value ) {
      $patterns[ $key ] = trailingslashit( $value ) . '*/data.json';
    }

    $choices[''] = 'all';
    foreach ( $patterns as $path ) {
      foreach ( glob( $path ) as $file ) {
        $choices[$file] = $file;
      }
    }

    while ( true ) {
      $choice = \cli\menu( $choices, null, __( 'Choose a fieldgroup to import', 'acf-wpcli' ) );
      \cli\line();

      return $choice;
      break;
    }
  }

  protected function select_blog() {
    $sites = wp_get_sites();

    foreach ( $sites as $site ) {
      $blog = get_blog_details( $site['blog_id'] );

      $choices[ $site['blog_id'] ] = $blog->blogname . ' - ' . $blog->domain . $blog->path;
    }

    return $this->choice( $choices, __( 'Choose a blog', 'acf-wpcli' ) );
  }

  protected function select_acf_field() {
    $field_groups = get_posts( array(
        'numberposts' =>  -1,
        'post_type'   =>  'acf-field-group',
        'sort_column' => 'menu_order',
        'order'       => 'ASC',
      ) );

    $choices = array( '' => 'all' );

    foreach ( $field_groups as $group ) {
      $choices[$group->ID] = $group->post_title;
    }

    return $this->choice( $choices, __( 'Choose a fieldgroup to export', 'acf-wpcli' ) );
  }

  protected function select_export_path() {
    $choices  = array();

    foreach ( $this->paths as $key => $value ) {
      $choices[ $value ] = $key . ': ' . $value;
    }

    return $this->choice( $choices, __( 'Choose a path to export the fieldgroup to', 'acf-wpcli' ) );
  }

  private function choice( $choices, $question = false ) {
    if ( ! $question ) {
      $question = __( 'Choose something', 'acf-wpcli' );
    }

    while ( true ) {
      $choice = \cli\menu( $choices, null, $question );
      \cli\line();

      break;
    }

    return $choice;
  }

}
