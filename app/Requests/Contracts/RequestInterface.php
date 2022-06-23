<?php

namespace App\Requests\Contracts;

interface RequestInterface
{
    public static function createFromGlobals(): self;

    public static function capture();

    public function initialize(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null): void;

    public function initializeFromRequest(RequestInterface $request);

    public function getContent(bool $asResource = false);

    public function getContentType();

    public function getRequestUri();

    public function getAttribute($name, $default = null);

    public function hasAttribute($name);

    public function getAttributes();

    public function setRouteParams($params);

    public function getRouteParams();

    public function getRouteParam($name);

    public function getQueryParam($name, $default = null);

    public function hasQueryParam($name);

    public function getQueryParams();

    public function getCookieParams();

    public function getServerParam($name);

    public function hasServerParam($name);

    public function getServerParams();
}
