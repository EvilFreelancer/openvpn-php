<?php

namespace OpenVPN;

use OpenVPN\Interfaces\ConfigInterface;
use OpenVPN\Interfaces\GeneratorInterface;
use function count;

/**
 * Class Generator
 *
 * @package OpenVPN
 * @since   1.0.0
 */
class Generator implements GeneratorInterface
{
    /**
     * @var \OpenVPN\Interfaces\ConfigInterface
     */
    private $config;

    /**
     * Generator constructor.
     *
     * @param \OpenVPN\Interfaces\ConfigInterface $config
     */
    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * Generate config in array format
     *
     * @return array
     */
    private function generateArray(): array
    {
        // Init the variable
        $config = [];

        // Basic parameters first
        foreach ($this->config->getParameters() as $key => $value) {
            $config[] = $key . ($value !== '' ? ' ' . $value : '');
        }

        // Get all what need for normal work
        $pushes = $this->config->getPushes();
        $routes = $this->config->getRoutes();
        $certs  = $this->config->getCerts();

        // If we have routes or pushes in lists then generate it
        if (count($pushes) || count($routes)) {
            foreach ($routes as $route) {
                $config[] = 'route ' . $route;
            }
            foreach ($pushes as $push) {
                $config[] = 'push "' . $push . '"';
            }
        }

        // Certs should be below everything, due embedded keys and certificates
        if (count($certs) > 0) {
            foreach ($this->config->getCerts() as $key => $value) {
                $config[] .= isset($value['content'])
                    ? "<$key>\n{$value['content']}\n</$key>"
                    : "$key {$value['path']}";
            }
        }

        return $config;
    }

    /**
     * Generate config in JSON format
     *
     * @return string
     */
    private function generateJson(): string
    {
        $config = $this->generateArray();
        return json_encode($config, JSON_PRETTY_PRINT);
    }

    /**
     * Generate config in RAW format
     *
     * @return string
     */
    private function generateRaw(): string
    {
        $config = $this->generateArray();
        return implode(PHP_EOL, $config);
    }

    /**
     * Generate config by parameters in memory
     *
     * @param string $type Type of generated config: raw (default), json, array
     *
     * @return array|string|null
     */
    public function generate(string $type = 'raw')
    {
        if ($type === 'raw') {
            return $this->generateRaw();
        }

        if ($type === 'json') {
            return $this->generateJson();
        }

        if ($type === 'array') {
            return $this->generateArray();
        }

        return null;
    }
}
