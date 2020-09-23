<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace HyperfTest\Cases;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Pgsql\Pgsql;
use Hyperf\Pgsql\Pool\PgsqlPool;
use Hyperf\Pgsql\Pool\PoolFactory;
use Hyperf\Pool\Channel;
use Hyperf\Pool\PoolOption;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Context;
use PHPUnit\Framework\TestCase;
use Mockery;
use Hyperf\Di\Container;
use Hyperf\Config\Config;

/**
 * Class AbstractTestCase.
 */
abstract class AbstractTestCase extends TestCase
{

    protected function tearDown()
    {
        Mockery::close();
        Context::set('pgsql.connection.default', null);
    }

    protected function getContainer($options = [])
    {
        $container = Mockery::mock(Container::class);
        $container->shouldReceive('get')->with(ConfigInterface::class)->andReturn(new Config([
            'pgsql' => [
                'default' => [
                    'host' => '',
                    'port' => '',
                    'username' => '',
                    'password' => '',
                    'database' => 'hyperf',
                    'pool' => [
                        'max_connections' => 20,
                    ],
                    'options' => $options,
                ]
            ],
        ]));
        $container->shouldReceive('make')->with(PgsqlPool::class, Mockery::any())->andReturnUsing(function ($_, $args) {
            return new PgsqlPool(...array_values($args));
        });
        $container->shouldReceive('make')->with(PoolOption::class, Mockery::any())->andReturnUsing(function ($_, $args) {
            return new PoolOption(...array_values($args));
        });
        $container->shouldReceive('make')->with(Channel::class, Mockery::any())->andReturnUsing(function ($_, $args) {
            return new Channel(...array_values($args));
        });
        $container->shouldReceive('get')->with(PoolFactory::class)->andReturn($factory = new PoolFactory($container));
        $container->shouldReceive('get')->with(Pgsql::class)->andReturn(new Pgsql($factory, 'default'));
        $container->shouldReceive('make')->with(Pgsql::class, Mockery::any())->andReturnUsing(function ($_, $params) use ($factory) {
            return new Pgsql($factory, $params['poolName']);
        });
        $container->shouldReceive('has')->with(StdoutLoggerInterface::class)->andReturn(false);
        ApplicationContext::setContainer($container);
        return $container;
    }
}
