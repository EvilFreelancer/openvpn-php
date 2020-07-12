<?php

namespace Tests\OpenVPN;

use OpenVPN\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /**
     * @var \OpenVPN\Config
     */
    private $class;

    public function setUp(): void
    {
        $this->class = new Config();
    }

    public function testClient(): void
    {
        $this->class->client();
        $params = $this->class->getParameters();
        self::assertArrayHasKey('client', $params);
    }

    public function testSetCert(): void
    {
        $this->class->setCert('ca', '/etc/openvpn/ca.crt', true);
        $this->class->setCert('cert', '/etc/openvpn/server.crt');
        $this->class->setCert('key', '/etc/openvpn/server.key');
        $this->class->setCert('dh', '/etc/openvpn/dh.pem');
        $this->class->setCert('tls-auth', '/etc/openvpn/ta.key');

        $certs = $this->class->getCerts();
        self::assertCount(5, $certs);
        self::assertEquals('/etc/openvpn/ca.crt', $certs['ca']['content']);
        self::assertEquals('/etc/openvpn/server.crt', $certs['cert']['path']);
    }

    public function testUnsetCert(): void
    {
        $this->class->setCert('ca', '/etc/openvpn/ca.crt');
        $this->class->setCert('cert', '/etc/openvpn/server.crt');
        self::assertCount(2, $this->class->getCerts());

        $this->class->unsetCert('ca');
        self::assertCount(1, $this->class->getCerts());

        self::assertTrue(isset($this->class->getCerts()['cert']));
    }

    public function testGetCerts(): void
    {
        $this->class->setCert('ca', '/etc/openvpn/ca.crt');
        $this->class->setCert('cert', '/etc/openvpn/server.crt');
        $this->class->setCert('key', '/etc/openvpn/server.key');
        $this->class->setCert('dh', '/etc/openvpn/dh.pem');
        $this->class->setCert('tls-auth', '/etc/openvpn/ta.key');
        $this->class->unsetCert('ca');
        $this->class->unsetCert('dh');

        self::assertCount(3, $this->class->getCerts());
    }
}
