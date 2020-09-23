# hyperf-pgsql 

| hyperf 框架pgsql插件

* 1.通过composer安装
```
composer require hyperf/pgsql

```
 * 2.自动配置

```
php bin/hyperf.php vendor:publish hyperf/pgsql

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
    //查询一条
    $this->pgsql->fetch("select * from company;",[]);
    //查询
    $this->pgsql->query("select * from company;",[]);
    //执行更新
    $this->pgsql->execute("update company set name = 'abc' where id = $1",[1]);

    //事务示例,注意要关闭pgsql 自动提交方能生效
    $this->pgsql->beginTransaction();
    $this->pgsql->insert(INSERT INTO company (id,name,age,address,salary) VALUES (5,'abc',25,'bck',1000.00) RETURNING id ;);
    $this->pgsql->commit();

```
