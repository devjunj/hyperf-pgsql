<?php
declare(strict_types=1);


namespace Hyperf\Pgsql;
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

use Hyperf\Contract\ConnectionInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Pgsql\DetectsLostConnections;
use Hyperf\Pgsql\ManagesTransactions;
use Hyperf\Pool\Connection;
use Hyperf\Pool\Exception\ConnectionException;

abstract class AbstractConnection extends Connection implements ConnectionInterface
{
    use DetectsLostConnections;
    use ManagesTransactions;

    /**
     * @var array
     */
    protected $config = [];

    public function getConfig(): array
    {
        return $this->config;
    }

    public function release(): void
    {
        if ($this->transactionLevel() > 0) {
            $this->rollBack(0);
            if ($this->container->has(StdoutLoggerInterface::class)) {
                $logger = $this->container->get(StdoutLoggerInterface::class);
                $logger->error('Maybe you\'ve forgotten to commit or rollback the MySQL transaction.');
            }
        }
        $this->pool->release($this);
    }

    public function getActiveConnection()
    {
        if ($this->check()) {
            return $this;
        }

        if (! $this->reconnect()) {
            throw new ConnectionException('Connection reconnect failed.');
        }

        return $this;
    }

    public function retry(\Throwable $throwable, $name, $arguments)
    {
        if ($this->transactionLevel() > 0) {
            throw $throwable;
        }

        if ($this->causedByLostConnection($throwable)) {
            try {
                $this->reconnect();
                return $this->{$name}(...$arguments);
            } catch (\Throwable $throwable) {
                if ($this->container->has(StdoutLoggerInterface::class)) {
                    $logger = $this->container->get(StdoutLoggerInterface::class);
                    $logger->error('Connection execute retry failed. message = ' . $throwable->getMessage());
                }
            }
        }

        throw $throwable;
    }
}
