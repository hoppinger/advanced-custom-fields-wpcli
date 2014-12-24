<?php
/*
Plugin Name: Advanced Custom Fields WP-CLI
Plugin URI: https://github.com/hoppinger/advanced-custom-fields-wpcli
Description: Manage your ACF field groups in WP-CLI
Version: 2.0.0
Author: Hoppinger
Author URI: http://www.hoppinger.com/
License: MIT
https://github.com/hoppinger/advanced-custom-fields-wpcli/blob/master/LICENCE.txt
*/

define( 'ACF_WPCLI_ROOT', __DIR__ );
define( 'ACF_WPCLI_URL', plugin_dir_url( __FILE__ ) );

require 'vendor/autoload.php';

// instantiate loader and register namespaces
$loader = new \Aura\Autoload\Loader;
$loader->register();
$loader->addPrefix( 'ACFWPCLI', __DIR__ . '/src/' );

// instantiate this plugin
new \ACFWPCLI\Plugin;
