<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Config object
$config = new \OpenVPN\Config();

// Set server options
$config->dev                  = 'tun';
$config->proto                = 'tcp';
$config->local                = 'vpn.example.com';
$config->port                 = 1194;
$config->resolvRetry          = 'infinite';
$config->cipher               = 'AES-256-CBC';
$config->redirectGateway      = true;
$config->server               = '10.8.0.0 255.255.255.0';
$config->keepalive            = '10 120';
$config->renegSec             = 18000;
$config->user                 = 'nobody';
$config->group                = 'nogroup';
$config->persistKey           = true;
$config->persistTun           = true;
$config->compLzo              = true;
$config->verb                 = 3;
$config->mute                 = 20;
$config->status               = '/var/log/openvpn/status.log';
$config->logAppend            = '/var/log/openvpn/openvpn.log';
$config->clientConfigDir      = 'ccd';
$config->scriptSecurity       = 3;
$config->usernameAsCommonName = true;
$config->verifyClientCert     = 'none';
$config->authUserPassVerify   = 'your_script.sh via-file';
$config->duplicateCn          = true;

// Set routes which will be used by server after starting
$config->setRoutes([
    '10.1.1.0 255.255.255.0',
    '10.1.2.0 255.255.255.0',
    '10.1.3.0 255.255.255.0',
    '10.1.4.0 255.255.255.0',
    '10.1.5.0 255.255.255.0',
]);

// Set additional certificates of server
$config->setCerts([
    'ca'   => '/etc/openvpn/keys/ca.crt',
    'cert' => '/etc/openvpn/keys/issued/server.crt',
]); // You can embed certificates into config by adding true as second parameter of setCerts method

// Another way for adding certificates
$config
    ->setCert('key', '/etc/openvpn/keys/private/server.key')
    ->setCert('dh', '/etc/openvpn/keys/dh.pem');

// Set pushes which will be passed to client
$config->setPushes([
    // Additional routes, which clients will see
    'route 10.1.2.0 255.255.255.0',
    'route 10.1.3.0 255.255.255.0',
    'route 10.1.4.0 255.255.255.0',

    // Replace default gateway, all client's traffic will be routed via VPN
    'redirect-gateway def1',

    // Prepend additional DNS addresses
    'dhcp-option DNS 8.8.8.8',
    'dhcp-option DNS 8.8.4.4',
]);

// Generate config by options
echo $config->generate('json');
