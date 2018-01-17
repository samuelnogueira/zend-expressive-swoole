# zend-expressive-swoole

[![Packagist Pre Release](https://img.shields.io/packagist/vpre/samuelnogueira/zend-expressive-swoole.svg)](https://packagist.org/packages/samuelnogueira/zend-expressive-swoole)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/samuelnogueira/zend-expressive-swoole/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/samuelnogueira/zend-expressive-swoole/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/samuelnogueira/zend-expressive-swoole/badges/build.png?b=master)](https://scrutinizer-ci.com/g/samuelnogueira/zend-expressive-swoole/build-status/master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/samuelnogueira/zend-expressive-swoole/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)

[Swoole](http://www.swoole.com/) HTTP server integration with [Zend Expressive](https://docs.zendframework.com/zend-expressive/) framework

## Requirements

* PHP >= 7.0
* [ext-swoole](https://pecl.php.net/package/swoole) >= 2.0
* [Zend Expressive](https://docs.zendframework.com/zend-expressive/) application

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
    ],
];
```

## Usage
```bash
# Start swoole HTTP booting your Zend Expressive app
$ ./vendor/bin/swoole
```


## TODO
- [x] ~~Cookies retrievable via \Psr\Http\Message\ServerRequestInterface::getCookieParams~~
- [ ] Include `Cookie` header in generated PSR-7 Server Request
- [ ] Handle uploaded files
- [ ] Stream request body instead of buffering it
- [ ] Stream response body instead of buffering it
- [x] ~~Configurable number of workers~~
- [ ] Windows support?
