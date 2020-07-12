<?php

namespace OpenVPN\Laravel;

use OpenVPN\Config;
use OpenVPN\Generator;
use OpenVPN\Import;
use OpenVPN\Interfaces\ConfigInterface;
use OpenVPN\Interfaces\GeneratorInterface;
use OpenVPN\Interfaces\ImportInterface;

class Wrapper
{
    /**
     * Get client configuration of OpenVPN
     *
     * @param array $params
     *
     * @return \OpenVPN\Interfaces\ConfigInterface
     */
    public function client(array $params = []): ConfigInterface
    {
        $configs = config('openvpn-client');
        $configs = array_merge($configs, $params);
        $object  = new Config($configs);

        return $object->client();
    }

    /**
     * Get server configuration of OpenVPN
     *
     * @param array $params
     *
     * @return \OpenVPN\Interfaces\ConfigInterface
     */
    public function server(array $params = []): ConfigInterface
    {
        $configs = config('openvpn-server');
        $configs = array_merge($configs, $params);

        return new Config($configs);
    }

    /**
     * Get instance of config generator
     *
     * @param \OpenVPN\Interfaces\ConfigInterface $config
     *
     * @return \OpenVPN\Interfaces\GeneratorInterface
     */
    public function generator(ConfigInterface $config): GeneratorInterface
    {
        return new Generator($config);
    }

    /**
     * Get instance of config importer
     *
     * @param string|null $filename
     * @param bool        $isContent
     *
     * @return \OpenVPN\Interfaces\ImportInterface
     */
    public function importer(string $filename = null, bool $isContent = false): ImportInterface
    {
        return new Import($filename, $isContent);
    }
}
