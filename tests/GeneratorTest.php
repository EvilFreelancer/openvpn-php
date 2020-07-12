<?php

namespace Tests\OpenVPN;

use OpenVPN\Config;
use OpenVPN\Generator;
use PHPUnit\Framework\TestCase;

class GeneratorTest extends TestCase
{
    /**
     * @var \OpenVPN\Generator
     */
    private $object;

    public function setUp(): void
    {
        $config       = new Config([
            'remote' => 'vpn.example.com 1234',
            'push'   => 'dns 1.2.3.4',
        ]);
        $this->object = new Generator($config);
    }

    public function testGenerate(): void
    {
        $test1 = $this->object->generate('raw');
        $test2 = $this->object->generate('array');
        $test3 = $this->object->generate('json');
        $test4 = $this->object->generate('awesome random string');

        self::assertEquals("push \"dns 1.2.3.4\"\nremote vpn.example.com 1234", $test1);
        self::assertEquals(["push \"dns 1.2.3.4\"", "remote vpn.example.com 1234"], $test2);
        self::assertEquals("[\n    \"push \\\"dns 1.2.3.4\\\"\",\n    \"remote vpn.example.com 1234\"\n]", $test3);
        self::assertNull($test4);
    }
}
