<?php namespace ACFWPCLI;

use WP_CLI;

class Plugin {
  public $textdomain;

  public function __construct() {
    $this->filters();
    $this->actions();

    if ( defined( 'WP_CLI' ) && WP_CLI ) {
      //Since version 5 uses json instead of xml, we have a new ACF command for it.
      if ( substr( get_option( 'acf_version' ), 0, 1 ) == 5 ) {
        // Include and register the class as the 'example' command handler
        WP_CLI::add_command( 'acf', 'ACFWPCLI\Commands\ACF5_Command' );
      } else {
        // Include and register the class as the 'example' command handler
        include 'ACFCommand.php';
        WP_CLI::add_command( 'acf', 'ACFCommand' );
      }
    }
  }

  public function filters() {

  }

  public function actions() {

  }

}


