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

/**
 * Require the autoloader if this plugin is being used without composer
 *
 * @since 2.0.0
 */
if ( ! class_exists( 'Aura\Autoload\Loader' ) ) {
  require 'lib/aura/autoload/Loader.php';
}

/**
 * Make instance of Autoloader
 * Set the namespace
 *
 * @since 2.0.0
 */
$loader = new \Aura\Autoload\Loader;
$loader->register();
$loader->addPrefix( 'ACFWPCLI', __DIR__ . '/src/' );

/**
 * Make instance of plugin
 *
 * @since 2.0.0
 */
new \ACFWPCLI\Plugin;
