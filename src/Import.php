<?php

namespace OpenVPN;

use OpenVPN\Interfaces\ConfigInterface;

class Import
{
    /**
     * Lines of config file
     * @var array
     */
    private $_lines = [];

    /**
     * Import constructor, can import file on starting
     * @param string|null $filename
     */
    public function __construct(string $filename = null)
    {
        if (null !== $filename) {
            $this->read($filename);
        }
    }

    /**
     * Check if line is valid config line, TRUE if line is okay.
     * If empty line or line with comment then FALSE.
     *
     * @param   string $line
     * @return  bool
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
     * @param   string $filename
     * @return  array Array with count of total and read lines
     */
    public function read(string $filename): array
    {
        $lines = ['total' => 0, 'read' => 0];

        // Open file as SPL object
        $file = new \SplFileObject($filename);

        // Read line by line
        while (!$file->eof()) {
            $line = $file->fgets();
            // Save line only of not empty
            if ($this->isLine($line) && \strlen($line) > 1) {
                $line = trim(preg_replace('/\s+/', ' ', $line));
                $this->_lines[] = $line;
                $lines['read']++;
            }
            $lines['total']++;
        }
        return $lines;
    }

    /**
     * Parse readed lines
     *
     * @return  ConfigInterface
     */
    public function parse(): ConfigInterface
    {
        $config = new Config();
        array_map(
            function($line) use ($config) {
                if (preg_match('/^(\S+)\ (.*)/', $line, $matches)) {
                    switch ($matches[1]) {
                        case 'push':
                            $config->addPush($matches[2]);
                            break;
                        case 'ca':
                        case 'cert':
                        case 'key':
                        case 'dh':
                        case 'tls-auth':
                            $config->addCert($matches[1], $matches[2]);
                            break;
                        default:
                            $config->add($matches[1], $matches[2]);
                            break;
                    }
                }
            },
            $this->_lines
        );
        return $config;
    }
}
