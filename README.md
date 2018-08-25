# OpenVPN-PHP

OpenVPN config generator written on PHP7.

    composer require evilfreelancer/openvpn-php

Version 0.1 contains the first version of the configuration generator,
most of the parameters were available. as variables.

## How to use

It's very simple, you need to set the required parameters, then
generate the config and voila, everything is done.

More examples [here](examples).

### Client config example

For make client conf you need do almost same steps, just put your
variables and generate the config:

```php
<?php
require_once __DIR__ . '/vendor/autoload.php';

$_ovpn = new OpenVPN\Config();

$_ovpn
    ->addParam('client')
    ->addParam('tls-client')
    ->addParam('dev', 'tun')
    ->addParam('proto', 'tcp-client')
    ->addParam('port', '1194')
    ->addParam('resolv-retry', 'infinite')
    ->addParam('cipher', 'AES-256-CBC')
    ->addParam('redirect-gateway');

$_ovpn
    ->addCert('ca', '/etc/openvpn/ca.crt', true)
    ->addCert('tls-auth', '/etc/openvpn/ta.key', true);

$_ovpn
    ->addParam('key-direction', 1)
    ->addParam('remote-cert-tls', 'server')
    ->addParam('auth-user-pass')
    ->addParam('auth-nocache')
    ->addParam('nobind')
    ->addParam('persist-key')
    ->addParam('persist-tun')
    ->addParam('comp-lzo')
    ->addParam('verb', 3);

$_ovpn
    ->addParam('http-proxy', 'proxy-http.example.com 3128');
```

Now you can generate your client configuration file:

```php
$config = $_ovpn->generateConfig();

header('Content-Type:text/plain');
header("Content-Disposition: attachment; filename=client.conf");
header("Pragma: no-cache");
header("Expires: 0");

die("$config");
```

# Links

* [OpenVPN parameters](https://openvpn.net/index.php/open-source/documentation/manuals/65-openvpn-20x-manpage.html) - Full list of available parameters what can be used
* [OpenVPN Admin](https://github.com/Chocobozzz/OpenVPN-Admin) - Web interface for your OpenVPN server
* [Docker for OpenVPN Admin](https://github.com/EvilFreelancer/docker-openvpn-admin) - Dockerized web panel together with OpenVPN
* [PHP Openvpn](https://github.com/paranic/openvpn) - Yet another library for generating OpenVPN config files
