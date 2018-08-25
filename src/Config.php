<?php

namespace OpenVPN;

use OpenVPN\Interfaces\ConfigInterface;

class Config implements ConfigInterface
{
    /**
     * Array with all certificates
     * @var array
     */
    private $_certs = [];

    /**
     * List of lines which must be pushed to clients
     * @var array
     */
    private $_pushes = [];

    /**
     * All parameters added via addParam method
     * @var array
     */
    private $_params = [];

    /**
     * Generate config by parameters in memory
     *
     * @return  string
     */
    public function generate(): string
    {
        // Init the variable
        $config = '';

        foreach ($this->getParams() as $param) {
            $config .= $param['name'] . ((mb_strlen($param['value']) > 0) ? ' ' . $param['value'] : '') . "\n";
        }

        // Keys and certs
        foreach ($this->getCerts() as $key => $value) {
            $config .= !empty($value['content'])
                ? "<$key>\n" . $value['content'] . "\n</$key>\n"
                : "$key " . $value['path'] . (($key === 'tls-auth') ? ' ' . $value['option'] : null) . "\n";
        }

        // Network and push
        foreach ($this->getPushes() as $push) {
            $config .= 'push "' . $push . "\"\n";
        }

        return $config;
    }

    /**
     * Add new cert into the configuration
     *
     * @param   string $type - Type of certificate [ca, cert, key, dh, tls-auth]
     * @param   string $path - Absolute or relative path to certificate
     * @param   bool $load - Read certificate as plain text
     * @param   int $option - Optional parameter (for tls-auth)
     * @return  Config
     */
    public function addCert(string $type, string $path, bool $load = false, int $option = 0): ConfigInterface
    {
        $type = mb_strtolower($type);
        $this->_certs[$type]['path'] = !empty($path) ? $path : null;
        $this->_certs[$type]['content'] = (true === $load) ? rtrim(file_get_contents($path)) : null;
        $this->_certs[$type]['option'] = !empty($option) ? $option : null;

        return $this;
    }

    /**
     * Remove selected certificate from array
     *
     * @param   string $type - Type of certificate [ca, cert, key, dh, tls-auth]
     * @return  ConfigInterface
     */
    public function delCert(string $type): ConfigInterface
    {
        unset($this->_certs[$type]);
        return $this;
    }

    /**
     * Get full list of certificates
     *
     * @return  array
     */
    public function getCerts(): array
    {
        return $this->_certs;
    }

    /**
     * Append new push into the array
     *
     * @param   string $line - String with line which must be pushed
     * @return  ConfigInterface
     */
    public function addPush(string $line): ConfigInterface
    {
        $this->_pushes[] = $line;
        return $this;
    }

    /**
     * Remove route line from push array
     *
     * @param   string $line - String with line which must be pushed
     * @return  ConfigInterface
     */
    public function delPush(string $line): ConfigInterface
    {
        unset($this->_pushes[$line]);
        return $this;
    }

    /**
     * Get all pushes from array
     *
     * @return  array
     */
    public function getPushes(): array
    {
        return $this->_pushes;
    }

    /**
     * Add some new parameter to the list of parameters
     *
     * @example $this->addParam('client')->addParam('remote', 'vpn.example.com');
     * @param   string $name - Name of parameter
     * @param   string|bool|null $value - Value of parameter
     * @return  ConfigInterface
     */
    public function add(string $name, $value = null): ConfigInterface
    {
        if (\is_bool($value)) {
            $value = $value ? 'true' : 'false';
        }
        $this->_params[] = ['name' => $name, 'value' => $value];
        return $this;
    }

    /**
     * Get some custom element
     *
     * @param   string|null $name - Name of parameter
     * @return  mixed
     */
    public function get(string $name)
    {
        return $this->_params[$name];
    }

    /**
     * Get full list of parameters, or some custom element
     *
     * @return  array
     */
    public function getParams(): array
    {
        return $this->_params;
    }

    /**
     * Remove some parameter from array by name
     *
     * @param   string $name - Name of parameter
     * @return  ConfigInterface
     */
    public function del(string $name): ConfigInterface
    {
        $this->_params = array_map(
            function($param) use ($name) {
                return ($param['name'] === $name) ? null : $param;
            },
            $this->_params
        );

        return $this;
    }
}
