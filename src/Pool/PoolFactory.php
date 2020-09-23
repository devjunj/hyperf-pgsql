<?php

namespace Hyperf\Pgsql\Pool;

use Hyperf\Di\Container;
use Hyperf\Pool\Pool;
use Psr\Container\ContainerInterface;

class PoolFactory
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var Pool[]
     */
    protected $pools = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getPool(string $name):PgsqlPool
    {
        if(isset($this->pools[$name])){
            return $this->pools[$name];
        }

        if($this->container instanceof Container){
            $pool = $this->container->make(PgsqlPool::class,['container'=>$this->container,'name'=>$name]);
        } else{
            $pool = new PgsqlPool($this->container,$name);
        }
        return $this->pools[$name] = $pool;
    }
}
