# zend-expressive-swoole
Swoole HTTP server integration with Zend Expressive framework

# TODO
- [x] Cookies retrievable via `\Psr\Http\Message\ServerRequestInterface::getCookieParams`  
- [ ] Include `Cookie` header in generated PSR-7 Server Request
- [ ] Handle uploaded files
- [ ] Stream request body instead of buffering it
- [ ] Stream response body instead of buffering it
- [ ] Configurable number of workers
