<?php
/**
 * Implement ACF command
 *
 * @package wp-cli
 * @subpackage commands/community
 * @maintainer Hoppinger (http://www.hoppinger.com)
 */

class ACFCommand extends WP_CLI_Command {
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

      $field_groups = get_posts( array(
          'numberposts' =>  -1,
          'post_type'   =>  'acf',
          'sort_column' => 'menu_order',
          'order'       => 'ASC',
        ) );

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
    include 'bin/helpers.php';

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
      WP_CLI::success( "Exporting fieldgroup: ".$choices[$choice]." \n" );
    }

    $field_groups = get_posts( array(
        'numberposts' =>  -1,
        'post_type'   =>  'acf',
        'sort_column' => 'menu_order',
        'order'       => 'ASC',
        'include'     => $export_field,
      ) );

    if ( $field_groups ) {

      if ( substr( get_option( 'acf_version' ), 0, 1 ) > 3 ) {
        $acf_fld_grp = new acf_field_group();
      }else {
        $acf         = new Acf();
      }


      $path        = get_stylesheet_directory() . '/field-groups/';

      if ( ! is_dir( $path ) && !mkdir( $path, 0755, false ) ) {
        WP_CLI::line( 'fieldgroup directory exists or cant be created!' );
      }


      foreach ( $field_groups as $group ) :
        $title            = get_the_title( $group->ID );
      $sanitized_title  = sanitize_title( $title );
      $subpath          = $path . $sanitized_title;
      $uniquid_path     = $subpath .'/uniqid';

      // retrieve the uniquid from the file if it exists else we make a new one
      $uniqid = ( file_exists( $uniquid_path ) ) ? file_get_contents( $uniquid_path ) : uniqid();
      if ( substr( get_option( 'acf_version' ), 0, 1 ) > 3 ) {
        $field_group_array = array(
          'id'         => $uniqid,
          'title'      => $title,
          'fields'     => $acf_fld_grp->get_fields( array(), $group->ID ),
          'location'   => $acf_fld_grp->get_location( array(), $group->ID ),
          'options'    => $acf_fld_grp->get_options( array(), $group->ID ),
          'menu_order' => $group->menu_order,
        );
      }else {
        $field_group_array = array(
          'id'         => $uniqid,
          'title'      => $title,
          'fields'     => $acf->get_acf_fields( $group->ID ),
          'location'   => $acf->get_acf_location( $group->ID ),
          'options'    => $acf->get_acf_options( $group->ID ),
          'menu_order' => $group->menu_order,
        );
      }
      // each field_group gets it's own folder by field_group name
      if ( ! is_dir( $subpath ) && !mkdir( $subpath, 0755, false ) ) {
        WP_CLI::line( 'fieldgroup subdirectory exists or cant be created!' );
      }else {

        // let's write the array to a data.php file so it can be used later on
        $fp     = fopen( $subpath . '/' ."data.php", "w" );
        $output = "<?php \n\$group = " . var_export( $field_group_array , true ) . ';';
        fwrite( $fp, $output );
        fclose( $fp );

        // write the xml
        include 'bin/xml_export.php';

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
    WP_CLI::success( 'cleanup dabatase!' );

    if ( is_multisite() ) {
      $blog_list = get_blog_list( 0, 'all' );
    } else {
      $blog_list   = array();
      $blog_list[] = array( 'blog_id' => 1 );
    }

    foreach ( $blog_list as $blog ) :
      if ( is_multisite() ) switch_to_blog( $blog['blog_id'] );

      $field_groups = get_posts( array(
          'numberposts' =>  -1,
          'post_type'   =>  'acf',
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
  }


  function import( $args, $assoc_args ) {
    include 'bin/parser.php';
    include 'bin/wp-importer.php';
    include 'bin/wp_import.php';


    if ( is_multisite() ) {

      $choice           = $this->select_blog();
      switch_to_blog( $choice );

      $field_group_name = $this->select_acf_xml();
      $path             = get_stylesheet_directory() . '/field-groups/*/data.xml';
      $importer         = new WP_Import();

      if ( $field_group_name == '' ) {

        foreach ( glob( $path ) as $file ) :

          $importer->import( $file );
        endforeach;
        WP_CLI::success( 'imported all the data.xml field_groups to the dabatase!' );

      } else {

        $importer->import( $field_group_name );
        WP_CLI::success( 'imported the data.xml for blog_id ' . '$blog_id' . ' " and field_group ' . $field_group_name .'" into to the dabatase!' );
      }

    } else {

      if ( ! isset( $args[0] ) ) {
        $choices = array();
        $choices['all'] = 'all';

        if ( $dir = opendir( get_stylesheet_directory() . '/field-groups' ) ) {
          /* This is the correct way to loop over the directory. */
          while ( false !== ( $folder = readdir( $dir ) ) ) {
            //echo "$folder";
            if ( $folder != '.' && $folder != '..' ) {
              $choices[$folder] = $folder;
            }

          }
        }
        while ( true ) {
          $choice = \cli\menu( $choices, null, 'Pick a fieldgroup to import' );
          \cli\line();

          $args[0] = $choice;
          break;
        }
      }

      // This is a single site so we require only 1 argument
      if ( isset( $args[0] ) ) {
        $field_group_name = $args[0];  // set new var with a decent name that makes sense farther down the line (let's keep our sanity intact)

        if ( $field_group_name == 'all' ) {
          $path_pattern = get_stylesheet_directory()  . '/field-groups/*/data.xml';
        } else {
          $path_pattern = get_stylesheet_directory()  . '/field-groups/' . $field_group_name . '/data.xml';
        }

        foreach ( glob( $path_pattern ) as $file ) :
          $importer = new WP_Import();
        $importer->import( $file );
        WP_CLI::success( 'imported the data.xml for field_group ' . $field_group_name .'" into the dabatase!' );
        endforeach;

      } else {
        WP_CLI::error( 'You need to provide 1 argument: "field-group-name"
Example: wp acf impport field-group-name' );
      }
    }
  }

  static function help() {
    WP_CLI::line( 'Welcome to advanced-custom-field-wpcli' );
    WP_CLI::line( 'This tool and plugin builds a bridge between WP-CLI and the Advanced Custom Fields' );
    WP_CLI::line( 'possible subcommands: status, export, clean, import' );
  }

  protected function select_acf_xml() {
    $path        = get_stylesheet_directory() . '/field-groups/*/data.xml';
    $choices     = array();
    $choices[''] = 'all';
    foreach ( glob( $path ) as $file ) {
      $choices[$file] = $file;
    }

    while ( true ) {
      $choice = \cli\menu( $choices, null, 'Pick a fieldgroup to import' );
      \cli\line();

      return $choice;
      break;
    }
  }

  protected function select_blog() {
    for ( $i = 1; $i <= get_blog_count()+1; $i++ ) {
      switch_to_blog( $i );
      $choices[$i] = get_blog_details( $i )->blogname . ' - ' .get_template() ;
    }

    while ( true ) {
      $choice = \cli\menu( $choices, null, 'Pick a blog' );
      \cli\line();

      return $choice;
      break;
    }
  }

  protected function select_acf_field() {
    $field_groups = get_posts( array(
        'numberposts' =>  -1,
        'post_type'   =>  'acf',
        'sort_column' => 'menu_order',
        'order'       => 'ASC',
      ) );

    $choices     = array();
    $choices[''] = 'all';
    foreach ( $field_groups as $group ) {
      $choices[$group->ID] = $group->post_title;
    }

    while ( true ) {
      $choice = \cli\menu( $choices, null, 'Pick a fieldgroup to export' );
      \cli\line();

      return $choice;
      break;
    }
  }

}
