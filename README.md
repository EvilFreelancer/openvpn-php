# OpenVPN-PHP

OpenVPN config generator written on PHP7.

    composer require evilfreelancer/openvpn-php

## How to use

### Client config example

For begin you need to make preparation:

```php
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

### Server config example

```php
$config = $_ovpn->getServerConfig();
```

# Links

* [OpenVPN Admin](https://github.com/Chocobozzz/OpenVPN-Admin) - Web interface for your OpenVPN server
* [PHP Openvpn](https://github.com/paranic/openvpn) - Yet another library for generating OpenVPN config files
