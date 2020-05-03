<?php

namespace OpenVPN\Tests;

use OpenVPN\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    private $class;

    public function setUp(): void
    {
        $this->class = new Config();
    }

    public function testSetCert(): void
    {
        $this->class->setCert('ca', '/etc/openvpn/ca.crt', true);
        $this->class->setCert('cert', '/etc/openvpn/server.crt');
        $this->class->setCert('key', '/etc/openvpn/server.key');
        $this->class->setCert('dh', '/etc/openvpn/dh.pem');
        $this->class->setCert('tls-auth', '/etc/openvpn/ta.key');

        $certs = $this->class->getCerts();
        $this->assertCount(5, $certs);
        $this->assertEquals('/etc/openvpn/ca.crt', $certs['ca']['content']);
        $this->assertEquals('/etc/openvpn/server.crt', $certs['cert']['path']);
    }

    public function testUnsetCert(): void
    {
        $this->class->setCert('ca', '/etc/openvpn/ca.crt');
        $this->class->setCert('cert', '/etc/openvpn/server.crt');
        $this->assertCount(2, $this->class->getCerts());

        $this->class->unsetCert('ca');
        $this->assertCount(1, $this->class->getCerts());

        $this->assertTrue(isset($this->class->getCerts()['cert']));
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
        $this->assertCount(3, $this->class->getCerts());
    }

}
