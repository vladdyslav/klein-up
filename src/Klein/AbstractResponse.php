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

use Klein\DataCollection\HeaderDataCollection;
use Klein\DataCollection\ResponseCookieDataCollection;
use Klein\Exceptions\LockedResponseException;
use Klein\Exceptions\ResponseAlreadySentException;

/**
 * AbstractResponse
 */
abstract class AbstractResponse
{

    /**
     * Properties
     */

    /**
     * The default response HTTP status code
     *
     * @type int
     */
    protected static int $default_status_code = 200;

    /**
     * The HTTP version of the response
     *
     * @type string
     */
    protected string $protocol_version = '1.1';

    /**
     * The response body
     *
     * @type string|null
     */
    protected string|null $body = null;

    /**
     * HTTP response status
     *
     * @type HttpStatus
     */
    protected HttpStatus $status;

    /**
     * HTTP response headers
     *
     * @type HeaderDataCollection
     */
    protected HeaderDataCollection $headers;

    /**
     * HTTP response cookies
     *
     * @type ResponseCookieDataCollection
     */
    protected ResponseCookieDataCollection $cookies;

    /**
     * Whether the response is "locked" from
     * any further modification
     *
     * @type boolean
     */
    protected bool $locked = false;

    /**
     * Whether the response has been sent
     *
     * @type boolean
     */
    protected bool $sent = false;

    /**
     * Whether the response has been chunked or not
     *
     * @type boolean
     */
    public bool $chunked = false;


    /**
     * Methods
     */

    /**
     * Constructor
     *
     * Create a new AbstractResponse object with a dependency injected Headers instance
     *
     * @param string|null $body          The response body's content
     * @param int|null $status_code      The status code
     * @param array $headers        The response header "hash"
     */
    public function __construct(string|null $body = '', int $status_code = null, array $headers = array())
    {
        $status_code   = $status_code ?: static::$default_status_code;

        // Set our body and code using our internal methods
        $this->body($body);
        $this->code($status_code);

        $this->headers = new HeaderDataCollection($headers);
        $this->cookies = new ResponseCookieDataCollection();
    }

    /**
     * Get (or set) the HTTP protocol version
     *
     * Simply calling this method without any arguments returns the current protocol version.
     * Calling with an integer argument, however, attempts to set the protocol version to what
     * was provided by the argument.
     *
     * @param string|null $protocol_version
     * @return string|AbstractResponse
     */
    public function protocolVersion(string $protocol_version = null): AbstractResponse|string
    {
        if (null !== $protocol_version) {
            // Require that the response be unlocked before changing it
            $this->requireUnlocked();

            $this->protocol_version = $protocol_version;

            return $this;
        }

        return $this->protocol_version;
    }

    /**
     * Get (or set) the response's body content
     *
     * Simply calling this method without any arguments returns the current response body.
     * Calling with an argument, however, sets the response body to what was provided by the argument.
     *
     * @param string|null $body  The body content string
     * @return string|null|AbstractResponse
     */
    public function body(string|null $body = null): AbstractResponse|string|null
    {
        if (null !== $body) {
            // Require that the response be unlocked before changing it
            $this->requireUnlocked();

            $this->body = $body;

            return $this;
        }

        return $this->body;
    }

    /**
     * Returns the status object
     *
     * @return HttpStatus
     */
    public function status(): HttpStatus
    {
        return $this->status;
    }

    /**
     * Returns the headers collection
     *
     * @return HeaderDataCollection
     */
    public function headers(): HeaderDataCollection
    {
        return $this->headers;
    }

    /**
     * Returns the cookies collection
     *
     * @return ResponseCookieDataCollection
     */
    public function cookies(): ResponseCookieDataCollection
    {
        return $this->cookies;
    }

    /**
     * Get (or set) the HTTP response code
     *
     * Simply calling this method without any arguments returns the current response code.
     * Calling with an integer argument, however, attempts to set the response code to what
     * was provided by the argument.
     *
     * @param int|null $code     The HTTP status code to send
     * @return int|AbstractResponse
     */
    public function code(int|null $code = null): AbstractResponse|int
    {
        if (null !== $code) {
            // Require that the response be unlocked before changing it
            $this->requireUnlocked();

            $this->status = new HttpStatus($code);

            return $this;
        }

        return $this->status->getCode();
    }

    /**
     * Prepend a string to the response's content body
     *
     * @param string $content   The string to prepend
     * @return AbstractResponse
     */
    public function prepend(string $content): AbstractResponse
    {
        // Require that the response be unlocked before changing it
        $this->requireUnlocked();

        $this->body = $content . $this->body;

        return $this;
    }

    /**
     * Append a string to the response's content body
     *
     * @param string|null $content   The string to append
     * @return AbstractResponse
     */
    public function append(string|null $content): AbstractResponse
    {
        // Require that the response be unlocked before changing it
        $this->requireUnlocked();

        $this->body .= $content;

        return $this;
    }

    /**
     * Check if the response is locked
     *
     * @return boolean
     */
    public function isLocked(): bool
    {
        return $this->locked;
    }

    /**
     * Require that the response is unlocked
     *
     * Throws an exception if the response is locked,
     * preventing any methods from mutating the response
     * when its locked
     *
     * @throws LockedResponseException  If the response is locked
     * @return AbstractResponse
     */
    public function requireUnlocked(): AbstractResponse
    {
        if ($this->isLocked()) {
            throw new LockedResponseException('Response is locked');
        }

        return $this;
    }

    /**
     * Lock the response from further modification
     *
     * @return AbstractResponse
     */
    public function lock(): AbstractResponse
    {
        $this->locked = true;

        return $this;
    }

    /**
     * Unlock the response from further modification
     *
     * @return AbstractResponse
     */
    public function unlock(): AbstractResponse
    {
        $this->locked = false;

        return $this;
    }

    /**
     * Generates an HTTP compatible status header line string
     *
     * Creates the string based off of the response's properties
     *
     * @return string
     */
    protected function httpStatusLine(): string
    {
        return sprintf('HTTP/%s %s', $this->protocol_version, $this->status);
    }

    /**
     * Send our HTTP headers
     *
     * @param boolean $cookies_also Whether to also send the cookies after sending the normal headers
     * @param boolean $override     Whether to override the check if headers have already been sent
     * @return AbstractResponse
     */
    public function sendHeaders(bool $cookies_also = true, bool $override = false): AbstractResponse
    {
        if (headers_sent() && !$override) {
            return $this;
        }

        // Send our HTTP status line
        header($this->httpStatusLine());

        // Iterate through our Headers data collection and send each header
        foreach ($this->headers as $key => $value) {
            header($key .': '. $value, false);
        }

        if ($cookies_also) {
            $this->sendCookies($override);
        }

        return $this;
    }

    /**
     * Send our HTTP response cookies
     *
     * @param boolean $override     Whether to override the check if headers have already been sent
     * @return AbstractResponse
     */
    public function sendCookies(bool $override = false): AbstractResponse
    {
        if (headers_sent() && !$override) {
            return $this;
        }

        // Iterate through our Cookies data collection and set each cookie natively
        foreach ($this->cookies as $cookie) {
            // Use the built-in PHP "setcookie" function
            setcookie(
                $cookie->getName(),
                $cookie->getValue(),
                $cookie->getExpire(),
                $cookie->getPath(),
                $cookie->getDomain(),
                $cookie->getSecure(),
                $cookie->getHttpOnly()
            );
        }

        return $this;
    }

    /**
     * Send our body's contents
     *
     * @return AbstractResponse
     */
    public function sendBody(): AbstractResponse
    {
        echo $this->body;

        return $this;
    }

    /**
     * Send the response and lock it
     *
     * @param boolean $override             Whether to override the check if the response has already been sent
     * @return AbstractResponse
     *@throws ResponseAlreadySentException If the response has already been sent
     */
    public function send(bool $override = false): AbstractResponse
    {
        if ($this->sent && !$override) {
            throw new ResponseAlreadySentException('Response has already been sent');
        }

        // Send our response data
        $this->sendHeaders();
        $this->sendBody();

        // Lock the response from further modification
        $this->lock();

        // Mark as sent
        $this->sent = true;

        // If there running FPM, tell the process manager to finish the server request/response handling
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }

        return $this;
    }

    /**
     * Check if the response has been sent
     *
     * @return boolean
     */
    public function isSent(): bool
    {
        return $this->sent;
    }

    /**
     * Enable response chunking
     *
     * @link https://github.com/klein/klein.php/wiki/Response-Chunking
     * @link http://bit.ly/hg3gHb
     * @return AbstractResponse
     */
    public function chunk(): AbstractResponse
    {
        if (false === $this->chunked) {
            $this->chunked = true;
            $this->header('Transfer-encoding', 'chunked');
            flush();
        }

        if (($body_length = strlen($this->body)) > 0) {
            printf("%x\r\n", $body_length);
            $this->sendBody();
            $this->body('');
            echo "\r\n";
            flush();
        }

        return $this;
    }

    /**
     * Sets a response header
     *
     * @param string $key       The name of the HTTP response header
     * @param mixed $value      The value to set the header with
     * @return AbstractResponse
     */
    public function header(string $key, mixed $value): AbstractResponse
    {
        $this->headers->set($key, $value);

        return $this;
    }

    /**
     * Sets a response cookie
     *
     * @param string $key           The name of the cookie
     * @param string $value         The value to set the cookie with
     * @param int|null $expiry           The time that the cookie should expire
     * @param string $path          The path of which to restrict the cookie
     * @param string|null $domain        The domain of which to restrict the cookie
     * @param boolean $secure       Flag of whether the cookie should only be sent over an HTTPS connection
     * @param boolean $httponly     Flag of whether the cookie should only be accessible over the HTTP protocol
     * @return AbstractResponse
     */
    public function cookie(
        string      $key,
        string      $value = '',
        int|null    $expiry = null,
        string      $path = '/',
        string|null $domain = null,
        bool        $secure = false,
        bool        $httponly = false
    ): AbstractResponse
    {
        if (null === $expiry) {
            $expiry = time() + (3600 * 24 * 30);
        }

        $this->cookies->set(
            $key,
            new ResponseCookie($key, $value, $expiry, $path, $domain, $secure, $httponly)
        );

        return $this;
    }

    /**
     * Tell the browser not to cache the response
     *
     * @return AbstractResponse
     */
    public function noCache(): AbstractResponse
    {
        $this->header('Pragma', 'no-cache');
        $this->header('Cache-Control', 'no-store, no-cache');

        return $this;
    }

    /**
     * Redirects the request to another URL
     *
     * @param string $url   The URL to redirect to
     * @param int $code     The HTTP status code to use for redirection
     * @return AbstractResponse
     */
    public function redirect(string $url, int $code = 302): AbstractResponse
    {
        $this->code($code);
        $this->header('Location', $url);
        $this->lock();

        return $this;
    }
}
