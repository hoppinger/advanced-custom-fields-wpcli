<?php

namespace ACFWPCLI;

class CLIUtils {
  public static function expand_tilde( $path ) {
    if ( function_exists( 'posix_getuid' ) && strpos( $path, '~' ) !== false ) {
      $info = posix_getpwuid( posix_getuid() );
      $path = str_replace( '~', $info['dir'], $path );
    }

    return $path;
  }
}
