<?php
require_once __DIR__ . '/../vendor/autoload.php';

$_ovpn = new OpenVPN\Config();

$_ovpn
    ->add('dev', 'tun')
    ->add('proto', 'tcp')
    ->add('port', '1194')
    ->add('resolvRetry', 'infinite')
    ->add('cipher', 'AES-256-CBC')
    ->add('redirectGateway', 'true');

$_ovpn
    ->addCert('ca', '/etc/openvpn/ca.crt')
    ->addCert('cert', '/etc/openvpn/server.crt')
    ->addCert('key', '/etc/openvpn/server.key')
    ->addCert('dh', '/etc/openvpn/dh')
    ->addCert('tls-auth', '/etc/openvpn/ta.key', false, 0);

$_ovpn->add('server', '10.8.0.0 255.255.255.0');

$_ovpn
    ->addPush('redirect-gateway def1')
    ->addPush('dhcp-option DNS 8.8.8.8')
    ->addPush('dhcp-option DNS 8.8.4.4');

$_ovpn
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

echo $_ovpn->generateConfig();
