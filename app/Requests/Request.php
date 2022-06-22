<?php

namespace App\Requests;

use JsonException;

class Request
{
    public array $request;
    public array $query;
    public array $headers;
    public array $attributes;
    public array $cookies;
    public array $files;
    public array $server;
    public $content;
    public $requestUri;
    public $method;

    public function __construct(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null)
    {
        $this->initialize($query, $request, $attributes, $cookies, $files, $server, $content);
    }


    public static function createFromGlobals(): self
    {
        return new self($_GET, $_POST, [], $_COOKIE, $_FILES, $_SERVER);
    }

    public function initialize(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null): void
    {
        $this->query = $query;
        $this->attributes = $attributes;
        $this->cookies = $cookies;
        $this->files = $files;
        $this->server = $server;
        $this->headers = $this->parseHeaders($server);
        $this->content = $content;
        $this->request = $this->parseRequest($request);

        $this->requestUri = null;
        $this->method = null;
    }

    public static function capture()
    {
        return static::createFromGlobals();
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function hasHeader($name)
    {
        return array_key_exists($name, $this->headers);
    }

    public function getHeader($name)
    {
        if (!$this->hasHeader($name)) {
            return null;
        }

        return $this->headers[$name];
    }

    public function getMethod()
    {
        if ($this->method) {
            return $this->method;
        }

        $this->method = strtoupper($this->getServerParam('REQUEST_METHOD'));


        return $this->method;
    }

    public function getContent(bool $asResource = false)
    {
        $currentContentIsResource = \is_resource($this->content);

        if (true === $asResource) {
            if ($currentContentIsResource) {
                rewind($this->content);

                return $this->content;
            }

            if (\is_string($this->content)) {
                $resource = fopen('php://temp', 'r+');
                fwrite($resource, $this->content);
                rewind($resource);

                return $resource;
            }

            $this->content = false;

            return fopen('php://input', 'r');
        }

        if ($currentContentIsResource) {
            rewind($this->content);

            return stream_get_contents($this->content);
        }

        if (null === $this->content || false === $this->content) {
            $this->content = file_get_contents('php://input');
        }

        return $this->content;
    }

    public function getServerParams()
    {
        return $this->server;
    }

    public function hasServerParam($name)
    {
        return array_key_exists($name, $this->server);
    }

    public function getServerParam($name)
    {
        return $this->hasServerParam($name)
            ? $this->server[$name]
            : null;
    }

    public function getCookieParams()
    {
        return $this->cookies;
    }


    public function getQueryParams()
    {
        return $this->query;
    }

    public function hasQueryParam($name)
    {
        return array_key_exists($name, $this->query);
    }

    public function getQueryParam($name, $default = null)
    {
        if ($this->hasQueryParam($name)) {
            return $this->query[$name];
        }

        return $default;
    }

    public function getUploadedFiles()
    {
        return $this->files;
    }

    public function getAttributes()
    {
        return $this->request;
    }

    public function hasAtrribute($name)
    {
        return array_key_exists($name, $this->request);
    }

    public function getAttribute($name, $default = null)
    {
        if ($this->hasAtrribute($name)) {
            return $this->request[$name];
        }

        return $default;
    }

    public function getContentType()
    {
        return $this->getHeader('CONTENT_TYPE');
    }

    public function getRequestUri()
    {
        return $this->getServerParam('REQUEST_URI');
    }

    private function parseHeaders(array $server)
    {
        // Based on Symfony Request Class
        $headers = [];
        foreach ($server as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $headers[substr($key, 5)] = $value;
            } elseif (\in_array($key, ['CONTENT_TYPE', 'CONTENT_LENGTH', 'CONTENT_MD5'], true)) {
                $headers[$key] = $value;
            }
        }

        if (isset($server['PHP_AUTH_USER'])) {
            $headers['PHP_AUTH_USER'] = $server['PHP_AUTH_USER'];
            $headers['PHP_AUTH_PW'] = $server['PHP_AUTH_PW'] ?? '';
        } else {
            $authorizationHeader = null;
            if (isset($server['HTTP_AUTHORIZATION'])) {
                $authorizationHeader = $server['HTTP_AUTHORIZATION'];
            } elseif (isset($server['REDIRECT_HTTP_AUTHORIZATION'])) {
                $authorizationHeader = $server['REDIRECT_HTTP_AUTHORIZATION'];
            }

            if (null !== $authorizationHeader) {
                if (0 === stripos($authorizationHeader, 'basic ')) {
                    $exploded = explode(':', base64_decode(substr($authorizationHeader, 6)), 2);
                    if (2 == \count($exploded)) {
                        [$headers['PHP_AUTH_USER'], $headers['PHP_AUTH_PW']] = $exploded;
                    }
                } elseif (0 === stripos($authorizationHeader, 'bearer ')) {
                    $headers['AUTHORIZATION'] = $authorizationHeader;
                }
            }
        }

        if (isset($headers['AUTHORIZATION'])) {
            return $headers;
        }

        // PHP_AUTH_USER/PHP_AUTH_PW
        if (isset($headers['PHP_AUTH_USER'])) {
            $headers['AUTHORIZATION'] = 'Basic '.base64_encode($headers['PHP_AUTH_USER'].':'.($headers['PHP_AUTH_PW'] ?? ''));
        } elseif (isset($headers['PHP_AUTH_DIGEST'])) {
            $headers['AUTHORIZATION'] = $headers['PHP_AUTH_DIGEST'];
        }


        return $headers;
    }

    /**
     * @throws JsonException
     */
    private function parseRequest($request)
    {
        $content = $this->getContent();
        if ($this->getContentType() === 'application/json' && $content !== '') {
            try {
                $content = json_decode($content, true, 512, \JSON_BIGINT_AS_STRING | (\PHP_VERSION_ID >= 70300 ? \JSON_THROW_ON_ERROR : 0));
            } catch (JsonException $e) {
                throw new JsonException('Could not decode request body.', $e->getCode(), $e);
            }
        } else {
            return $request;
        }

        return $content;
    }

}