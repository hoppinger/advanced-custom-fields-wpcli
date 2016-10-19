<?php

class CommandFeature {
  protected function run($command) {
    exec( "php /private/tmp/wordpress/wp-cli.phar --path=/private/tmp/wordpress {$command}", $output, $exitCode );

    return ['exitCode' => $exitCode, 'output' => $output];
  }
}
