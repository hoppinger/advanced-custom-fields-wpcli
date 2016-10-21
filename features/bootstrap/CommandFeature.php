<?php

class CommandFeature {

  protected $config = [];

  public function __construct() {
    $this->config = include 'config/config.php';
  }

  protected function run($command) {
    //$cmd = "php /private/tmp/wordpress/wp-cli.phar --path=/private/tmp/wordpress {$command}";

    $cmd = "php {$this->config['wp-cli_path']} --path={$this->config['wordpress_path']} {$command}";

    $output_string = exec( $cmd, $output, $exitCode );

    return ['exitCode' => $exitCode, 'output' => $output, 'output_string' => $output_string];
  }

  protected function run_no_wp() {
    $cmd = "echo $(pwd)";

    $output_string = exec( $cmd, $output, $exitCode );

    var_dump(['exitCode' => $exitCode, 'output' => $output, 'output_string' => $output_string]);
  }
}
