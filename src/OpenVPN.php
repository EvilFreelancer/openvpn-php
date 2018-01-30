<?php

namespace EvilFreelancer;

class OpenVPN implements Interfaces\OpenVPN
{
    // Array with all certificates
    private $_certs = [];
    // List of lines which must be pushed to clients
    private $_pushes = [];

    /**
     * Type of device, tun or tap
     * @var string
     */
    public $dev;

    /**
     * Type of protocol, udp or tcp
     * @var string
     */
    public $proto;

    /**
     * Remote address ip or hostname
     * @var string
     */
    public $remote;

    /**
     * Port number of remote server or of local instance
     * @var int
     */
    public $port;

    /**
     * Listen ip address or hostname
     * @var string
     */
    public $listen;
    public $nobind;
    public $resolvRetry;

    /**
     * Redirect all IP network traffic originating on client machines to pass through the OpenVPN server
     * @var bool
     */
    public $redirectGateway;
    public $keyDirection;

    /**
     * Encryption protocol
     * @var string
     */
    public $cipher;
    public $tlsCipher;

    /**
     * Persist keys (because we are nobody, so we couldn't read them again)
     * @var bool
     */
    public $persistKey;

    /**
     * Don't close and re open TUN/TAP device
     * @var bool
     */
    public $persistTun;
    public $remoteCertTls;
    public $authUserPass;
    public $authNocache;

    /**
     * For subnetwork 10.8.0.0/24 the server will be the 10.8.0.1 and clients will take the other ips
     * @var string
     */
    public $server;

    /**
     * Downgrade privileges of the daemon
     * @var string
     */
    public $user;

    /**
     * Downgrade privileges of the daemon
     * @var string
     */
    public $group;

    /**
     * Enable compression
     * @var bool
     */
    public $compLzo;

    public $httpProxy;
    public $httpProxyRetry;

    /**
     * "keepalive 10 120" mean - ping every 10 seconds and if after 120 seconds the client doesn't respond we disconnect
     * @var string
     */
    public $keepalive;

    /**
     * Regenerate key each count of seconds (disconnect the client)
     * @var int
     */
    public $renegSec;

    /**
     * Verbosity (0-4), 3 or 4 for a normal utilisation
     * @var int
     */
    public $verb;

    /**
     * Count of messages of the same category
     * @var int
     */
    public $mute;

    /**
     * Log gile where we put the clients status
     * @var string
     */
    public $status;

    /**
     * Log file
     * @var string
     */
    public $logAppend;

    /**
     * Configuration directory of the clients
     * @var string
     */
    public $clientConfigDir;

    /**
     * Allow running external scripts with password in ENV variables
     * @var string
     */
    public $scriptSecurity;

    /**
     * Use the authenticated username as the common name, rather than the common name from the client cert
     * @var bool
     */
    public $usernameAsCommonName;

    /**
     * Client certificate is required
     * @var string
     */
    public $verifyClientCert;

    /**
     * Use the connection script when a user wants to login
     * @var string
     */
    public $authUserPassVerify;

    /**
     * Maximum count of client connections
     * @var int
     */
    public $maxClients;

    /**
     * Run this scripts when the client connects
     * @var string
     */
    public $clientConnect;

    /**
     * Run this scripts when the client connects
     * @var string
     */
    public $clientDisconnect;

    public function getClientConfig(): string
    {
        return $this->generateConfig(false);
    }

    public function getServerConfig(): string
    {
        return $this->generateConfig(true);
    }

    public function generateConfig(bool $server = false): string
    {
        $mode = $server ? 'server' : 'client';
        $this->proto = strcasecmp($this->proto, 'tcp') ? $this->proto . '-' . $mode : $this->proto;

        $config = "# This config was generated automatically\n\n";

        // General
        $config .= "mode $mode\n";
        $config .= !empty($this->dev) ?? "dev " . $this->dev . "\n";
        $config .= !empty($this->proto) ?? "proto " . $this->proto . "\n";
        $config .= !empty($this->remote) ?? "remote " . $this->remote . "\n";
        $config .= !empty($this->listen) ?? "listen " . $this->listen . "\n";
        $config .= !empty($this->port) ?? "port " . $this->port . "\n";
        $config .= !empty($this->redirectGateway) ?? "redirect-gateway\n";
        $config .= !empty($this->httpProxy) ?? "http-proxy " . $this->httpProxy . "\n";
        $config .= !empty($this->nobind) ?? "nobind\n";
        $config .= !empty($this->compLzo) ?? "comp-lzo\n";

        // Keys and certs
        foreach ($this->getCerts() as $key => $value) {
            $config .= (true === $value['load'] && !$server)
                ? "<$key>\n" . $value['content'] . "\n</$key>\n"
                : "$key " . $value['path'] . (($key == 'tls-auth') ? ' ' . $value['option'] : null) . "\n";
        }
        $config .= !empty($this->cipher) ?? "cipher " . $this->cipher . "\n";
        $config .= !empty($this->tlsCipher) ?? "tls-cipher " . $this->tlsCipher . "\n";

        // Network and push
        $config .= !empty($this->server) ?? "server " . $this->server . "\n";
        foreach ($this->getPushes() as $push) {
            $config .= "push " . $push . "\n";
        }
        $config .= !empty($this->keepalive) ?? "keepalive " . $this->keepalive . "\n";
        $config .= !empty($this->renegSec) ?? "reneg-sec " . $this->renegSec . "\n";

        // Security
        $config .= !empty($this->user) ?? "user " . $this->user . "\n";
        $config .= !empty($this->group) ?? "group " . $this->group . "\n";
        $config .= !empty($this->persistKey) ?? "persist-key\n";
        $config .= !empty($this->persistTun) ?? "persist-tun\n";
        $config .= !empty($this->remoteCertTls) ?? "remote-cert-tls " . $this->remoteCertTls . "\n";
        $config .= !empty($this->authUserPass) ?? "auth-user-pass\n";
        $config .= !empty($this->authNocache) ?? "auth-nocache\n";
        $config .= !empty($this->scriptSecurity) ?? "script-security " . $this->scriptSecurity . "\n";

        // Logs
        $config .= !empty($this->verb) ?? "verb " . $this->verb . "\n";
        $config .= !empty($this->mute) ?? "mute " . $this->mute . "\n";
        $config .= !empty($this->status) ?? "status " . $this->status . "\n";
        $config .= !empty($this->logAppend) ?? "log-append " . $this->logAppend . "\n";

        // Authorization
        $config .= !empty($this->usernameAsCommonName) ?? "username-as-common-name\n";
        $config .= !empty($this->verifyClientCert) ?? "verify-client-cert\n";
        $config .= !empty($this->authUserPassVerify) ?? "auth-user-pass-verify " . $this->authUserPassVerify . "\n";
        $config .= !empty($this->maxClients) ?? "max-clients " . $this->maxClients . "\n";
        $config .= !empty($this->clientConnect) ?? "client-connect " . $this->clientConnect . "\n";
        $config .= !empty($this->clientDisconnect) ?? "client-disconnect " . $this->clientDisconnect . "\n";

        return $config;
    }

    public function addCert(string $type, string $path, bool $load = false, int $option = 0): Interfaces\OpenVPN
    {
        $type = mb_strtolower($type);
        $this->_certs[$type]['path'] = !empty($path) ? $path : null;
        $this->_certs[$type]['content'] = (true === $load) ? file_get_contents($path) : null;
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
}
