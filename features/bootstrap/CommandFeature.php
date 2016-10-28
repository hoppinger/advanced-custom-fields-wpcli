<?php

class CommandFeature
{
    protected $config = [];

    public function __construct()
    {
        $this->config = include 'config/config.php';
    }

    protected function run($command)
    {
        $cmd = "php {$this->config['wp-cli_path']} --path={$this->config['wordpress_path']} {$command}";

        $output_string = exec($cmd, $output, $exitCode);

        return ['exitCode' => $exitCode, 'output' => $output, 'output_string' => $output_string];
    }
}
