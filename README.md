# zend-expressive-swoole

[![Packagist](https://img.shields.io/packagist/v/samuelnogueira/zend-expressive-swoole.svg)]()
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/samuelnogueira/zend-expressive-swoole/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/samuelnogueira/zend-expressive-swoole/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/samuelnogueira/zend-expressive-swoole/badges/build.png?b=master)](https://scrutinizer-ci.com/g/samuelnogueira/zend-expressive-swoole/build-status/master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/samuelnogueira/zend-expressive-swoole/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)

Swoole HTTP server integration with Zend Expressive framework

# TODO
- [x] ~~Cookies retrievable via \Psr\Http\Message\ServerRequestInterface::getCookieParams~~
- [ ] Include `Cookie` header in generated PSR-7 Server Request
- [ ] Handle uploaded files
- [ ] Stream request body instead of buffering it
- [ ] Stream response body instead of buffering it
- [ ] Configurable number of workers
