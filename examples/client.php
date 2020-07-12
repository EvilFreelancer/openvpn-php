<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Config object
$config = new \OpenVPN\Config();

// Set client options
$config
    ->client()
    ->set('dev', 'tun')
    ->set('remote', 'vpn.example.com 1194')
    ->set('proto', 'tcp')
    ->set('resolv-retry', 'infinite')
    ->set('cipher', 'AES-256-CBC')
    ->set('redirect-gateway', true)
    ->set('ca', '/etc/openvpn/ca.crt')
    ->set('tls-auth', '/etc/openvpn/ta.key 0')
    ->set('key-direction', 1)
    ->set('remote-cert-tls', 'server')
    ->set('auth-user-pass', true)
    ->set('auth-nocache', true)
    ->set('nobind', true)
    ->set('persist-key', true)
    ->set('persist-tun', true)
    ->set('comp-lzo', true)
    ->set('verb', 3)
    ->set('http-proxy', 'proxy-http.example.com 3128');

// Generate config by options
echo $config->generate();
