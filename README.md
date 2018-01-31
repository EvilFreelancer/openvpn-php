# OpenVPN-PHP

OpenVPN config generator written on PHP7.

    composer require evilfreelancer/openvpn-php

## How to use

It's very simple, you need to set the required parameters, then
generate the config and voila, everything is done.

More examples [here](extra).

### Server config example

For begin you need to make preparation:

```php
$_ovpn = new EvilFreelancer\OpenVPN();

$_ovpn->dev = 'tun';
$_ovpn->proto = 'tcp';
$_ovpn->port = '1194';
$_ovpn->resolvRetry = 'infinite';
$_ovpn->cipher = 'AES-256-CBC';
$_ovpn->redirectGateway = true;

// Append certs
$_ovpn->addCert('ca', '/etc/openvpn/ca.crt')
      ->addCert('cert', '/etc/openvpn/server.crt')
      ->addCert('key', '/etc/openvpn/server.key')
      ->addCert('dh', '/etc/openvpn/dh')
      ->addCert('tls-auth', '/etc/openvpn/ta.key', false, 0);

$_ovpn->server = "10.8.0.0 255.255.255.0";

// Insert pushes into the config
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

// Generate configs
echo $_ovpn->getServerConfig();
```

### Client config example

For make client conf you need do almost same steps, just put your
variables and generate the config:

```php
$_ovpn = new EvilFreelancer\OpenVPN();

$_ovpn->dev = 'tun';
$_ovpn->proto = 'tcp';
$_ovpn->port = '1194';
$_ovpn->resolvRetry = 'infinite';
$_ovpn->cipher = 'AES-256-CBC';
$_ovpn->redirectGateway = true;

// You can enbed certificates into your config, for this just set in "true" the third parameters
$_ovpn->addCert('ca', '/etc/openvpn/ca.crt', true)
      ->addCert('tls-auth', '/etc/openvpn/ta.key', true);

// If you wanna to use certs placed in same folder as config
$_ovpn->addCert('ca', 'ca.crt')
      ->addCert('tls-auth', 'ta.key', false, 1);

$_ovpn->keyDirection = 1;
$_ovpn->remoteCertTls = 'server';
$_ovpn->authUserPass = true;
$_ovpn->authNocache = true;

$_ovpn->nobind = true;
$_ovpn->persistKey = true;
$_ovpn->persistTun = true;
$_ovpn->compLzo = true;
$_ovpn->verb = 3;

$_ovpn->httpProxy = 'proxy-http.example.com 3128'
```

Now you can generate your client configuration file:

```php
$config = $_ovpn->getClientConfig();

header('Content-Type:text/plain');
header("Content-Disposition: attachment; filename=client.conf");
header("Pragma: no-cache");
header("Expires: 0");

echo $config;
```

# Links

* [OpenVPN Admin](https://github.com/Chocobozzz/OpenVPN-Admin) - Web interface for your OpenVPN server
* [Docker for OpenVPN Admin](https://github.com/EvilFreelancer/docker-openvpn-admin) - Dockerized web panel together with OpenVPN
* [PHP Openvpn](https://github.com/paranic/openvpn) - Yet another library for generating OpenVPN config files
