<?php
require_once __DIR__ . "/../vendor/autoload.php";

$_ovpn = new OpenVPN\Config();

$_ovpn
    ->add('client')
    ->add('dev', 'tun')
    ->add('proto', 'tcp-client')
    ->add('port', '1194')
    ->add('resolv-retry', 'infinite')
    ->add('cipher', 'AES-256-CBC')
    ->add('redirect-gateway', true);

$_ovpn
    ->addCert('ca', '/etc/openvpn/ca.crt', true)
    ->addCert('tls-auth', '/etc/openvpn/ta.key', true);

$_ovpn
    ->add('key-direction', 1)
    ->add('remote-cert-tls', 'server')
    ->add('auth-user-pass', true)
    ->add('auth-nocache', true)
    ->add('nobind', true)
    ->add('persist-key', true)
    ->add('persist-tun', true)
    ->add('comp-lzo', true)
    ->add('verb', 3);

$_ovpn
    ->add('http-proxy', 'proxy-http.example.com 3128');

echo $_ovpn->generateConfig();
