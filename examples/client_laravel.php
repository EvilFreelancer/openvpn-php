<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Config og client object
$config = \OpenVPN::getClient([
    'dev'              => 'tun',
    'proto'            => 'tcp',
    'resolvRetry'      => 'infinite',
    'cipher'           => 'AES-256-CB',
    'redirect-gateway' => true,
    'key-direction'    => 1,
    'remote-cert-tls'  => 'server',
    'auth-user-pass'   => true,
    'auth-nocache'     => true,
    'nobind'           => true,
    'persist-key'      => true,
    'persist-tun'      => true,
    'comp-lzo'         => true,
    'verb'             => 3,
]);

// Another way for change values
$config->remote    = 'vpn.example.com 1194';
$config->httpProxy = 'proxy-http.example.com 3128';

// Set additional certificates of client
$config->setCerts([
    'ca'   => '/etc/openvpn/keys/ca.crt',
    'cert' => '/etc/openvpn/keys/issued/client1.crt',
    'key'  => '/etc/openvpn/keys/private/client1.key',
], true); // true mean embed certificates into config, false by default

// Generate config by options
echo $config->generate();
