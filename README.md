# hyperf-pgsql 

| hyperf 框架pgsql插件

* 1.通过composer安装
```
composer require jhyperf/pgsql

```
 * 2.自动配置

```
php bin/hyperf.php vendor:publish jhyperf/pgsql

```

* 3.修改ENV配置

```
PGSQL_USERNAME=
PGSQL_PASSWORD=
PGSQL_HOST=
PGSQL_PORT=
PGSQL_DB=hyperf
PGSQL_MAX_IDLE_TIME=60

```

* 4.使用示例
```
    /**
     * @Inject()
     * @var Pgsql
     */
    protected $pgsql;

    $this-pgsql->fetch("select * from company;",[]);

```
