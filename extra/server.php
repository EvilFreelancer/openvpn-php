<?php
require_once __DIR__ . "/../vendor/autoload.php";

$_ovpn = new EvilFreelancer\OpenVPN();

$_ovpn->dev = 'tun';
$_ovpn->proto = 'tcp';
$_ovpn->port = '1194';
$_ovpn->resolvRetry = 'infinite';
$_ovpn->cipher = 'AES-256-CBC';
$_ovpn->redirectGateway = true;

$_ovpn->addCert('ca', '/etc/openvpn/ca.crt')
    ->addCert('cert', '/etc/openvpn/server.crt')
    ->addCert('key', '/etc/openvpn/server.key')
    ->addCert('dh', '/etc/openvpn/dh')
    ->addCert('tls-auth', '/etc/openvpn/ta.key', false, 0);

$_ovpn->server = "10.8.0.0 255.255.255.0";

$_ovpn->addPush("redirect-gateway def1")
    ->addPush("dhcp-option DNS 8.8.8.8")
    ->addPush("dhcp-option DNS 8.8.4.4");

$_ovpn->keepalive = "10 120";
$_ovpn->renegSec = 18000;

$_ovpn->user = "nobody";
$_ovpn->group = "nogroup";

$_ovpn->persistKey = true;
$_ovpn->persistTun = true;
$_ovpn->compLzo = true;
$_ovpn->verb = 3;
$_ovpn->mute = 20;
$_ovpn->status = "/var/log/openvpn/status.log";
$_ovpn->logAppend = "/var/log/openvpn/openvpn.log";
$_ovpn->clientConfigDir = "ccd";

$_ovpn->scriptSecurity = 3;
$_ovpn->usernameAsCommonName = true;
$_ovpn->verifyClientCert = 'none';

echo $_ovpn->getServerConfig();
