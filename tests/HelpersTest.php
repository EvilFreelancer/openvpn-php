<?php

namespace Tests\OpenVPN;

use OpenVPN\Helpers;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class HelpersTest extends TestCase
{

    public function testIsCertAllowedException(): void
    {
        $this->expectException(RuntimeException::class);
        Helpers::isCertAllowed('wrong');
    }

    public function decamelizeDataProvider(): array
    {
        return [
            ['input' => '', 'result' => ''],
            ['input' => 'MMM', 'result' => 'mmm'],
            ['input' => 'makeMeHappy', 'result' => 'make-me-happy'],
            ['input' => 'make-Me-Happy', 'result' => 'make-me-happy'],
            ['input' => 'make Me Happy', 'result' => 'make-me-happy'],
            ['input' => 'make me happy', 'result' => 'make-me-happy'],
        ];
    }

    /**
     * @dataProvider decamelizeDataProvider
     *
     * @param string $input
     * @param string $result
     */
    public function testDecamelize(string $input, string $result): void
    {
        $test = Helpers::decamelize($input);
        self::assertEquals($result, $test);
    }
}
