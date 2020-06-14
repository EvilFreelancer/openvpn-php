<?php

namespace OpenVPN\Interfaces;

interface ConfigInterface
{
    /**
     * List of types of certs, for validation
     */
    public const ALLOWED_TYPES_OF_CERTS = ['ca', 'cert', 'key', 'dh', 'tls-auth', 'secret', 'pkcs12'];

    /**
     * Alias for client line of config
     *
     * @return \OpenVPN\Interfaces\ConfigInterface
     */
    public function client(): ConfigInterface;

    /**
     * Import content of all listed certificates
     *
     * @return void
     */
    public function loadCertificates(): void;

    /**
     * Append new push into the array
     *
     * @param string $line String with line which must be pushed
     *
     * @return \OpenVPN\Interfaces\ConfigInterface
     */
    public function setPush(string $line): ConfigInterface;

    /**
     * Remove route line from push array
     *
     * @param string $line String with line which must be pushed
     *
     * @return \OpenVPN\Interfaces\ConfigInterface
     */
    public function unsetPush(string $line): ConfigInterface;

    /**
     * Add new cert into the configuration
     *
     * @param string $type Type of certificate [ca, cert, key, dh, tls-auth]
     * @param string $path Absolute or relative path to certificate
     *
     * @return \OpenVPN\Interfaces\ConfigInterface
     * @throws \RuntimeException
     */
    public function setCert(string $type, string $path): ConfigInterface;

    /**
     * Return information about specified certificate
     *
     * @param string $type
     *
     * @return array
     * @throws \RuntimeException
     */
    public function getCert(string $type): array;

    /**
     * Remove selected certificate from array
     *
     * @param string $type Type of certificate [ca, cert, key, dh, tls-auth]
     *
     * @return \OpenVPN\Interfaces\ConfigInterface
     * @throws \RuntimeException
     */
    public function unsetCert(string $type): ConfigInterface;

    /**
     * Append new route into the array
     *
     * @param string $line String with route
     *
     * @return \OpenVPN\Interfaces\ConfigInterface
     */
    public function setRoute(string $line): ConfigInterface;

    /**
     * Remove route line from routes array
     *
     * @param string $line String with route
     *
     * @return \OpenVPN\Interfaces\ConfigInterface
     */
    public function unsetRoute(string $line): ConfigInterface;

    /**
     * Append new push into the array
     *
     * @param string $line String with line which must be added as remote
     *
     * @return \OpenVPN\Interfaces\ConfigInterface
     */
    public function setRemote(string $line): ConfigInterface;

    /**
     * Remove remote line from remotes array
     *
     * @param string $line String with line which must be added as remote
     *
     * @return \OpenVPN\Interfaces\ConfigInterface
     */
    public function unsetRemote(string $line): ConfigInterface;

    /**
     * Add some new parameter to the list of parameters
     *
     * @param string           $name  Name of parameter
     * @param string|bool|null $value Value of parameter
     *
     * @return \OpenVPN\Interfaces\ConfigInterface
     * @example $this->add('client')->add('remote', 'vpn.example.com');
     */
    public function set(string $name, $value = null): ConfigInterface;

    /**
     * Get some custom element
     *
     * @param string|null $name Name of parameter
     *
     * @return mixed
     */
    public function get(string $name);

    /**
     * Set scope of certs
     *
     * @param \OpenVPN\Types\Cert[] $certs
     * @param bool                  $loadCertificates
     *
     * @return \OpenVPN\Interfaces\ConfigInterface
     */
    public function setCerts(array $certs, bool $loadCertificates = false): ConfigInterface;

    /**
     * Set scope of unique pushes
     *
     * @param \OpenVPN\Types\Push[] $pushes
     *
     * @return \OpenVPN\Interfaces\ConfigInterface
     */
    public function setPushes(array $pushes): ConfigInterface;

    /**
     * Set scope of unique routes
     *
     * @param \OpenVPN\Types\Route[] $routes
     *
     * @return \OpenVPN\Interfaces\ConfigInterface
     */
    public function setRoutes(array $routes): ConfigInterface;

    /**
     * Set scope of unique remotes
     *
     * @param \OpenVPN\Types\Remote[] $remotes
     *
     * @return \OpenVPN\Interfaces\ConfigInterface
     */
    public function setRemotes(array $remotes): ConfigInterface;

    /**
     * Set scope of unique parameters
     *
     * @param \OpenVPN\Types\Parameter[] $parameters
     *
     * @return \OpenVPN\Interfaces\ConfigInterface
     */
    public function setParams(array $parameters): ConfigInterface;

    /**
     * Export array of all certificates
     *
     * @return array
     */
    public function getCerts(): array;

    /**
     * Export array of all pushes
     *
     * @return array
     */
    public function getPushes(): array;

    /**
     * Export array of all routes
     *
     * @return array
     */
    public function getRoutes(): array;

    /**
     * Export array of all remotes
     *
     * @return array
     */
    public function getRemotes(): array;

    /**
     * Export array of all parameters
     *
     * @return array
     */
    public function getParameters(): array;
}
