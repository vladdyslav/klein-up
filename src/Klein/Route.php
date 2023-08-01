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

use InvalidArgumentException;

/**
 * Route
 *
 * Class to represent a route definition
 */
class Route
{

    /**
     * Properties
     */

    /**
     * The callback method to execute when the route is matched
     *
     * Any valid "callable" type is allowed
     *
     * @link http://php.net/manual/en/language.types.callable.php
     * @type callable
     */
    protected $callback;

    /**
     * The URL path to match
     *
     * Allows for regular expression matching and/or basic string matching
     *
     * Examples:
     * - '/posts'
     * - '/posts/[:post_slug]'
     * - '/posts/[i:id]'
     *
     * @type string
     */
    protected string $path = '';

    /**
     * The HTTP method to match
     *
     * May either be represented as a string or an array containing multiple methods to match
     *
     * Examples:
     * - 'POST'
     * - array('GET', 'POST')
     *
     * @type string|array|null
     */
    protected string|array|null $method = null;

    /**
     * Whether to count this route as a match when counting total matches
     *
     * @type boolean|null
     */
    protected bool|null $count_match = null;

    /**
     * The name of the route
     *
     * Mostly used for reverse routing
     *
     * @type string|null
     */
    protected string|null $name = null;


    /**
     * Methods
     */

    /**
     * Constructor
     *
     * @param callable $callback
     * @param string|null $path
     * @param array|string|null $method
     * @param boolean $count_match
     * @param string|null $name
     */
    public function __construct(callable $callback, string|null $path = null, array|string|null $method = null, bool|null $count_match = true, string|null $name = null)
    {
        // Initialize some properties (use our setters so we can validate param types)
        $this->setCallback($callback);
        $this->setPath($path);
        $this->setMethod($method);
        $this->setCountMatch($count_match);
        $this->setName($name);
    }

    /**
     * Get the callback
     *
     * @return callable
     */
    public function getCallback(): callable
    {
        return $this->callback;
    }

    /**
     * Set the callback
     *
     * @param callable $callback
     * @return Route
     * @throws InvalidArgumentException If the callback isn't a callable
     */
    public function setCallback(callable $callback): Route
    {
        // not possible with strict typing
//        if (!is_callable($callback)) {
//            throw new InvalidArgumentException('Expected a callable. Got an uncallable '. gettype($callback));
//        }
        $this->callback = $callback;
        return $this;
    }

    /**
     * Get the path
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Set the path
     *
     * @param string|null $path
     * @return Route
     */
    public function setPath(string|null $path): Route
    {
        $this->path = (string) $path;
        return $this;
    }

    /**
     * Get the method
     *
     * @return array|string|null
     */
    public function getMethod(): array|string|null
    {
        return $this->method;
    }

    /**
     * Set the method
     *
     * @param array|string|null $method
     * @return Route
     *@throws InvalidArgumentException If a non-string or non-array type is passed
     */
    public function setMethod(array|string|null $method): Route
    {
        // Not possible with strict typing
        // Allow null, otherwise expect an array or a string
        // if (null !== $method && !is_array($method) && !is_string($method)) {
        //     throw new InvalidArgumentException('Expected an array or string. Got a '. gettype($method));
        // }
        $this->method = $method;
        return $this;
    }

    /**
     * Get the count_match
     *
     * @return bool|null
     */
    public function getCountMatch(): bool|null
    {
        return $this->count_match;
    }

    /**
     * Set the count_match
     *
     * @param boolean|null $count_match
     * @return Route
     */
    public function setCountMatch(bool|null $count_match): Route
    {
        $this->count_match = (boolean) $count_match;
        return $this;
    }

    /**
     * Get the name
     *
     * @return string|null
     */
    public function getName(): string|null
    {
        return $this->name;
    }

    /**
     * Set the name
     *
     * @param string|null $name
     * @return Route
     */
    public function setName(string|null $name): Route
    {
        $this->name = $name;
        return $this;
    }


    /**
     * Magic "__invoke" method
     *
     * Allows the ability to arbitrarily call this instance like a function
     *
     * @param mixed|null $args Generic arguments, magically accepted
     * @return mixed
     */
    public function __invoke(mixed $args = null): mixed
    {
        $args = func_get_args();

        return call_user_func_array(
            $this->callback,
            $args
        );
    }
}
