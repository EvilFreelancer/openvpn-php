<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Config object
$config = new OpenVPN\Config();

// Set client options
$config
    ->add('client')
    ->add('dev', 'tun')
    ->add('remote', 'vpn.example.com 1194')
    ->add('proto', 'tcp')
    ->add('resolv-retry', 'infinite')
    ->add('cipher', 'AES-256-CBC')
    ->add('redirect-gateway', true)
    ->add('ca', '/etc/openvpn/ca.crt')
    ->add('tls-auth', '/etc/openvpn/ta.key 0')
    ->add('key-direction', 1)
    ->add('remote-cert-tls', 'server')
    ->add('auth-user-pass', true)
    ->add('auth-nocache', true)
    ->add('nobind', true)
    ->add('persist-key', true)
    ->add('persist-tun', true)
    ->add('comp-lzo', true)
    ->add('verb', 3)
    ->add('http-proxy', 'proxy-http.example.com 3128');

// Generate config by options
echo $config->generate();
