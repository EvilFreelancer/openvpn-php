<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Import OpenVPN config file
$import = new \OpenVPN\Import('server.conf');
print_r($import);

// Parse configuration
$config = $import->parse();
print_r($config);

// Generate config by options
echo $config->generate();
