<?php
require_once __DIR__ . "/../vendor/autoload.php";

$_ovpn = new EvilFreelancer\OpenVPN();

$_ovpn
    ->addParam('client')
    ->addParam('dev', 'tun')
    ->addParam('proto', 'tcp-client')
    ->addParam('port', '1194')
    ->addParam('resolv-retry', 'infinite')
    ->addParam('cipher', 'AES-256-CBC')
    ->addParam('redirect-gateway', true);

$_ovpn
    ->addCert('ca', '/etc/openvpn/ca.crt', true)
    ->addCert('tls-auth', '/etc/openvpn/ta.key', true);

$_ovpn
    ->addParam('key-direction', 1)
    ->addParam('remote-cert-tls', 'server')
    ->addParam('auth-user-pass', true)
    ->addParam('auth-nocache', true)
    ->addParam('nobind', true)
    ->addParam('persist-key', true)
    ->addParam('persist-tun', true)
    ->addParam('comp-lzo', true)
    ->addParam('verb', 3);

$_ovpn
    ->addParam('http-proxy', 'proxy-http.example.com 3128');

echo $_ovpn->generateConfig();
