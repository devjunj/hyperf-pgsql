<?php
declare(strict_types=1);

namespace Hyperf\Pgsql;

use Hyperf\Contract\ConnectionInterface;
use Hyperf\Pgsql\AbstractConnection;
use Hyperf\Pgsql\Exception\RuntimeException;
use Hyperf\Pool\Connection;
use Hyperf\Pool\Pool;
use Psr\Container\ContainerInterface;
use Swoole\Coroutine\PostgreSQL;

class PgsqlConnection extends AbstractConnection
{

    /**
     * @var PostgreSQL
     */
    protected $connection;

    /**
     * @var array
     */
    protected $config = [
        'username' => '',
        'password' => '',
        'host' => '',
        'port' => '',
        'database' => '',
        'timezone' => '',
        'pool' => [
            'min_connections' => 1,
            'max_connections' => 100,
            'connect_timeout' => 10.0,
            'wait_timeout' => 3.0,
            'heartbeat' => -1,
            'max_idle_time' => 60,
        ],
    ];

    public function __construct(ContainerInterface $container, Pool $pool, array $config)
    {
        parent::__construct($container, $pool);
        $this->config = array_replace_recursive($this->config, $config);
        $this->reconnect();
    }

    public function __call($name, $arguments)
    {
        return $this->connection->{$name}(...$arguments);
    }

    /**
     * @inheritDoc
     */
    public function reconnect(): bool
    {
        $connection = new PostgreSQL();
        $connStr =   "host=" . $this->config['host'] .
            " port=" . $this->config['port'] .
            " user=" . $this->config['username'] .
            " password=" . $this->config["password"] .
            " dbname=" . $this->config['database'].
            " connect_timeout=" . $this->config['pool']['connect_timeout'];
        if(!empty($this->config['timezone'])){
            $connStr = $connStr . " options='-c timezone=".$this->config['timezone']."'";
        }
        $connection->connect($connStr);
        $this->connection = $connection;
        $this->lastUseTime = microtime(true);
        $this->transactions = 0;
        return true;
    }

    /**
     * @inheritDoc
     */
    public function close(): bool
    {
        unset($this->connection);
        return true;
    }

    public function insert(string $query, array $bindings = []): int
    {
        $this->connection->prepare('pg_insert',$query);

        $result = $this->connection->execute("pg_insert",$bindings);

        if ($result === false) {
            throw new RuntimeException($this->connection->error);
        }

        $arr = $this->connection->fetchAssoc($result);
        return $arr?$arr['id']:0;
    }

    public function execute(string $query, array $bindings = []): int
    {
        $this->connection->prepare('pg_execute',$query);

        $result = $this->connection->execute("pg_execute",$bindings);
        if ($result === false) {
            throw new RuntimeException($this->connection->error);
        }
        return $this->connection->affectedRows($result);
    }

    public function exec(string $sql): int
    {
        $res = $this->connection->query($sql);
        if ($res === false) {
            throw new RuntimeException($this->connection->error);
        }

        return $this->connection->affectedRows($res);
    }

    public function query(string $query, array $bindings = []):? array
    {

        $md5  = md5($query)."_".microtime(false)."_".sizeof($bindings);

        $this->connection->prepare($md5,$query);

        $result = $this->connection->execute($md5,$bindings);
        if ($result === false) {
            throw new RuntimeException($this->connection->error);
        }
        return  $this->connection->fetchAll($result);
    }

    public function fetch(string $query, array $bindings = [])
    {
        $records = $this->query($query, $bindings);

        return array_shift($records);
    }

    public function call(string $method, array $argument = [])
    {
        switch ($method) {
            case 'beginTransaction':
                return $this->connection->query("BEGIN;");
            case 'rollBack':
                return $this->connection->query("ROLLBACK;");
            case 'commit':
                return $this->connection->query("COMMIT;");
        }

        return $this->connection->{$method}(...$argument);
    }

}
