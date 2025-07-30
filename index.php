<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Container\Container;
use Junco\Router\Runner\Runner;
use Junco\Http\Server\ServerRequestFactory;
use Junco\Http\Emitter\SapiEmitter;

/**
 * Include
 * 
 * - bootstrap
 * - autoload
 */
include 'bootstrap.php';
include SYSTEM_ABSPATH . SYSTEM_AUTOLOAD;

/**
 * Initialize Systems
 *
 * @The following systems are initialized:
 * - Container
 * - System
 * - Debugger
 */
$container = Container::getInstance();

# profiler
$profiler = null;
if ($container->get('config')->get('system.profiler')) {
    $profiler = new Profiler(true);
    $profiler->mark('After including the libraries');
    $container->set('profiler', $profiler);
}

$container->get('system');
$container->get('debugger');

if ($profiler) {
    $profiler->mark("After initialising the system");
}

/**
 * Run script
 */
$request = (new ServerRequestFactory)->createServerRequest();
$handler = new Runner();
$handler->add('router');
if ($profiler) {
    $handler->add('profiler');
}
$response = $handler->handle($request);

(new SapiEmitter)->emit($response);
