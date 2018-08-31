# OpenVPN configuration manager

OpenVPN configuration generator/importer written on PHP7.

    composer require evilfreelancer/openvpn-php

## How to use

It's very simple, you need to set the required parameters, then
generate the config and voila, everything is done.

More examples [here](examples).

### Import existing OpenVPN config

For example you have `server.conf` to import this file you need create
`\OpenVPN\Import` object and specify name of your config file.

```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Import OpenVPN config file
$import = new \OpenVPN\Import('server.conf');
// or (classic way)
$import = new \OpenVPN\();
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
$config = new OpenVPN\Config();

// Set client options
$config
    ->add('client')
    ->add('dev', 'tun')
    ->add('proto', 'tcp-client')
    ->add('port', '1194')
    ->add('resolv-retry', 'infinite')
    ->add('cipher', 'AES-256-CBC')
    ->add('redirect-gateway', true)
    ->add('ca', '/etc/openvpn/ca.crt')
    ->add('tls-auth', '/etc/openvpn/ta.key 0')
    ->add('key-direction', 1)
    ->add('remote-cert-tls', 'server')
    ->add('auth-user-pass', true)
    ->add('auth-nocache', true)
    ->add('nobind', true)
    ->add('persist-key', true)
    ->add('persist-tun', true)
    ->add('comp-lzo', true)
    ->add('verb', 3)
    ->add('http-proxy', 'proxy-http.example.com 3128');

// Generate config by options
echo $config->generate();
```

### Downloadable config

Just a simple usage example:

```php
header('Content-Type:text/plain');
header("Content-Disposition: attachment; filename=client.conf");
header("Pragma: no-cache");
header("Expires: 0");

die($config->generate());
```

# Links

* [OpenVPN parameters](https://openvpn.net/index.php/open-source/documentation/manuals/65-openvpn-20x-manpage.html) - Full list of available parameters what can be used
* [OpenVPN Admin](https://github.com/Chocobozzz/OpenVPN-Admin) - Web interface for your OpenVPN server
* [Docker for OpenVPN Admin](https://github.com/EvilFreelancer/docker-openvpn-admin) - Dockerized web panel together with OpenVPN
* [PHP Openvpn](https://github.com/paranic/openvpn) - Yet another library for generating OpenVPN config files
