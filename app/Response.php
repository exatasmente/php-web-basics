<?php

namespace App;

class Response
{
    public $headers = [];
    protected $content;
    protected $statusCode;
    protected $statusText;
    protected $charset;
    public $version;
    public $cookies;


    public function __construct($content = '', int $status = 200, array $headers = [], array $cookies = [], $charset = 'UTF-8', $version = '1.1')
    {
        $this->headers = $headers;
        $this->cookies = $cookies;
        $this->charset = $charset;
        $this->version = $version;

        $this->setContent($content);
        $this->setStatusCode($status);
    }


    public static function create($content = '', int $status = 200, array $headers = [], array $cookies = [], $charset = 'UTF-8', $version = '1.1')
    {
        return new self($content, $status, $headers, $cookies, $charset);
    }

    public static function json($content = '', int $status = 200, array $headers = [], array $cookies = [], $charset = 'UTF-8', $version = '1.1')
    {
        $content = json_encode($content);
        $response = new self($content, $status, $headers, $cookies, $charset);
        $response->setContentType('application/json');

        return $response;
    }


    public function setContent($content)
    {
        $this->content = $content ?? '';
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setStatusCode(int $code)
    {
        $this->statusCode = $code;
        if ($code < 100 || $code >= 600) {
            throw new \InvalidArgumentException(sprintf('The HTTP status code "%s" is not valid.', $code));
        }
    }

    public function setContentType($contentType)
    {
        $this->headers['Content-Type'] = [$contentType];
    }

    public function setCookie(string $name, string $value = null, $expire = 0, ?string $path = '/', string $domain = null, bool $secure = null, bool $httpOnly = true)
    {
        $str = $name . '=';

        if ('' === (string) $value) {
            $str .= 'deleted; expires='.gmdate('D, d-M-Y H:i:s T', time() - 31536001).'; Max-Age=0';
        } else {
            $str .= rawurlencode($value);
            $maxAge = max($expire - time(), 0);

            if (0 !== $expire) {
                $str .= '; expires='.gmdate('D, d-M-Y H:i:s T', $expire).'; Max-Age='. $maxAge;
            }
        }
        $str .= '; path='.$path;

        if ($domain) {
            $str .= '; domain='.$domain;
        }

        if (true === $httpOnly) {
            $str .= '; httponly';
        }

        $this->cookies[$name] = $str;
    }


    public function sendHeaders()
    {
        // headers have already been sent by the developer
        if (headers_sent()) {
            return $this;
        }

        // headers
        foreach ($this->headers as $name => $values) {
            $replace = 0 === strcasecmp($name, 'Content-Type');
            foreach ($values as $value) {
                header($name.': '.$value, $replace, $this->statusCode);
            }
        }

        // cookies
        foreach ($this->cookies as $cookie) {
            header('Set-Cookie: '.$cookie, false, $this->statusCode);
        }

        // status
        header(sprintf('HTTP/%s %s %s', $this->version, $this->statusCode, $this->statusText), true, $this->statusCode);

        return $this;
    }

    public function sendContent()
    {
        echo $this->content;
    }

    /**
     * Sends HTTP headers and content.
     *
     * @return $this
     */
    public function send()
    {
        $this->sendHeaders();
        $this->sendContent();

        if (\function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        } elseif (\function_exists('litespeed_finish_request')) {
            litespeed_finish_request();
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->content;
    }
}