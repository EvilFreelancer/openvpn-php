[![Latest Stable Version](https://poser.pugx.org/evilfreelancer/openvpn-php/v/stable)](https://packagist.org/packages/evilfreelancer/openvpn-php)
[![Build Status](https://travis-ci.org/evilfreelancer/openvpn-php.svg?branch=master)](https://travis-ci.org/EvilFreelancer/openvpn-php)
[![Total Downloads](https://poser.pugx.org/evilfreelancer/openvpn-php/downloads)](https://packagist.org/packages/evilfreelancer/openvpn-php)
[![License](https://poser.pugx.org/evilfreelancer/openvpn-php/license)](https://packagist.org/packages/evilfreelancer/openvpn-php)
[![Code Climate](https://codeclimate.com/github/EvilFreelancer/openvpn-php/badges/gpa.svg)](https://codeclimate.com/github/EvilFreelancer/openvpn-php)
[![Code Coverage](https://scrutinizer-ci.com/g/EvilFreelancer/openvpn-php/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/EvilFreelancer/openvpn-php/?branch=master)
[![Scrutinizer CQ](https://scrutinizer-ci.com/g/EvilFreelancer/openvpn-php/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/EvilFreelancer/openvpn-php/)

# OpenVPN config manager

OpenVPN configuration generator/importer written on PHP7.

    composer require evilfreelancer/openvpn-php

## Laravel framework support

This library is optimized for usage as normal Laravel package, all functional is available via `\OpenVPN` facade,
for access to (for example) client object you need:

```php
// Config og client object
$config = \OpenVPN::getClient([
    'dev'              => 'tun',
    'proto'            => 'tcp',
    'resolv-retry'     => 'infinite',
    'cipher'           => 'AES-256-CB',
    'redirect-gateway' => true,
    'key-direction'    => 1,
    'remote-cert-tls'  => 'server',
    'auth-user-pass'   => true,
    'auth-nocache'     => true,
    'persist-key'      => true,
    'persist-tun'      => true,
    'comp-lzo'         => true,
    'verb'             => 3,
]);

// Another way for change values
$config->set('verb', 3);
$config->set('nobind');

// Yet another way for change values via magic methods
$config->remote    = 'vpn.example.com 1194';
$config->httpProxy = 'proxy-http.example.com 3128';

// Set additional certificates of client
$config->setCerts([
    'ca'   => '/etc/openvpn/keys/ca.crt',
    'cert' => '/etc/openvpn/keys/issued/client1.crt',
    'key'  => '/etc/openvpn/keys/private/client1.key',
], true); // true mean embed certificates into config, false by default

// Generate config by options
echo $config->generate();
```

It will read `openvpn-client.php` configuration from `configs` folder (if it was published of course), then merge your parameters to this
array and in results you will see the `\OpenVPN\Config` object.

### Laravel installation

Install the package via Composer:

    composer require evilfreelancer/openvpn-php

By default the package will automatically register its service provider, but
if you are a happy owner of Laravel version less than 5.5, then in a project, which is using your package
(after composer require is done, of course), add into`providers` block of your `config/app.php`:

```php
'providers' => [
    // ...
    OpenVPN\Laravel\ServiceProvider::class,
],
```

Optionally, publish the configuration files if you want to change any defaults:

    php artisan vendor:publish --provider="OpenVPN\\Laravel\\ServiceProvider"

It will create two files in your `configs` folder: `openvpn-client.php` and `openvpn-server.php`.

## How to use

It's very simple, you need to set the required parameters, then
generate the config and voila, everything is done.

More examples [here](examples).

### Write new config in OOP style

```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Config object
$config = new \OpenVPN\Config();

// Set server options
$config->dev                  = 'tun';
$config->proto                = 'tcp';
$config->port                 = 1194;
$config->resolvRetry          = 'infinite';
$config->cipher               = 'AES-256-CBC';
$config->redirectGateway      = true;
$config->server               = '10.8.0.0 255.255.255.0';
$config->keepalive            = '10 120';
$config->renegSec             = 18000;
$config->user                 = 'nobody';
$config->group                = 'nogroup';
$config->persistKey           = true;
$config->persistTun           = true;
$config->compLzo              = true;
$config->verb                 = 3;
$config->mute                 = 20;
$config->status               = '/var/log/openvpn/status.log';
$config->logAppend            = '/var/log/openvpn/openvpn.log';
$config->clientConfigDir      = 'ccd';
$config->scriptSecurity       = 3;
$config->usernameAsCommonName = true;
$config->verifyClientCert     = 'none';

// Set routes which will be used by server after starting
$config->setRoutes([
    '10.1.1.0 255.255.255.0',
    '10.1.2.0 255.255.255.0',
    '10.1.3.0 255.255.255.0',
]);

// Set additional certificates of server
$config->setCerts([
    'ca'   => '/etc/openvpn/keys/ca.crt',
    'cert' => '/etc/openvpn/keys/issued/server.crt',
]); // You can embed certificates into config by adding true as second parameter of setCerts method

// Another way for adding certificates
$config
    ->setCert('key', '/etc/openvpn/keys/private/server.key')
    ->setCert('dh', '/etc/openvpn/keys/dh.pem');

// Set pushes which will be passed to client
$config->setPushes([
    // Additional routes, which clients will see
    'route 10.1.2.0 255.255.255.0',
    'route 10.1.3.0 255.255.255.0',
    'route 10.1.4.0 255.255.255.0',

    // Replace default gateway, all client's traffic will be routed via VPN
    'redirect-gateway def1',

    // Prepend additional DNS addresses    
    'dhcp-option DNS 8.8.8.8', 
    'dhcp-option DNS 8.8.4.4',
]);

// Generate config by options
echo $config->generate();
```

### Import existing OpenVPN config

For example you have `server.conf` to import this file you need create
`\OpenVPN\Import` object and specify name of your config file.

```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Import OpenVPN config file
$import = new \OpenVPN\Import('server.conf');
// or (classic way)
$import = new \OpenVPN\Import();
$import->read('server.conf');

// Parse configuration and return "\OpenVPN\Config" object
$config = $import->parse();
```

In `$config` variable will be `\OpenVPN\Config` object.

### Client config example

For making client configuration you need just add required parameters
and generate the config:

```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Config object
$config = new \OpenVPN\Config();

// Set client options
$config->client();
$config->dev             = 'tun';
$config->remote          = 'vpn.example.com 1194';
$config->proto           = 'tcp';
$config->resolvRetry     = 'infinite';
$config->cipher          = 'AES-256-CB';
$config->redirectGateway = true;
$config->keyDirection    = 1;
$config->remoteCertTls   = 'server';
$config->authUserPass    = true;
$config->authNocache     = true;
$config->nobind          = true;
$config->persistKey      = true;
$config->persistTun      = true;
$config->compLzo         = true;
$config->verb            = 3;
$config->httpProxy       = 'proxy-http.example.com 3128';

// Set additional certificates of client
$config->setCerts([
    'ca'   => '/etc/openvpn/keys/ca.crt',
    'cert' => '/etc/openvpn/keys/issued/client1.crt',
    'key'  => '/etc/openvpn/keys/private/client1.key',
], true); // true mean embed certificates into config, false by default

// Generate config by options
echo $config->generate();
```

### Downloadable config

Just a simple usage example:

```php
header('Content-Type:text/plain');
header('Content-Disposition: attachment; filename=client.ovpn');
header('Pragma: no-cache');
header('Expires: 0');

die($config->generate());
```

# Links

* [OpenVPN parameters](https://openvpn.net/index.php/open-source/documentation/manuals/65-openvpn-20x-manpage.html) - Full list of available parameters what can be used
* [Laravel VPN Admin](https://github.com/Laravel-VPN-Admin) - Web interface for your VPN server
* [OpenVPN Admin](https://github.com/Chocobozzz/OpenVPN-Admin) - Web interface for your OpenVPN server
* [Docker for OpenVPN Admin](https://github.com/EvilFreelancer/docker-openvpn-admin) - Dockerized web panel together with OpenVPN
* [PHP OpenVPN](https://github.com/paranic/openvpn) - Yet another library for generating OpenVPN config files
