<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Config object
$config = new \OpenVPN\Config();

// Set server options
$config
    ->add('dev', 'tun')
    ->add('proto', 'tcp')
    ->add('port', '1194')
    ->add('resolv-retry', 'infinite')
    ->add('cipher', 'AES-256-CBC')
    ->add('redirect-gateway', 'true')
    ->add('server', '10.8.0.0 255.255.255.0')
    ->add('keepalive', '10 120')
    ->add('reneg-sec', 18000)
    ->add('user', 'nobody')
    ->add('group', 'nogroup')
    ->add('persist-key', true)
    ->add('persist-tun', true)
    ->add('compLzo', true)
    ->add('verb', 3)
    ->add('mute', 20)
    ->add('status', '/var/log/openvpn/status.log')
    ->add('log-append', '/var/log/openvpn/openvpn.log')
    ->add('client-config-dir', 'ccd')
    ->add('script-security', 3)
    ->add('username-as-common-name', true)
    ->add('verify-client-cert', 'none')
    ->addCert('ca', '/etc/openvpn/ca.crt')
    ->addCert('cert', '/etc/openvpn/server.crt')
    ->addCert('key', '/etc/openvpn/server.key')
    ->addCert('dh', '/etc/openvpn/dh')
    ->addCert('tls-auth', '/etc/openvpn/ta.key 0')
    ->addPush('redirect-gateway def1')
    ->addPush('dhcp-option DNS 8.8.8.8')
    ->addPush('dhcp-option DNS 8.8.4.4');

// Generate config by options
echo $config->generate();
