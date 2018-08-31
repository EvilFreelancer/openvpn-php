<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Config object
$config = new OpenVPN\Config();

// Set server options
$config
    ->add('dev', 'tun')
    ->add('proto', 'tcp')
    ->add('port', '1194')
    ->add('resolvRetry', 'infinite')
    ->add('cipher', 'AES-256-CBC')
    ->add('redirectGateway', 'true')
    ->add('ca', '/etc/openvpn/ca.crt')
    ->add('cert', '/etc/openvpn/server.crt')
    ->add('key', '/etc/openvpn/server.key')
    ->add('dh', '/etc/openvpn/dh')
    ->add('tls-auth', '/etc/openvpn/ta.key 0')
    ->add('server', '10.8.0.0 255.255.255.0')
    ->add('push', 'redirect-gateway def1')
    ->add('push', 'dhcp-option DNS 8.8.8.8')
    ->add('push', 'dhcp-option DNS 8.8.4.4')
    ->add('keepalive', '10 120')
    ->add('renegSec', 18000)
    ->add('user', 'nobody')
    ->add('group', 'nogroup')
    ->add('persistKey', true)
    ->add('persistTun', true)
    ->add('compLzo', true)
    ->add('verb', 3)
    ->add('mute', 20)
    ->add('status', '/var/log/openvpn/status.log')
    ->add('logAppend', '/var/log/openvpn/openvpn.log')
    ->add('clientConfigDir', 'ccd')
    ->add('scriptSecurity', 3)
    ->add('usernameAsCommonName', true)
    ->add('verifyClientCert', 'none');

// Generate config by options
echo $config->generate();
