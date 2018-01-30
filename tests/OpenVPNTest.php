<?php
use EvilFreelancer\OpenVPN;
use PHPUnit\Framework\TestCase;

class OpenVPNTest extends TestCase
{
    private $class;

    public function __construct(string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->class = new OpenVPN();
    }

    public function testAddCert()
    {
        $this->class->addCert('ca', '/etc/openvpn/ca.crt');
        $this->class->addCert('cert', '/etc/openvpn/server.crt');
        $this->class->addCert('key', '/etc/openvpn/server.key', true);
        $this->class->addCert('dh', '/etc/openvpn/dh.pem');
        $this->class->addCert('tls-auth', '/etc/openvpn/ta.key', false, 0);

        $this->assertEquals(count($this->class->getCerts()), 5);
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
        $this->class->addCert('key', '/etc/openvpn/server.key', true);
        $this->class->addCert('dh', '/etc/openvpn/dh.pem');
        $this->class->addCert('tls-auth', '/etc/openvpn/ta.key', false, 1);
        $this->class->delCert('ca');
        $this->class->delCert('dh');
        $this->assertEquals(count($this->class->getCerts()), 3);

    }

    public function testAddPush()
    {

    }

    public function testDelPush()
    {

    }

    public function testGetPushes()
    {

    }

    public function testGenerateConfig()
    {

    }

    public function testGetClientConfig()
    {

    }

    public function testGetServerConfig()
    {

    }

}
