<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Config object
$config = new \OpenVPN\Config();

// Set server options
$config
    ->set('dev', 'tun')
    ->set('proto', 'tcp')
    ->set('port', '1194')
    ->set('resolv-retry', 'infinite')
    ->set('cipher', 'AES-256-CBC')
    ->set('redirect-gateway', 'true')
    ->set('server', '10.8.0.0 255.255.255.0')
    ->set('keepalive', '10 120')
    ->set('reneg-sec', 18000)
    ->set('user', 'nobody')
    ->set('group', 'nogroup')
    ->set('persist-key', true)
    ->set('persist-tun', true)
    ->set('compLzo', true)
    ->set('verb', 3)
    ->set('mute', 20)
    ->set('status', '/var/log/openvpn/status.log')
    ->set('log-append', '/var/log/openvpn/openvpn.log')
    ->set('client-config-dir', 'ccd')
    ->set('script-security', 3)
    ->set('username-as-common-name', true)
    ->set('verify-client-cert', 'none')
    ->setCert('ca', '/etc/openvpn/ca.crt')
    ->setCert('cert', '/etc/openvpn/server.crt')
    ->setCert('key', '/etc/openvpn/server.key')
    ->setCert('dh', '/etc/openvpn/dh')
    ->setCert('tls-auth', '/etc/openvpn/ta.key 0')
    ->setPush('redirect-gateway def1')
    ->setPush('dhcp-option DNS 8.8.8.8')
    ->setPush('dhcp-option DNS 8.8.4.4');

// Generate config by options
echo $config->generate();
