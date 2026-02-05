<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Container\Container;
use Junco\Filesystem\MediaStorage;
use Junco\Http\Exception\HttpError;
use Junco\Http\Exception\HttpException;
use Junco\Http\Message\Response;
use Junco\Http\Message\ServerRequest;
use Junco\Users\Curuser;

/**
 * App
 */
function app(string $id = ''): object
{
    if ($id) {
        return Container::getInstance()->get($id);
    }

    return Container::getInstance();
}

/**
 * Abort
 * 
 * @param int    $statusCode
 * @param string $message
 * 
 * @throws HttpError
 */
function abort(int $statusCode = 0, string $message = '')
{
    throw new HttpError($statusCode, $message);
}

/**
 * Alert
 * 
 * @param int    $statusCode
 * @param string $message
 * 
 * @throws HttpException
 */
function alert(int $statusCode = 0, string $message = '')
{
    throw new HttpException($statusCode, $message);
}

/**
 * Authentication
 * 
 * @return Authentication
 */
function auth(): Authentication
{
    static $auth;
    if ($auth === null) {
        $auth = app('authentication');
    }

    return $auth;
}

/**
 * Cache
 */
function cache(): Cache
{
    static $cache;
    if ($cache === null) {
        $cache = app('cache');
    }

    return $cache;
}

/**
 * Config
 */
function config(string $key = '')
{
    static $config;
    if ($config === null) {
        $config = app('config');
    }

    if ($key) {
        return $config->get($key);
    }

    return $config;
}

/**
 * Curuser
 * 
 * @return Curuser
 */
function curuser(): Curuser
{
    static $curuser;
    if ($curuser === null) {
        $curuser = new Curuser();
    }

    return $curuser;
}

/**
 * Database
 */
function db(): Database
{
    static $db;
    if ($db === null) {
        $db = app('database');
    }

    return $db;
}

/**
 * Media path
 * 
 * @param string $path
 */
function media_path(string $path = ''): string
{
    static $media;
    if ($media === null) {
        $media = app(MediaStorage::class);
    }

    return $media->getPath($path);
}

/**
 * Media url
 * 
 * @param string $path
 */
function media_url(string $path = '', bool $absolute = false): string
{
    static $media;
    if ($media === null) {
        $media = app(MediaStorage::class);
    }

    return $media->getUrl($path, $absolute);
}

/**
 * Request
 */
function request(): ?ServerRequest
{
    if (!app()->has('request')) {
        return null;
    }

    return app('request');
}

/**
 * Response
 * 
 * @param int    $code
 * @param string $reasonPhrase
 * 
 * @return Response
 */
function response(int $code = 200, string $reasonPhrase = ''): Response
{
    return new Response($code, [], null, '1.1', $reasonPhrase);
}

/**
 * Request
 */
function cookie(string $key, mixed $default = null): mixed
{
    return request()?->getCookieParams()[$key] ?? $default;
}

/**
 * Router
 */
function router(): Router
{
    static $router;
    if ($router === null) {
        $router = app('router');
    }

    return $router;
}

/**
 * Router::getUrl
 * 
 * @param string $route
 * @param array  $args
 * @param bool   $absolute
 * 
 * @return string
 */
function url(string $route = '', array $args = [], bool $absolute = false): string
{
    static $router;
    if ($router === null) {
        $router = app('router');
    }

    return $router->getUrl($route, $args, $absolute);
}

/**
 * Router::redirect
 * 
 * @param string|array $url
 * @param bool         $absolute
 */
function redirect($url = null, bool $absolute = true): void
{
    app('router')->redirect($url, $absolute);
}

/**
 * Session
 */
function session(): Session
{
    static $session;
    if ($session === null) {
        $session = app('session');
    }

    return $session;
}

/**
 * Snippet
 */
function snippet(string $extension, ?string $snippet = null, ...$args): object
{
    static $snippets;
    if ($snippets === null) {
        $snippets = app('snippets');
    }

    return $snippets->new($extension, $snippet, ...$args);
}

/**
 * It is used to safely include functions
 * 
 * @param string $file
 * 
 * @return mixed
 */
function system_import(string $file, array $data = [])
{
    $data and extract($data);

    return include $file;
}

/**
 * Translator
 */
function _t(string $message): string
{
    static $translator;
    if ($translator === null) {
        $translator = app('language')->getTranslator();
    }

    return $translator->gettext($message);
}

/**
 * Plural version of gettext
 * 
 * @param string $message
 * @param string $plural
 * @param int    $n
 * 
 * @return string
 */
function _nt(string $singular, string $plural, int $n): string
{
    static $translator;
    if ($translator === null) {
        $translator = app('language')->getTranslator();
    }

    return $translator->ngettext($singular, $plural, $n);
}
