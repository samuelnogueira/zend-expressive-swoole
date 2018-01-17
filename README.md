# zend-expressive-swoole

[![Build Status](https://travis-ci.org/samuelnogueira/zend-expressive-swoole.svg?branch=master)](https://travis-ci.org/samuelnogueira/zend-expressive-swoole)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/samuelnogueira/zend-expressive-swoole/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/samuelnogueira/zend-expressive-swoole/?branch=master)

Swoole HTTP server integration with Zend Expressive framework

# TODO
- [x] Cookies retrievable via `\Psr\Http\Message\ServerRequestInterface::getCookieParams`  
- [ ] Include `Cookie` header in generated PSR-7 Server Request
- [ ] Handle uploaded files
- [ ] Stream request body instead of buffering it
- [ ] Stream response body instead of buffering it
- [ ] Configurable number of workers
