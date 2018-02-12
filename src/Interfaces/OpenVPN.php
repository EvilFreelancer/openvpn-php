<?php

namespace EvilFreelancer\Interfaces;

interface OpenVPN
{
    /**
     * Append new push into the array
     *
     * @param   string $line - String with line which must be pushed
     * @return  OpenVPN
     */
    public function addPush(string $line): OpenVPN;

    /**
     * Remove route line from push array
     *
     * @param   string $line - String with line which must be pushed
     * @return  OpenVPN
     */
    public function delPush(string $line): OpenVPN;

    /**
     * Get all pushes from array
     *
     * @return  array
     */
    public function getPushes(): array;

    /**
     * Add new cert into the configuration
     *
     * @param   string $type - Type of certificate [ca, cert, key, dh, tls-auth]
     * @param   string $path - Absolute or relative path to certificate
     * @param   bool $load - Read certificate as plain text
     * @param   int $option - Optional parameter (for tls-auth)
     * @return  OpenVPN
     */
    public function addCert(string $type, string $path, bool $load = false, int $option = 0): OpenVPN;

    /**
     * Remove selected certificate from array
     *
     * @param   string $type - Type of certificate [ca, cert, key, dh, tls-auth]
     * @return  OpenVPN
     */
    public function delCert(string $type): OpenVPN;

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
    public function generateConfig(): string;

    /**
     * Add some new parameter to the list of parameters
     *
     * @example $this->addParam('client')->addParam('remote', 'vpn.example.com');
     * @param   string $name - Name of parameter
     * @param   string|bool|null $value - Value of parameter
     * @return  OpenVPN
     */
    public function addParam(string $name, $value = null): OpenVPN;

    /**
     * Get full list of parameters, or some custom element
     *
     * @param   string|null $name - Name of parameter
     * @return  array
     */
    public function getParams(string $name = null): array;

    /**
     * Remove some parameter from array by name
     *
     * @param   string $name - Name of parameter
     * @return  OpenVPN
     */
    public function delParam(string $name): OpenVPN;
}
