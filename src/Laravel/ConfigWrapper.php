<?php

namespace OpenVPN\Laravel;

use OpenVPN\Config;
use OpenVPN\Interfaces\ConfigInterface;

class ConfigWrapper
{
    /**
     * Get client configuration of OpenVPN
     *
     * @param array $params
     *
     * @return \OpenVPN\Interfaces\ConfigInterface
     */
    public function getClient(array $params = []): ConfigInterface
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
    public function getServer(array $params = []): ConfigInterface
    {
        $configs = config('openvpn-server');
        $configs = array_merge($configs, $params);

        return new Config($configs);
    }
}
