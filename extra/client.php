<?php
require_once __DIR__ . "/../vendor/autoload.php";

$_ovpn = new EvilFreelancer\OpenVPN();

$_ovpn->dev = 'tun';
$_ovpn->proto = 'tcp';
$_ovpn->port = '1194';
$_ovpn->resolvRetry = 'infinite';
$_ovpn->cipher = 'AES-256-CBC';
$_ovpn->redirectGateway = true;

$_ovpn->addCert('ca', '/etc/openvpn/ca.crt', true)
    ->addCert('tls-auth', '/etc/openvpn/ta.key', false, 0);

$_ovpn->keyDirection = 1;
$_ovpn->remoteCertTls = 'server';
$_ovpn->authUserPass = true;
$_ovpn->authNocache = true;

$_ovpn->nobind = true;
$_ovpn->persistKey = true;
$_ovpn->persistTun = true;
$_ovpn->compLzo = true;
$_ovpn->verb = 3;

$_ovpn->httpProxy = 'proxy-http.example.com 3128';

echo $_ovpn->getClientConfig();
