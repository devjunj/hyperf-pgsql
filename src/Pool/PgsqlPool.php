<?php

namespace Hyperf\Pgsql\Pool;

use http\Exception\InvalidArgumentException;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\ConnectionInterface;
use Hyperf\Pgsql\PgsqlConnection;
use Hyperf\Pool\Pool;
use Hyperf\Utils\Arr;
use Psr\Container\ContainerInterface;

class PgsqlPool extends Pool
{

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $config;

    public function __construct(ContainerInterface $container,string $name)
    {
        $this->name = $name;
        $config = $container->get(ConfigInterface::class);
        $key = sprintf("pgsql.%s",$name);
        if(!$config->has($key)){
            throw new \InvalidArgumentException(sprintf('config[%s] is not exist!',$key));
        }

        $this->config = $config->get($key);
        $options = Arr::get($this->config,'pool',[]);

        parent::__construct($container, $options);
    }

    protected function createConnection(): ConnectionInterface
    {
        return new PgsqlConnection($this->container,$this,$this->config);
    }
}
