<?php

declare(strict_types=1);

namespace Hyperf\Pgsql;
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */


use Hyperf\Pgsql\Pgsql;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                Pgsql::class => Pgsql::class,
            ],
            'commands' => [
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'The config of pgsql client',
                    'source' => __DIR__ . '/../publish/pgsql.php',
                    'destination' => BASE_PATH . '/config/autoload/pgsql.php',
                ],
            ],
        ];
    }
}
