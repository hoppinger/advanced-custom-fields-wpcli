<?php
defined( 'ABSPATH' ) || exit;

function hoppinger_acfwpcli_fieldgroup_paths( $paths ) {
  $paths['tests'] = ABSPATH . '/field-groups/';

  return $paths;
}

add_filter( 'acfwpcli_fieldgroup_paths', 'hoppinger_acfwpcli_fieldgroup_paths' );
