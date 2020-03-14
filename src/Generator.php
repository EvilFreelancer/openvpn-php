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
     * Generate config by parameters in memory
     *
     * @return string
     */
    public function generate(): string
    {
        // Init the variable
        $config = '';

        // Basic parameters first
        foreach ($this->config->getParameters() as $key => $value) {
            $config .= $key . ($value !== '' ? ' ' . $value : '') . "\n";
        }

        // Get all what need for normal work
        $pushes = $this->config->getPushes();
        $routes = $this->config->getRoutes();
        $certs  = $this->config->getCerts();

        // If we have routes or pushes in lists then generate it
        if (count($pushes) || count($routes)) {
            $config .= "\n### Networking\n";
            foreach ($routes as $route) {
                $config .= 'route ' . $route . "\n";
            }
            foreach ($pushes as $push) {
                $config .= 'push "' . $push . "\"\n";
            }
        }

        // Certs should be below everything, due embedded keys and certificates
        if (count($certs) > 0) {
            $config .= "\n### Certificates\n";
            foreach ($this->config->getCerts() as $key => $value) {
                $config .= isset($value['content'])
                    ? "<$key>\n{$value['content']}\n</$key>\n"
                    : "$key {$value['path']}\n";
            }
        }

        return $config;
    }
}
