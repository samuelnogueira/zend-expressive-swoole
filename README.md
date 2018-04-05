# zend-expressive-swoole

[![Packagist Pre Release](https://img.shields.io/packagist/vpre/samuelnogueira/zend-expressive-swoole.svg)](https://packagist.org/packages/samuelnogueira/zend-expressive-swoole)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/samuelnogueira/zend-expressive-swoole/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/samuelnogueira/zend-expressive-swoole/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/samuelnogueira/zend-expressive-swoole/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/samuelnogueira/zend-expressive-swoole/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/samuelnogueira/zend-expressive-swoole/badges/build.png?b=master)](https://scrutinizer-ci.com/g/samuelnogueira/zend-expressive-swoole/build-status/master)
[![CircleCI](https://circleci.com/gh/samuelnogueira/zend-expressive-swoole.svg?style=shield)](https://circleci.com/gh/samuelnogueira/zend-expressive-swoole)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/samuelnogueira/zend-expressive-swoole/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)

[Swoole](http://www.swoole.com/) HTTP server integration with [Zend Expressive](https://docs.zendframework.com/zend-expressive/) framework

## Requirements

* PHP >= 7.1
* [ext-swoole](https://pecl.php.net/package/swoole) >= 2.0
* [Zend Expressive](https://docs.zendframework.com/zend-expressive/) v3 application

Support for older version v2 of Zend Expressive is provided in version [0.2](https://packagist.org/packages/samuelnogueira/zend-expressive-swoole#0.2.0) of this library.

## Installation

This package is installable and autoloadable via Composer as [samuelnogueira/zend-expressive-swoole](https://packagist.org/packages/samuelnogueira/zend-expressive-swoole).

```sh
composer require samuelnogueira/zend-expressive-swoole
```

## Configuration
```php
<?php // config/autoload/swoole.global.php

return [
    'swoole_http_server' => [
        'host'     => '127.0.0.1', // default is '0.0.0.0'
        'port'     => 80,          // default is 8080
        'settings' => [            // default is []. See see https://rawgit.com/tchiotludo/swoole-ide-helper/english/docs/classes/swoole_server.html#method_set
            'http_parse_post' => false, 
            'worker_num'      => 100, 
        ],
        'hot_code_reload' => [
            'enabled'  => true, // default is false
            'interval' => 500,  // default is 1000. Milliseconds between file changes checks.
        ],
    ],
];
```
Any omitted config key will fallback to the default value in the comments above.
 
## Usage
```bash
# Start swoole HTTP booting your Zend Expressive app
$ ./vendor/bin/swoole
```

### Hot Code Reload
To enable hot code reload, add the following configuration:
```php
<?php // config/autoload/swoole.global.php

return [
    'swoole_http_server' => [
        // (...)
        'hot_code_reload' => [
            'enabled'  => true,
        ],
    ],
];
```
With this feature enabled, each swoole worker will keep track of included PHP files using [inotify](https://pecl.php.net/package/inotify), and will restart all workers if a file is changed.

This serves to enable easier development when using swoole server.

**Do not use this feature in production**. It doesn't perform well for a big number of workers, nor is it safe.

## TODO
- [x] ~~Cookies retrievable via \Psr\Http\Message\ServerRequestInterface::getCookieParams~~
- [x] ~~Configurable number of workers~~
- [x] ~~Hot code reload~~
- [x] ~~Add support for Zend Expressive 3~~
- [ ] Include `Cookie` header in generated PSR-7 Server Request
- [ ] Handle uploaded files
- [ ] Stream request body instead of buffering it
- [ ] Stream response body instead of buffering it
- [ ] Windows support?
