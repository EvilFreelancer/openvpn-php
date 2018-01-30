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
     * Generate server config
     *
     * @return string
     */
    public function getServerConfig(): string;

    /**
     * Generate client config
     *
     * @return string
     */
    public function getClientConfig(): string;

    /**
     * Generate config by parameters in memory
     *
     * @param   bool $server - Mode of work, client by default default
     * @return  string
     */
    public function generateConfig(bool $server = false): string;
}
