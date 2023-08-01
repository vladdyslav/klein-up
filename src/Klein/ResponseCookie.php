<?php
/**
 * Klein (klein.php) - A fast & flexible router for PHP
 *
 * @author      Chris O'Hara <cohara87@gmail.com>
 * @author      Trevor Suarez (Rican7) (contributor and v2 refactorer)
 * @copyright   (c) Chris O'Hara
 * @link        https://github.com/klein/klein.php
 * @license     MIT
 */

namespace Klein;

/**
 * ResponseCookie
 *
 * Class to represent an HTTP response cookie
 */
class ResponseCookie
{

    /**
     * Class properties
     */

    /**
     * The name of the cookie
     *
     * @type string|null
     */
    protected string|null $name = null;

    /**
     * The string "value" of the cookie
     *
     * @type string|null
     */
    protected string|null $value = null;

    /**
     * The date/time that the cookie should expire
     *
     * Represented by a Unix "Timestamp"
     *
     * @type int|null
     */
    protected int|null $expire = null;

    /**
     * The path on the server that the cookie will
     * be available on
     *
     * @type string|null
     */
    protected string|null $path = null;

    /**
     * The domain that the cookie is available to
     *
     * @type string|null
     */
    protected string|null $domain = null;

    /**
     * Whether the cookie should only be transferred
     * over an HTTPS connection or not
     *
     * @type boolean|null
     */
    protected bool|null $secure;

    /**
     * Whether the cookie will be available through HTTP
     * only (not available to be accessed through
     * client-side scripting languages like JavaScript)
     *
     * @type boolean|null
     */
    protected bool|null $http_only;


    /**
     * Methods
     */

    /**
     * Constructor
     *
     * @param string $name         The name of the cookie
     * @param string|null $value        The value to set the cookie with
     * @param int|null $expire       The time that the cookie should expire
     * @param string|null $path         The path of which to restrict the cookie
     * @param string|null $domain       The domain of which to restrict the cookie
     * @param boolean|null $secure       Flag of whether the cookie should only be sent over an HTTPS connection
     * @param boolean|null $http_only    Flag of whether the cookie should only be accessible over the HTTP protocol
     */
    public function __construct(
        string      $name,
        string      $value = null,
        int         $expire = null,
        string      $path = null,
        string      $domain = null,
        bool|null   $secure = false,
        bool|null   $http_only = false
    ) {
        // Initialize our properties
        $this->setName($name);
        $this->setValue($value);
        $this->setExpire($expire);
        $this->setPath($path);
        $this->setDomain($domain);
        $this->setSecure($secure);
        $this->setHttpOnly($http_only);
    }

    /**
     * Gets the cookie's name
     *
     * @return string|null
     */
    public function getName(): string|null
    {
        return $this->name;
    }

    /**
     * Sets the cookie's name
     *
     * @param string $name
     * @return ResponseCookie
     */
    public function setName(string $name): ResponseCookie
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets the cookie's value
     *
     * @return string|null
     */
    public function getValue(): string|null
    {
        return $this->value;
    }

    /**
     * Sets the cookie's value
     *
     * @param string|null $value
     * @return ResponseCookie
     */
    public function setValue(string|null $value): ResponseCookie
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Gets the cookie's expire time
     *
     * @return int|null
     */
    public function getExpire(): int|null
    {
        return $this->expire;
    }

    /**
     * Sets the cookie's expire time
     *
     * The time should be an integer
     * representing a Unix timestamp
     *
     * @param int|null $expire
     * @return ResponseCookie
     */
    public function setExpire(int|null $expire): ResponseCookie
    {
        $this->expire = $expire;
        return $this;
    }

    /**
     * Gets the cookie's path
     *
     * @return string|null
     */
    public function getPath(): string|null
    {
        return $this->path;
    }

    /**
     * Sets the cookie's path
     *
     * @param string|null $path
     * @return ResponseCookie
     */
    public function setPath(string|null $path): ResponseCookie
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Gets the cookie's domain
     *
     * @return string|null
     */
    public function getDomain(): string|null
    {
        return $this->domain;
    }

    /**
     * Sets the cookie's domain
     *
     * @param string|null $domain
     * @return ResponseCookie
     */
    public function setDomain(string|null $domain): ResponseCookie
    {
        $this->domain = $domain;
        return $this;
    }

    /**
     * Gets the cookie's secure only flag
     *
     * @return bool|null
     */
    public function getSecure(): bool|null
    {
        return $this->secure;
    }

    /**
     * Sets the cookie's secure only flag
     *
     * @param boolean|null $secure
     * @return ResponseCookie
     */
    public function setSecure(bool|null $secure): ResponseCookie
    {
        $this->secure = $secure;

        return $this;
    }

    /**
     * Gets the cookie's HTTP only flag
     *
     * @return bool|null
     */
    public function getHttpOnly(): bool|null
    {
        return $this->http_only;
    }

    /**
     * Sets the cookie's HTTP only flag
     *
     * @param boolean $http_only
     * @return ResponseCookie
     */
    public function setHttpOnly(bool $http_only): ResponseCookie
    {
        $this->http_only = $http_only;
        return $this;
    }
}
