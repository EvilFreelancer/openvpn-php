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

        foreach ($this->getParams() as $key => $value) {
            $config .= $key . ((\strlen($value) > 0) ? ' ' . $value : '') . "\n";
        }

        $certs = $this->getCerts();
        if (\count($certs) > 0) {
            $config .= "\n### Certificates\n";
            foreach ($this->getCerts() as $key => $value) {
                $config .= isset($value['content'])
                    ? "<$key>\n{$value['content']}\n</$key>\n"
                    : "$key {$value['path']}\n";
            }
        }

        $pushes = $this->getPushes();
        if (\count($pushes) > 0) {
            $config .= "\n### Networking\n";
            foreach ($this->getPushes() as $push) {
                $config .= 'push "' . $push . "\"\n";
            }
        }

        return $config;
    }

    /**
     * Import content of all listed certificates
     */
    public function importCerts()
    {
        foreach ($this->_certs as $cert) {
            $cert['content'] = rtrim(file_get_contents($cert['path']));
        }
    }

    private function throwIfNotInArray($key, $array)
    {
        if (!\in_array($key, $array, true)) {
            throw new \RuntimeException("Key '$key' not in list of allowed [" . implode(',', $array) . ']');
        }
    }

    /**
     * Add new cert into the configuration
     *
     * @param   string $type Type of certificate [ca, cert, key, dh, tls-auth]
     * @param   string $pathOrContent Absolute or relative path to certificate or content of this file
     * @param   bool if content of file is provided
     * @throws  \RuntimeException
     * @return  ConfigInterface
     */
    public function addCert(string $type, string $pathOrContent, bool $isContent = false): ConfigInterface
    {
        $type = mb_strtolower($type);
        $this->throwIfNotInArray($type, self::CERTS);
        if (true === $isContent) {
            $this->_certs[$type]['content'] = $pathOrContent;
        } else {
            $this->_certs[$type]['path'] = $pathOrContent;
        }
        return $this;
    }

    /**
     * Remove selected certificate from array
     *
     * @param   string $type Type of certificate [ca, cert, key, dh, tls-auth]
     * @throws  \RuntimeException
     * @return  ConfigInterface
     */
    public function delCert(string $type): ConfigInterface
    {
        $type = mb_strtolower($type);
        $this->throwIfNotInArray($type, self::CERTS);
        unset($this->_certs[$type]);
        return $this;
    }

    /**
     * Return information about specified certificate
     *
     * @param   string $type
     * @throws  \RuntimeException
     * @return  array
     */
    public function getCert(string $type): array
    {
        $type = mb_strtolower($type);
        $this->throwIfNotInArray($type, self::CERTS);
        return $this->_certs[$type] ?? [];
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
     * @param   string $line String with line which must be pushed
     * @return  ConfigInterface
     */
    public function addPush(string $line): ConfigInterface
    {
        $this->_pushes[] = trim($line, '"');
        return $this;
    }

    /**
     * Remove route line from push array
     *
     * @param   string $line String with line which must be pushed
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
     * Check if value is boolean, if not then return same string
     *
     * @param   mixed $value
     * @return  mixed
     */
    private function isBool($value)
    {
        if (\is_bool($value)) {
            $value = $value ? 'true' : 'false';
        }
        return $value;
    }

    /**
     * Add some new parameter to the list of parameters
     *
     * @example $this->add('client')->add('remote', 'vpn.example.com');
     * @param   string $name Name of parameter
     * @param   string|bool|null $value Value of parameter
     * @return  ConfigInterface
     */
    public function add(string $name, $value = null): ConfigInterface
    {
        $name = mb_strtolower($name);

        // Check if key is certificate or push, or classic parameter
        if (\in_array($name, self::CERTS, true)) {
            $this->addCert($name, $value);
        } elseif ($name === 'push') {
            $this->addPush($value);
        } else {
            $this->_params[$name] = $this->isBool($value);
        }

        return $this;
    }

    /**
     * Get some custom element
     *
     * @param   string|null $name Name of parameter
     * @return  mixed
     */
    public function get(string $name)
    {
        return $this->_params[$name] ?? null;
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
     * @param   string $name Name of parameter
     * @throws  \RuntimeException
     * @return  ConfigInterface
     */
    public function del(string $name): ConfigInterface
    {
        // Check if key is certificate or push, or classic parameter
        if (\in_array($name, self::CERTS, true)) {
            $this->delCert($name);
        } elseif ($name === 'push') {
            throw new \RuntimeException("Not possible to remove push, use 'delPush' instead");
        } else {
            $this->_params = array_map(
                function($param) use ($name) {
                    return ($param['name'] === $name) ? null : $param;
                },
                $this->_params
            );
        }

        return $this;
    }
}
