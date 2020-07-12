<?php

namespace OpenVPN;

use RuntimeException;

/**
 * Class Helpers
 *
 * @package OpenVPN
 * @since   1.0.0
 */
class Helpers
{
    /**
     * Check if provided type of certs is allowed
     *
     * @param string $key
     *
     * @throws \RuntimeException
     */
    public static function isCertAllowed(string $key): void
    {
        if (!in_array($key, Config::ALLOWED_TYPES_OF_CERTS, true)) {
            throw new RuntimeException("Key '$key' not in list of allowed [" . implode(',', Config::ALLOWED_TYPES_OF_CERTS) . ']');
        }
    }

    /**
     * Convert string like "makeMeHappy" to "make-me-happy"
     *
     * @param string $input
     *
     * @return string
     */
    public static function decamelize(string $input): string
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $result = $matches[0];
        foreach ($result as &$match) {
            $match = $match === strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('-', $result);
    }
}
