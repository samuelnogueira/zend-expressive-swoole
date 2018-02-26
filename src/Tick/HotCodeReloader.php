<?php

namespace Samuelnogueira\ExpressiveSwoole\Tick;

use Swoole\Server;

/**
 * Class HotCodeReloader
 *
 * @author  Samuel Nogueira <samuel.nogueira@jumia.com>
 * @package Samuelnogueira\ExpressiveSwoole\Tick
 */
class HotCodeReloader
{
    /** @var string[] */
    private $files;
    /** @var resource */
    private $inotify;
    /** @var \Swoole\Server */
    private $server;

    /**
     * FileWatcher constructor.
     *
     * @param \Swoole\Server $server
     */
    public function __construct(Server $server)
    {
        $this->inotify = \inotify_init();
        \stream_set_blocking($this->inotify, 0);
        $this->server = $server;
    }

    public function __invoke()
    {
        $events = \inotify_read($this->inotify);
        if (false !== $events) {
            $this->server->reload();
        }

        foreach (\get_included_files() as $file) {
            if (!isset($this->files[$file])) {
                $this->files[$file] = \inotify_add_watch($this->inotify, $file, IN_ATTRIB);
            }
        }
    }
}
