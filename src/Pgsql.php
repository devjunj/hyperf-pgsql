<?php
declare(strict_types=1);

namespace Hyperf\Pgsql;

use Hyperf\Pgsql\Pool\PoolFactory;
use Hyperf\Utils\Context;

class Pgsql
{
    /**
     * @var PoolFactory
     */
    protected $factory;

    /**
     * @var string
     */
    protected $poolName="default";

    public function __construct(PoolFactory $factory)
    {
        $this->factory = $factory;
    }

    public function beginTransaction(){
        $this->getConnection()->call('beginTransaction');
    }

    public function rollback(){
        $this->getConnection()->call('rollBack');
    }

    public function commit(){
        $this->getConnection()->call('commit');
    }

    public function insert(string $query,array $bindings = []): int
    {
        return $this->getConnection()->insert($query,$bindings);
    }

    public function execute(string $query,array $bindings = []): int
    {
        return $this->getConnection()->execute($query,$bindings);
    }

    public function query(string $query,array $bindings = []): array
    {
        return $this->getConnection()->query($query,$bindings);
    }

    public function fetch(string $query,array $bindings = [])
    {
        return $this->getConnection()->fetch($query,$bindings);
    }

    public function getConnection()
    {
        $connection = null;
        $hasContextConnection = Context::has($this->getContextKey());
        if ($hasContextConnection) {
            $connection = Context::get($this->getContextKey());
        }
        if (!$connection instanceof PgsqlConnection) {
            $pool = $this->factory->getPool($this->poolName);
            $connection = $pool->get()->getConnection();
            Context::set($this->getContextKey(),$connection);
        }
        return $connection;
    }

    /**
     * The key to identify the connection object in coroutine context.
     */
    private function getContextKey(): string
    {
        return sprintf('pgsql.connection.%s', $this->poolName);
    }
}
