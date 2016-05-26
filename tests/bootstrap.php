<?php

require __DIR__ . '/../lib/aura/autoload/Loader.php';

if ( ! $tests_dir = getenv( 'WP_TESTS_DIR' ) ) {
  $tests_dir = '/tmp/wordpress-tests-lib';
}

require_once $tests_dir . '/includes/functions.php';

function _manually_load_plugin() {
  require dirname( __FILE__ ) . '/../advanced-custom-fields-wpcli.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require $tests_dir . '/includes/bootstrap.php';
