<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Http\Message;

use Psr\Http\Message\UriInterface;

/**
 * Value object representing a URI.
 */
class Uri implements UriInterface
{
    // vars
    protected string  $scheme    = '';
    protected string  $userInfo  = '';
    protected string  $host      = '';
    protected ?int    $port      = null;
    //
    protected string  $path      = '';
    protected string  $query     = '';
    protected string  $fragment  = '';
    protected ?string $uriString = null;
    //
    protected $allowedSchemes = [
        'http' => 80,
        'https' => 443,
        'ftp' => 21,
        'gopher' => 70,
        'nntp' => 119,
        'news' => 119,
        'telnet' => 23,
        'tn3270' => 23,
        'imap' => 143,
        'pop' => 110,
        'ldap' => 389,
    ];

    /**
     * Unreserved characters used in user info, paths, query strings, and fragments.
     *
     * @see https://datatracker.ietf.org/doc/html/rfc3986#section-2.3
     */
    protected const CHAR_UNRESERVED = 'a-zA-Z0-9_\-\.~\pL';

    /**
     * Sub-delimiters used in user info, query strings and fragments.
     *
     * @see https://datatracker.ietf.org/doc/html/rfc3986#section-2.2
     */
    protected const CHAR_SUB_DELIMS = '!\$&\'\(\)\*\+,;=';

    /**
     * Constructor
     * 
     * @param string|array
     */
    public function __construct(string|array $uri)
    {
        if (!$uri) {
            throw new \InvalidArgumentException('The uri is empty');
        }
        if (is_string($uri)) {
            $parts = $this->parse($uri);

            if ($parts === false) {
                throw new \InvalidArgumentException(sprintf('Unable to parse URI «%s»', $uri));
            }
        } else {
            $parts = $uri;
        }

        $this->scheme   = isset($parts['scheme']) ? $this->filterScheme($parts['scheme']) : '';
        $this->userInfo = isset($parts['user']) ? $this->filterUserInfoComponent($parts['user']) : '';
        $this->host     = isset($parts['host']) ? $this->filterHost($parts['host']) : '';
        $this->port     = isset($parts['port']) ? $this->filterPort($parts['port']) : null;
        $this->path     = isset($parts['path']) ? $this->filterPath($parts['path']) : '';
        $this->query    = isset($parts['query']) ? $this->filterQueryAndFragment($parts['query']) : '';
        $this->fragment = isset($parts['fragment']) ? $this->filterQueryAndFragment($parts['fragment']) : '';

        if (isset($parts['pass'])) {
            $this->userInfo .= ':' . $this->filterUserInfoComponent($parts['pass']);
        }
    }
    /**
     * Retrieve the scheme component of the URI.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.1
     * 
     * @return string The URI scheme.
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * Retrieve the authority component of the URI.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.2
     * 
     * @return string The URI authority, in "[user-info@]host[:port]" format.
     */
    public function getAuthority(): string
    {
        if ($this->host == '') {
            return '';
        }

        $authority = '';

        if ($this->userInfo != '') {
            $authority .= $this->userInfo . '@';
        }
        $authority .= $this->host;

        if ($this->isNonStandardPort()) {
            $authority .= ':' . $this->port;
        }

        return $authority;
    }

    /**
     * Retrieve the user information component of the URI.
     *
     * @return string The URI user information, in "username[:password]" format.
     */
    public function getUserInfo(): string
    {
        return $this->userInfo;
    }

    /**
     * Retrieve the host component of the URI.
     *
     * @see http://tools.ietf.org/html/rfc3986#section-3.2.2
     * 
     * @return string The URI host.
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * Retrieve the port component of the URI.
     *
     * @return null|int The URI port.
     */
    public function getPort(): ?int
    {
        return $this->isNonStandardPort()
            ? $this->port
            : null;
    }

    /**
     * Retrieve the path component of the URI.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.3
     * 
     * @return string The URI path.
     */
    public function getPath(): string
    {
        if ($this->path == '') {
            return $this->path;
        }

        if ($this->path[0] != '/') {
            return $this->path;
        }

        return '/' . ltrim($this->path, '/');
    }

    /**
     * Retrieve the query string of the URI.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.4
     * 
     * @return string The URI query string.
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * Retrieve the fragment component of the URI.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.5
     * 
     * @return string The URI fragment.
     */
    public function getFragment(): string
    {
        return $this->fragment;
    }

    /**
     * Return an instance with the specified scheme.
     *
     * @param string $scheme The scheme to use with the new instance.
     * 
     * @return static
     * 
     * @throws \InvalidArgumentException for invalid or unsupported schemes.
     */
    public function withScheme(string $scheme): UriInterface
    {
        $scheme = $this->filterScheme($scheme);

        if ($scheme == $this->scheme) {
            return $this;
        }

        $new = clone $this;
        $new->scheme = $scheme;

        return $new;
    }

    /**
     * Return an instance with the specified user information.
     *
     * @param string      $user     The user name to use for authority.
     * @param null|string $password The password associated with $user.
     * 
     * @return static
     */
    public function withUserInfo(string $user, ?string $password = null): UriInterface
    {
        $info = $this->filterUserInfoComponent($user);
        if ($password !== null) {
            $info .= ':' . $this->filterUserInfoComponent($password);
        }

        if ($info === $this->userInfo) {
            return $this;
        }

        $new = clone $this;
        $new->userInfo = $info;

        return $new;
    }

    /**
     * Return an instance with the specified host.
     *
     * @param string $host The hostname to use with the new instance.
     * 
     * @return static A new instance with the specified host.
     * 
     * @throws \InvalidArgumentException for invalid hostnames.
     */
    public function withHost(string $host): UriInterface
    {
        if ($host === $this->host) {
            return $this;
        }

        $new = clone $this;
        $new->host = $this->filterHost($host);

        return $new;
    }

    /**
     * Return an instance with the specified port.
     *
     * @param null|int $port The port to use with the new instance; a null value
     *     removes the port information.
     * 
     * @return static A new instance with the specified port.
     * 
     * @throws \InvalidArgumentException for invalid ports.
     */
    public function withPort(?int $port): UriInterface
    {
        if ($port === $this->port) {
            return $this;
        }

        $new = clone $this;
        $new->port = $this->filterPort($port);

        return $new;
    }

    /**
     * Return an instance with the specified path.
     *
     * @param string $path The path to use with the new instance.
     * 
     * @return static A new instance with the specified path.
     * 
     * @throws \InvalidArgumentException for invalid paths.
     */
    public function withPath(string $path): UriInterface
    {
        $path = $this->filterPath($path);

        if ($path === $this->path) {
            return $this;
        }

        $new = clone $this;
        $new->path = $path;

        return $new;
    }

    /**
     * Return an instance with the specified query string.
     *
     * @param string $query The query string to use with the new instance.
     * @return static A new instance with the specified query string.
     * @throws \InvalidArgumentException for invalid query strings.
     */
    public function withQuery(string $query): UriInterface
    {
        $query = $this->filterQueryAndFragment($query);

        if ($query === $this->query) {
            return $this;
        }

        $new = clone $this;
        $new->query = $query;

        return $new;
    }

    /**
     * Return an instance with the specified URI fragment.
     *
     * @param string $fragment The fragment to use with the new instance.
     * 
     * @return static A new instance with the specified fragment.
     */
    public function withFragment(string $fragment): UriInterface
    {
        $fragment = $this->filterQueryAndFragment($fragment);

        if ($fragment === $this->fragment) {
            return $this;
        }

        $new = clone $this;
        $new->fragment = $fragment;

        return $new;
    }

    /**
     * Return the string representation as a URI reference.
     *
     * @see http://tools.ietf.org/html/rfc3986#section-4.1
     * 
     * @return string
     */
    public function __toString(): string
    {
        if ($this->uriString === null) {
            $uri = '';

            if ($this->scheme != '') {
                $uri .= $this->scheme . ':';
            }

            $authority = $this->getAuthority();
            if ($authority != '' || $this->scheme === 'file') {
                $uri .= '//' . $authority;
            }

            if (
                $authority != ''
                && $this->path != ''
                && $this->path[0] != '/'
            ) {
                $uri .= '/';
            }
            $uri .= $this->path;

            if ($this->query != '') {
                $uri .= '?' . $this->query;
            }

            if ($this->fragment != '') {
                $uri .= '#' . $this->fragment;
            }

            $this->uriString = $uri;
        }

        return $this->uriString;
    }

    /**
     * On clone
     */
    public function __clone()
    {
        $this->uriString = null;
    }

    /**
     * Multibytes Url Parser
     *
     * @see https://www.php.net/manual/en/function.parse-url.php#114817
     * 
     * @param string $url
     *
     * @return array|false
     */
    protected function parse(string $url): array|false
    {
        // extract IPv6
        $url_with_IPv6 = '';
        if (preg_match('%^(.*://\[[0-9a-f:]+\])(.*?)$%', $url, $matches)) {
            $url_with_IPv6    = $matches[1];
            $url            = $matches[2];
        }

        // endoding
        $url = preg_replace_callback('%[^:/@?&=#]+%usD', function ($matches) {
            return urlencode($matches[0]);
        }, $url);

        // parsing
        $parts = parse_url($url_with_IPv6 . $url);

        if ($parts === false) {
            return false;
        }

        return array_map('urldecode', $parts);
    }

    /**
     * Is a given port non-standard for the current scheme
     */
    protected function isNonStandardPort(): bool
    {
        if ($this->scheme == '') {
            return $this->host == '' || $this->port !== null;
        }
        if ($this->host == '' || $this->port === null) {
            return false;
        }

        return !isset($this->allowedSchemes[$this->scheme]) || $this->port != $this->allowedSchemes[$this->scheme];
    }

    /**
     * Filters the scheme to ensure it is a valid scheme.
     * 
     * @param string $scheme Scheme name.
     * 
     * @return string Filtered scheme.
     */
    protected function filterScheme(string $scheme): string
    {
        $scheme = rtrim($scheme, ":/");

        if ($scheme == '') {
            return '';
        }

        $scheme = strtolower($scheme);

        if (!isset($this->allowedSchemes[$scheme])) {
            throw new \InvalidArgumentException(sprintf('Unsupported scheme «%s»', $scheme));
        }

        return $scheme;
    }

    /**
     * Filters a component of user info in a URI to ensure it is properly encoded.
     * 
     * @param string $component
     * 
     * @return string
     */
    protected function filterUserInfoComponent(string $component): string
    {
        $regex = '/(?:[^%' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . ']+|%(?![A-Fa-f0-9]{2}))/';

        return preg_replace_callback($regex, function ($match) {
            return rawurlencode($match[0]);
        }, $component);
    }

    /**
     * Filter
     * 
     * @param string $host
     *
     * @throws \InvalidArgumentException If the port is invalid.
     */
    protected function filterHost(string $host): string
    {
        return strtolower($host);
    }

    /**
     * Filter
     * 
     * @param int $port
     *
     * @throws \InvalidArgumentException If the port is invalid.
     */
    protected function filterPort(int $port): int
    {
        if (0 > $port || 0xFFFF < $port) {
            throw new \InvalidArgumentException(sprintf('The port «%s» is not valid', $port));
        }

        return $port;
    }

    /**
     * Filters the path of a URI
     *
     * @param string $path
     *
     * @throws \InvalidArgumentException If the path is invalid.
     */
    protected function filterPath(string $path): string
    {
        $regex = '/(?:[^' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . '%:@\/]++|%(?![A-Fa-f0-9]{2}))/';

        return preg_replace_callback($regex, function ($match) {
            return rawurlencode($match[0]);
        }, $path);
    }

    /**
     * Filter a query string to ensure it is propertly encoded.
     * 
     * @param string $query
     *
     * @throws \InvalidArgumentException If the path is invalid.
     */
    protected function filterQueryAndFragment(string $query): string
    {
        $regex = '/(?:[^' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . '%:@\/\?]++|%(?![A-Fa-f0-9]{2}))/';

        return preg_replace_callback($regex, function ($match) {
            return rawurlencode($match[0]);
        }, $query);
    }
}
