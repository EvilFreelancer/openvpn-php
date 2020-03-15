<?php

namespace OpenVPN\Tests;

use OpenVPN\Config;
use OpenVPN\Import;
use PHPUnit\Framework\TestCase;

class ImportTest extends TestCase
{
    /**
     * @var \OpenVPN\Import
     */
    private $import;

    /**
     * @var \OpenVPN\Config
     */
    private $config;

    public function setUp()
    {
        $this->import = new Import();
        $this->config = new Config();

        // Set server options
        $this->config->dev                  = 'tun';
        $this->config->proto                = 'tcp';
        $this->config->local                = 'vpn.example.com';
        $this->config->port                 = 1194;
        $this->config->resolvRetry          = 'infinite';
        $this->config->cipher               = 'AES-256-CBC';
        $this->config->redirectGateway      = true;
        $this->config->server               = '10.8.0.0 255.255.255.0';
        $this->config->keepalive            = '10 120';
        $this->config->renegSec             = 18000;
        $this->config->user                 = 'nobody';
        $this->config->group                = 'nogroup';
        $this->config->persistKey           = true;
        $this->config->persistTun           = true;
        $this->config->compLzo              = true;
        $this->config->verb                 = 3;
        $this->config->mute                 = 20;
        $this->config->status               = '/var/log/openvpn/status.log';
        $this->config->logAppend            = '/var/log/openvpn/openvpn.log';
        $this->config->clientConfigDir      = 'ccd';
        $this->config->scriptSecurity       = 3;
        $this->config->usernameAsCommonName = true;
        $this->config->verifyClientCert     = 'none';
        $this->config->authUserPassVerify   = 'your_script.sh via-file';
        $this->config->duplicateCn          = true;

        // Set routes which will be used by server after starting
        $this->config->setRoutes([
            '10.1.1.0 255.255.255.0',
            '10.1.2.0 255.255.255.0',
            '10.1.3.0 255.255.255.0',
            '10.1.4.0 255.255.255.0',
            '10.1.5.0 255.255.255.0',
        ]);

        // Set additional certificates of server
        $this->config->setCerts([
            'ca'   => '/etc/openvpn/keys/ca.crt',
            'cert' => '/etc/openvpn/keys/issued/server.crt',
        ]); // You can embed certificates into config by adding true as second parameter of setCerts method

        // Another way for adding certificates
        $this->config
            ->setCert('key', '/etc/openvpn/keys/private/server.key')
            ->setCert('dh', '/etc/openvpn/keys/dh.pem');

        // Set pushes which will be passed to client
        $this->config->setPushes([
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
    }

    public function testParse(): void
    {
        $this->import->lines = [
            'dev tun',
            'proto tcp',
            'local vpn.example.com',
            'port 1194',
            'resolv-retry infinite',

            'cipher AES-256-CBC',
            'redirect-gateway',
            'server 10.8.0.0 255.255.255.0',
            'keepalive 10 120',
            'reneg-sec 18000',
            'user nobody',
            'group nogroup',
            'persist-key',
            'persist-tun',
            'comp-lzo',
            'verb 3',
            'mute 20',
            'status /var/log/openvpn/status.log',
            'log-append /var/log/openvpn/openvpn.log',
            'client-config-dir ccd',
            'script-security 3',
            'username-as-common-name',
            'verify-client-cert none',
            'auth-user-pass-verify your_script.sh via-file',
            'duplicate-cn',
            'route 10.1.1.0 255.255.255.0',
            'route 10.1.2.0 255.255.255.0',
            'route 10.1.3.0 255.255.255.0',
            'route 10.1.4.0 255.255.255.0',
            'route 10.1.5.0 255.255.255.0',
            'push "route 10.1.2.0 255.255.255.0"',
            'push "route 10.1.3.0 255.255.255.0"',
            'push "route 10.1.4.0 255.255.255.0"',
            'push "redirect-gateway def1"',
            'push "dhcp-option DNS 8.8.8.8"',
            'push "dhcp-option DNS 8.8.4.4"',
            'ca /etc/openvpn/keys/ca.crt',
            'cert /etc/openvpn/keys/issued/server.crt',
            'key /etc/openvpn/keys/private/server.key',
            'dh /etc/openvpn/keys/dh.pem',
        ];

        $object = $this->import->parse();

        $this->assertInstanceOf(Config::class, $object);

        // For tests
        $pushes = $object->getPushes();
        $certs  = $object->getCerts();
        $routes = $object->getRoutes();
        $params = $object->getParameters();

        $this->assertCount(6, $pushes);
        $this->assertCount(4, $certs);
        $this->assertCount(5, $routes);
        $this->assertCount(25, $params);
    }

    public function testRead(): void
    {
        $file = __DIR__ . '/server.ovpn';
        $this->import->read($file);
        $object = $this->import->parse();

        // For tests
        $pushes = $object->getPushes();
        $certs  = $object->getCerts();
        $routes = $object->getRoutes();
        $params = $object->getParameters();

        $this->assertCount(6, $pushes);
        $this->assertCount(4, $certs);
        $this->assertCount(5, $routes);
        $this->assertCount(25, $params);
    }

    public function testLoad(): void
    {
        // Generate config
        $config = $this->config->generate();

        // Load text via importer then parse and return object
        $this->import->load($config);
        $object = $this->import->parse();

        // For tests
        $pushes = $object->getPushes();
        $certs  = $object->getCerts();
        $routes = $object->getRoutes();
        $params = $object->getParameters();

        $this->assertCount(6, $pushes);
        $this->assertCount(4, $certs);
        $this->assertCount(5, $routes);
        $this->assertCount(25, $params);
    }
}
