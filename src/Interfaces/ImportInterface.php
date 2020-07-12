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
     * Parse lines which was read fron file
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
     * @param string|array $content Content of config file, if array then will be selected array parser
     * @param string       $type    Type of loaded content: raw (default), json
     *
     * @return array Array with count of total and read lines
     */
    public function load($content, string $type = 'raw'): array;
}
