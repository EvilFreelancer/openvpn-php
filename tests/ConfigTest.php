<?php

use OpenVPN\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    private $class;

    public function __construct(string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->class = new Config();
    }

    public function testAddCert()
    {
        $this->class->addCert('ca', '/etc/openvpn/ca.crt', true);
        $this->class->addCert('cert', '/etc/openvpn/server.crt');
        $this->class->addCert('key', '/etc/openvpn/server.key');
        $this->class->addCert('dh', '/etc/openvpn/dh.pem');
        $this->class->addCert('tls-auth', '/etc/openvpn/ta.key');

        $certs = $this->class->getCerts();
        $this->assertCount(5, $certs);
        $this->assertEquals('/etc/openvpn/ca.crt', $certs['ca']['content']);
        $this->assertEquals('/etc/openvpn/server.crt', $certs['cert']['path']);
    }

    public function testDelCert()
    {
        $this->class->addCert('ca', '/etc/openvpn/ca.crt');
        $this->class->addCert('cert', '/etc/openvpn/server.crt');
        $this->assertEquals(count($this->class->getCerts()), 2);

        $this->class->delCert('ca');
        $this->assertEquals(count($this->class->getCerts()), 1);

        $this->assertTrue(isset($this->class->getCerts()['cert']));
    }

    public function testGetCerts()
    {
        $this->class->addCert('ca', '/etc/openvpn/ca.crt');
        $this->class->addCert('cert', '/etc/openvpn/server.crt');
        $this->class->addCert('key', '/etc/openvpn/server.key');
        $this->class->addCert('dh', '/etc/openvpn/dh.pem');
        $this->class->addCert('tls-auth', '/etc/openvpn/ta.key');
        $this->class->delCert('ca');
        $this->class->delCert('dh');
        $this->assertEquals(count($this->class->getCerts()), 3);

    }

}
