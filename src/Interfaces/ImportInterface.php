<?php

namespace OpenVPN\Interfaces;

/**
 * Interface ImportInterface
 *
 * @package OpenVPN\Interfaces
 * @since   1.0.3
 */
interface ImportInterface
{
    /**
     * Parse readed lines
     *
     * @return \OpenVPN\Interfaces\ConfigInterface
     */
    public function parse(): ConfigInterface;

    /**
     * Read configuration file line by line
     *
     * @param string $filename
     *
     * @return array Array with count of total and read lines
     */
    public function read(string $filename): array;

    /**
     * Load content from text of config
     *
     * @param string $content Content of config file
     *
     * @return array Array with count of total and read lines
     */
    public function load(string $content): array;
}
