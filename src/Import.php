<?php

namespace OpenVPN;

use OpenVPN\Interfaces\ConfigInterface;
use OpenVPN\Interfaces\ImportInterface;
use function strlen;

class Import implements ImportInterface
{
    /**
     * Lines of config file
     *
     * @var array
     */
    public $lines = [];

    /**
     * Import constructor, can import file on starting
     *
     * @param string|null $filename  Path to config file
     * @param bool        $isContent If true, then path mean content of config file
     */
    public function __construct(string $filename = null, bool $isContent = false)
    {
        if ($isContent) {
            $this->load($filename);
        } elseif (null !== $filename) {
            $this->read($filename);
        }
    }

    /**
     * Check if line is valid config line, TRUE if line is okay.
     * If empty line or line with comment then FALSE.
     *
     * @param string $line
     *
     * @return bool
     */
    private function isLine(string $line): bool
    {
        return !(
            // Empty lines
            preg_match('/^\n+|^[\t\s]*\n+/m', $line) ||
            // Lines with comments
            preg_match('/^#/m', $line)
        );
    }

    /**
     * Read configuration file line by line
     *
     * @param string $filename
     *
     * @return array Array with count of total and read lines
     */
    public function read(string $filename): array
    {
        $content = file_get_contents($filename);
        return $this->load($content);
    }

    /**
     * Load content from text of config
     *
     * @param string $content Content of config file
     *
     * @return array Array with count of total and read lines
     */
    public function load(string $content): array
    {
        $result = ['total' => 0, 'read' => 0];

        // Open file as SPL object
        $lines = explode("\n", $content);

        // Read line by line
        foreach ($lines as $line) {
            $line = trim($line);
            // Save line only of not empty
            if ($this->isLine($line) && strlen($line) > 1) {
                $line          = trim(preg_replace('/\s+/', ' ', $line));
                $this->lines[] = $line;
                $result['read']++;
            }
            $result['total']++;
        }
        return $result;
    }

    /**
     * Parse readed lines
     *
     * @return \OpenVPN\Interfaces\ConfigInterface
     */
    public function parse(): ConfigInterface
    {
        $config = new Config();
        array_map(
            static function ($line) use ($config) {
                if (preg_match('/^(\S+)( (.*))?/', $line, $matches)) {
                    $config->set($matches[1], $matches[3] ?? true);
                }
            },
            $this->lines
        );
        return $config;
    }
}
