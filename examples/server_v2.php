<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Config object
$config = new OpenVPN\Config();

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

// Set routes which will be used by server
$config->setRoutes([
    '10.1.1.0 255.255.255.0',
    '10.1.2.0 255.255.255.0',
    '10.1.3.0 255.255.255.0',
]);

// Set additional certificates of server
$config->setCerts([
    'ca'       => '/etc/openvpn/ca.crt',
    'cert'     => '/etc/openvpn/server.crt',
    'key'      => '/etc/openvpn/server.key',
    'dh'       => '/etc/openvpn/dh4096.crt',
    'tls-auth' => '/etc/openvpn/ta.key 0',
]);

// Set pushes which will be passed to client
$config->setPushes([
    'redirect-gateway def1',
    'dhcp-option DNS 8.8.8.8',
    'dhcp-option DNS 8.8.4.4',
]);

// Generate config by options
echo $config->generate();
