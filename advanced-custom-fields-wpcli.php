<?php
/*
Plugin Name: Advanced Custom Fields WP-CLI
Plugin URI: https://github.com/hoppinger/advanced-custom-fields-wpcli
Description: Manage your ACF field groups in WP-CLI
Version: 3.0.0
Author: Hoppinger
Author URI: http://www.hoppinger.com/
License: MIT
https://github.com/hoppinger/advanced-custom-fields-wpcli/blob/master/LICENCE.txt
*/

require 'lib/acfwpcli.php';
require 'lib/acfwpcli/field.php';
require 'lib/acfwpcli/field_group.php';

if ( defined( 'WP_CLI' ) && WP_CLI ) {
  require 'lib/acfwpcli/cli.php';
  require 'lib/acfwpcli/cli_utils.php';
}

$acfwpcli = new ACFWPCLI;
