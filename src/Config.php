<?php

namespace OpenVPN;

use OpenVPN\Interfaces\ConfigInterface;
use OpenVPN\Interfaces\GeneratorInterface;
use RuntimeException;
use function in_array;
use function is_bool;

/**
 * Class Config
 *
 * @property string    $modeSet             OpenVPN major mode: p2p, server
 * @property string    $local               Local host name or IP address
 * @property string    $remote              Remote host name or IP address
 * @property bool|null $remoteRandom        When multiple --remote address/ports are specified, initially randomize the order of the list
 * @property string    $proto               Protocol for communicating with remote host: tcp, udp, tcp-client
 * @property integer   $connectRetry        For --proto tcp-client, take n as the number of seconds to wait between connection retries (default=5)
 * @property string    $httpProxy           Connect to remote host through an HTTP proxy
 * @property bool|null $httpProxyRetry      Retry indefinitely on HTTP proxy errors. If an HTTP proxy error occurs, simulate a SIGUSR1 reset.
 * @property integer   $httpProxyTimeout    Set proxy timeout to n seconds, default=5.
 * @property string    $httpProxyOption     Set extended HTTP proxy options
 * @property string    $socksProxy          Connect to remote host through a Socks5 proxy
 * @property bool|null $socksProxyRetry     Retry indefinitely on Socks proxy errors. If a Socks proxy error occurs, simulate a SIGUSR1 reset.
 * @property string    $resolvRetry         If hostname resolve fails for --remote, retry resolve for n seconds before failing
 * @property bool|null $float               Allow remote peer to change its IP address and/or port number, such as due to DHCP
 * @property string    $ipchange            Execute shell command, format: cmd ip_address port_number
 * @property integer   $port                TCP/UDP port number for both local and remote: 1194
 * @property integer   $lport               TCP/UDP port number for local
 * @property integer   $rport               TCP/UDP port number for remote
 * @property bool|null $nobind              Do not bind to local address and port
 * @property string    $dev                 TUN/TAP virtual network device: tunX, tapX, null
 * @property string    $devType             Which device type are we using? device-type should be tun or tap
 * @property bool|null $tunIpv6             Build a tun link capable of forwarding IPv6 traffic
 * @property string    $devNode             Explicitly set the device node rather than using /dev/net/tun, /dev/tun, /dev/tap, etc.
 * @property string    $ifconfig            Set TUN/TAP adapter IP address of the local VPN endpoint.
 * @property bool|null $ifconfigNoexec      Don’t actually execute ifconfig/netsh commands, instead pass –ifconfig parameters to scripts using environmental variables.
 * @property bool|null $ifconfigNowarn      Don’t output an options consistency check warning
 * @property string    $ifconfigPool        Set aside a pool of subnets to be dynamically allocated to connecting clients, similar to a DHCP server.
 * @property string    $ifconfigPoolPersist Persist/unpersist ifconfig-pool data to file, at secondsintervals (default=600),
 * @property string    $cipher              Encrypt packets with cipher algorithm alg. The default is BF-CBC,an abbreviation for Blowfish in Cipher Block Chaining mode.
 * @property bool|null $redirectGateway
 * @property integer   $keyDirection
 * @property string    $remoteCertTls
 * @property string    $auth                Authenticate packets with HMAC using message digest algorithm alg. (The default is SHA1).
 * @property bool|null $authUserPass
 * @property bool|null $authNocache
 * @property string    $authUserPassVerify  Path to login script
 * @property bool|null $duplicateCn         You may need this if everyone is using same certificate
 * @property bool|null $persistKey
 * @property bool|null $persistTun
 * @property bool|null $compLzo             Use fast LZO compression — may add up to 1 byte per packet for incompressible data.
 * @property bool|null $compNoadapt         When used in conjunction with –comp-lzo, this option will disable OpenVPN’s adaptive compression algorithm.
 * @property integer   $verb                Set output verbosity to n(default=1). Each level shows all info from the previous levels: 0,1,2 ... 11
 * @property string    $server              A helper directive designed to simplify the configuration of OpenVPN’s server mode.
 * @property string    $serverBridge        A helper directive similar to --server which is designed to simplify the configuration of OpenVPN’s server mode in ethernet bridging configurations.
 * @property string    $keepalive
 * @property integer   $renegSec
 * @property string    $user
 * @property string    $group
 * @property string    $mute
 * @property string    $status
 * @property string    $logAppend
 * @property string    $clientConfigDir
 * @property string    $scriptSecurity
 * @property string    $usernameAsCommonName
 * @property string    $verifyClientCert
 *
 * @package OpenVPN
 */
class Config implements ConfigInterface, GeneratorInterface
{
    /**
     * Array with all certificates
     *
     * @var array
     */
    private $certs = [];

    /**
     * List of all routes available on server
     *
     * @var array
     */
    private $routes = [];

    /**
     * List of lines which must be pushed to clients
     *
     * @var array
     */
    private $pushes = [];

    /**
     * All parameters added via addParam method
     *
     * @var array
     */
    private $parameters = [];

    /**
     * Config constructor.
     *
     * @param array $parameters List of default parameters
     */
    public function __construct(array $parameters = [])
    {
        $this->setParams($parameters);
    }

    /**
     * Alias for client line of config
     *
     * @return \OpenVPN\Interfaces\ConfigInterface
     */
    public function client(): ConfigInterface
    {
        return $this->set('client');
    }

    /**
     * Import content of all listed certificates
     *
     * @return void
     */
    public function loadCertificates(): void
    {
        foreach ($this->certs as &$cert) {
            $cert['content'] = rtrim(file_get_contents($cert['path']));
        }
    }

    /**
     * Alias to setCert
     *
     * @deprecated TODO: Delete in future releases
     */
    public function addCert(string $type, string $pathOrContent, bool $isContent = false): ConfigInterface
    {
        return $this->setCert($type, $pathOrContent, $isContent);
    }

    /**
     * Add new cert into the configuration
     *
     * @param string    $type      Type of certificate [ca, cert, key, dh, tls-auth]
     * @param string    $path      Absolute or relative path to certificate or content of this file
     * @param bool|null $isContent If true, then script will try to load file from dist by $path
     *
     * @return \OpenVPN\Interfaces\ConfigInterface
     * @throws \RuntimeException
     */
    public function setCert(string $type, string $path, bool $isContent = null): ConfigInterface
    {
        $type = mb_strtolower($type);
        Helpers::isCertAllowed($type);
        if (true === $isContent) {
            $this->certs[$type]['content'] = $path;
        } else {
            $this->certs[$type]['path'] = $path;
        }
        return $this;
    }

    /**
     * Return information about specified certificate
     *
     * @param string $type
     *
     * @return array
     * @throws \RuntimeException
     */
    public function getCert(string $type): array
    {
        $type = mb_strtolower($type);
        Helpers::isCertAllowed($type);
        return $this->certs[$type] ?? [];
    }

    /**
     * Alias to setPush
     *
     * @deprecated TODO: Delete in future releases
     */
    public function addPush(string $line): ConfigInterface
    {
        return $this->setPush($line);
    }

    /**
     * Append new push into the array
     *
     * @param string $line String with line which must be pushed
     *
     * @return \OpenVPN\Interfaces\ConfigInterface
     */
    public function setPush(string $line): ConfigInterface
    {
        $this->pushes[] = trim($line, '"');
        return $this;
    }

    /**
     * Remove route line from push array
     *
     * @param string $line String with line which must be pushed
     *
     * @return \OpenVPN\Interfaces\ConfigInterface
     */
    public function unsetPush(string $line): ConfigInterface
    {
        unset($this->pushes[$line]);
        return $this;
    }

    /**
     * Append new route into the array
     *
     * @param string $line String with route
     *
     * @return \OpenVPN\Interfaces\ConfigInterface
     */
    public function setRoute(string $line): ConfigInterface
    {
        $this->routes[] = trim($line, '"');
        return $this;
    }

    /**
     * Remove route line from routes array
     *
     * @param string $line String with route
     *
     * @return \OpenVPN\Interfaces\ConfigInterface
     */
    public function unsetRoute(string $line): ConfigInterface
    {
        unset($this->routes[$line]);
        return $this;
    }

    /**
     * Alias to set
     *
     * @deprecated TODO: Delete in future releases
     */
    public function add(string $name, $value = null): ConfigInterface
    {
        return $this->set($name, $value);
    }

    /**
     * Add some new parameter to the list of parameters
     *
     * @param string           $name  Name of parameter
     * @param string|bool|null $value Value of parameter
     *
     * @return \OpenVPN\Interfaces\ConfigInterface
     * @example $this->add('client')->add('remote', 'vpn.example.com');
     */
    public function set(string $name, $value = null): ConfigInterface
    {
        $name = mb_strtolower($name);

        // Check if key is certificate or push, or classic parameter
        if (in_array($name, self::ALLOWED_TYPES_OF_CERTS, true)) {
            return $this->setCert($name, $value);
        }

        // If is push then use add push method
        if ($name === 'push') {
            return $this->setPush($value);
        }

        // If is push then use add push method
        if ($name === 'route') {
            return $this->setRoute($value);
        }

        // Check if provided value is boolean and if it's true, then set null (that mean parameter without value)
        if (is_bool($value) && $value) {
            if ($value) {
                $value = null;
            } else {
                // If false then skip this step
                return $this;
            }
        }

        // Set new value
        $this->parameters[$name] = $value;

        return $this;
    }

    /**
     * Get some custom element
     *
     * @param string|null $name Name of parameter
     *
     * @return mixed
     */
    public function get(string $name)
    {
        return $this->parameters[$name] ?? null;
    }

    /**
     * Generate config by parameters in memory
     *
     * @param string $type Type of generated config: raw (default), json
     *
     * @return array|string|null
     */
    public function generate(string $type = 'raw')
    {
        $generator = new Generator($this);
        return $generator->generate($type);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function __isset(string $name): bool
    {
        // Inform about deleting push
        if ($name === 'push') {
            throw new RuntimeException("Not possible to remove push, use 'unsetPush' instead");
        }

        // Inform about deleting route
        if ($name === 'route') {
            throw new RuntimeException("Not possible to remove route, use 'unsetRoute' instead");
        }

        return isset($this->parameters[$name]);
    }

    /**
     * @param string                   $name
     * @param string|bool|integer|null $value
     */
    public function __set(string $name, $value = null): void
    {
        $name = Helpers::decamelize($name);
        $this->set($name, $value);
    }

    /**
     * @param string $name
     *
     * @return string|bool|null
     */
    public function __get(string $name)
    {
        return $this->get($name);
    }

    /**
     * Remove some parameter from array by name
     *
     * @param string $name Name of parameter
     *
     * @return void
     * @throws \RuntimeException
     */
    public function __unset(string $name)
    {
        // Inform about deleting push
        if ($name === 'push') {
            throw new RuntimeException("Not possible to remove push, use 'unsetPush' instead");
        }

        // Inform about deleting route
        if ($name === 'route') {
            throw new RuntimeException("Not possible to remove route, use 'unsetRoute' instead");
        }

        // Check if key is certificate or push, or classic parameter
        if (in_array($name, self::ALLOWED_TYPES_OF_CERTS, true)) {
            $this->unsetCert($name);
            return;
        }

        // Update list of parameters
        $this->parameters = array_map(
            static function ($param) use ($name) {
                return ($param['name'] === $name) ? null : $param;
            },
            $this->parameters
        );
    }

    /**
     * Remove selected certificate from array
     *
     * @param string $type Type of certificate [ca, cert, key, dh, tls-auth]
     *
     * @return \OpenVPN\Interfaces\ConfigInterface
     * @throws \RuntimeException
     */
    public function unsetCert(string $type): ConfigInterface
    {
        $type = mb_strtolower($type);
        Helpers::isCertAllowed($type);
        unset($this->certs[$type]);
        return $this;
    }

    /**
     * Set scope of certs
     *
     * @param \OpenVPN\Types\Cert[] $certs
     * @param bool                  $loadCertificates
     *
     * @return \OpenVPN\Interfaces\ConfigInterface
     */
    public function setCerts(array $certs, bool $loadCertificates = false): ConfigInterface
    {
        // Pass list of certs from array to variable
        foreach ($certs as $type => $path) {
            $this->setCert($type, $path);
        }

        // If need to load content of files from disk
        if ($loadCertificates) {
            $this->loadCertificates();
        }

        return $this;
    }

    /**
     * Set scope of unique pushes
     *
     * @param \OpenVPN\Types\Push[] $pushes
     *
     * @return \OpenVPN\Interfaces\ConfigInterface
     */
    public function setPushes(array $pushes): ConfigInterface
    {
        foreach ($pushes as $push) {
            $this->setPush($push);
        }

        return $this;
    }

    /**
     * Set scope of unique routes
     *
     * @param \OpenVPN\Types\Route[] $routes
     *
     * @return \OpenVPN\Interfaces\ConfigInterface
     */
    public function setRoutes(array $routes): ConfigInterface
    {
        foreach ($routes as $route) {
            $this->setRoute($route);
        }

        return $this;
    }

    /**
     * Set scope of unique parameters
     *
     * @param \OpenVPN\Types\Parameter[] $parameters
     *
     * @return \OpenVPN\Interfaces\ConfigInterface
     */
    public function setParams(array $parameters): ConfigInterface
    {
        foreach ($parameters as $name => $value) {
            $this->set($name, $value);
        }

        return $this;
    }

    /**
     * Export array of all certificates
     *
     * @return array
     */
    public function getCerts(): array
    {
        return $this->certs;
    }

    /**
     * Export array of all pushes
     *
     * @return array
     */
    public function getPushes(): array
    {
        return $this->pushes;
    }

    /**
     * Export array of all routes
     *
     * @return array
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * Export array of all parameters
     *
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
}
