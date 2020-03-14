<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Config object
$config = new OpenVPN\Config();

// Set client options
$config->client();
$config->dev             = 'tun';
$config->remote          = 'vpn.example.com 1194';
$config->proto           = 'tcp';
$config->resolvRetry     = 'infinite';
$config->cipher          = 'AES-256-CB';
$config->redirectGateway = true;
$config->keyDirection    = 1;
$config->remoteCertTls   = 'server';
$config->authUserPass    = true;
$config->authNocache     = true;
$config->nobind          = true;
$config->persistKey      = true;
$config->persistTun      = true;
$config->compLzo         = true;
$config->verb            = 3;
$config->httpProxy       = 'proxy-http.example.com 3128';

// Set additional certificates of client
$config->setCerts([
    'ca'       => '/etc/openvpn/ca.crt',
    'tls-auth' => '/etc/openvpn/ta.key 0',
], true);

// Generate config by options
echo $config->generate();
