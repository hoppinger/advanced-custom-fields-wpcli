<?php

class CommandFeature {
  protected function run($command) {
    $cmd = "php /private/tmp/wordpress/wp-cli.phar --path=/private/tmp/wordpress {$command}";

    $output_string = exec( $cmd, $output, $exitCode );

    return ['exitCode' => $exitCode, 'output' => $output, 'output_string' => $output_string];
  }
}
