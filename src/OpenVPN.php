<?php

namespace EvilFreelancer;

class OpenVPN implements Interfaces\OpenVPN
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

    public function generateConfig(): string
    {
        // Init the variable
        $config = '';

        foreach ($this->getParams() as $param) {
            $config .= $param['name'] . ((mb_strlen($param['value']) > 0) ? ' ' . (string)$param['value'] : '') . "\n";
        }

        // Keys and certs
        foreach ($this->getCerts() as $key => $value) {
            $config .= !empty($value['content'])
                ? "<$key>\n" . $value['content'] . "\n</$key>\n"
                : "$key " . $value['path'] . (($key == 'tls-auth') ? ' ' . $value['option'] : null) . "\n";
        }

        // Network and push
        foreach ($this->getPushes() as $push) {
            $config .= "push \"" . $push . "\"\n";
        }

        return $config;
    }

    public function addCert(string $type, string $path, bool $load = false, int $option = 0): Interfaces\OpenVPN
    {
        $type = mb_strtolower($type);
        $this->_certs[$type]['path'] = !empty($path) ? $path : null;
        $this->_certs[$type]['content'] = (true === $load) ? rtrim(file_get_contents($path)) : null;
        $this->_certs[$type]['option'] = !empty($options) ? $options : null;

        return $this;
    }

    public function delCert(string $type): Interfaces\OpenVPN
    {
        unset($this->_certs[$type]);
        return $this;
    }

    public function getCerts(): array
    {
        return $this->_certs;
    }

    public function addPush(string $line): Interfaces\OpenVPN
    {
        $this->_pushes[] = "$line";
        return $this;
    }

    public function delPush(string $line): Interfaces\OpenVPN
    {
        unset($this->_pushes[$line]);
        return $this;
    }

    public function getPushes(): array
    {
        return $this->_pushes;
    }

    public function addParam(string $name, $value = null): Interfaces\OpenVPN
    {
        if (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        }
        $this->_params[] = ['name' => $name, 'value' => $value];
        return $this;
    }

    public function getParams(string $name = null): array
    {
        return !empty($name)
            ? array_search(['name' => $name], $this->_params)
            : $this->_params;
    }

    public function delParam(string $name): Interfaces\OpenVPN
    {
        $this->_params = array_map(
            function ($param) use ($name) {
                return ($param['name'] == $name) ? null : $param;
            },
            $this->_params
        );

        return $this;
    }
}
