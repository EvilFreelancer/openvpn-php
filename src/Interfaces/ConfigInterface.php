<?php

namespace OpenVPN\Interfaces;

interface ConfigInterface
{
    /**
     * List of types of certs, for validation
     */
    const CERTS = ['ca', 'cert', 'key', 'dh', 'tls-auth'];

    /**
     * Append new push into the array
     *
     * @param   string $line String with line which must be pushed
     * @return  ConfigInterface
     */
    public function addPush(string $line): ConfigInterface;

    /**
     * Remove route line from push array
     *
     * @param   string $line String with line which must be pushed
     * @return  ConfigInterface
     */
    public function delPush(string $line): ConfigInterface;

    /**
     * Get all pushes from array
     *
     * @return  array
     */
    public function getPushes(): array;

    /**
     * Add new cert into the configuration
     *
     * @param   string $type Type of certificate [ca, cert, key, dh, tls-auth]
     * @param   string $path Absolute or relative path to certificate
     * @return  ConfigInterface
     */
    public function addCert(string $type, string $path): ConfigInterface;

    /**
     * Remove selected certificate from array
     *
     * @param   string $type Type of certificate [ca, cert, key, dh, tls-auth]
     * @return  ConfigInterface
     */
    public function delCert(string $type): ConfigInterface;

    /**
     * Get full list of certificates
     *
     * @return  array
     */
    public function getCerts(): array;

    /**
     * Generate config by parameters in memory
     *
     * @return  string
     */
    public function generate(): string;

    /**
     * Add some new parameter to the list of parameters
     *
     * @example $this->addParam('client')->addParam('remote', 'vpn.example.com');
     * @param   string $name Name of parameter
     * @param   string|bool|null $value Value of parameter
     * @return  ConfigInterface
     */
    public function add(string $name, $value = null): ConfigInterface;

    /**
     * Get some custom element
     *
     * @param   string|null $name Name of parameter
     * @return  mixed
     */
    public function get(string $name);

    /**
     * Get full list of parameters
     *
     * @return  array
     */
    public function getParams(): array;

    /**
     * Remove some parameter from array by name
     *
     * @param   string $name Name of parameter
     * @return  ConfigInterface
     */
    public function del(string $name): ConfigInterface;
}
