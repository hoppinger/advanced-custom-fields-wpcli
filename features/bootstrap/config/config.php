<?php

$config = [];
$config['wordpress_path'] = str_replace('features/bootstrap/config', '', dirname(__FILE__)).'wordpress/';
$config['wp-cli_path'] = $config['wordpress_path'].'wp-cli.phar';

return $config;
