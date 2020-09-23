<?php


namespace HyperfTest\Cases;


use Hyperf\Pgsql\Pgsql;
use Hyperf\Pgsql\Pool\PoolFactory;

class PgsqlTest extends AbstractTestCase
{

    public function testConnection()
    {
        $connection = $this->getPgsql()->getConnection();
        var_dump($connection);
        self::assertNotEmpty($connection);
    }

    public function testFetch()
    {

        $result2 = $this->getPgsql()->fetch("select * from company where id=$1",[1]);
        var_dump($result2);
        $this->assertNotEmpty($result2);
    }

    public function testTransaction()
    {
       $this->getPgsql()->beginTransaction();
       $this->testInsert();
       $this->getPgsql()->commit();
    }

    public function testRollback()
    {
        $this->getPgsql()->beginTransaction();
        $result = $this->getPgsql()->insert("INSERT INTO company (id,name,age,address,salary) VALUES (19,'fbcd',21,'efg',2000.00);");
        var_dump($result);
        $this->getPgsql()->rollback();

    }

    public function testInsert()
    {
        $result = $this->getPgsql()->insert("INSERT INTO company (id,name,age,address,salary) VALUES (14,'jadenf4',215,'jifjieow',1000.00) RETURNING id ;");
        var_dump($result);
        $this->assertGreaterThan(1, $result);
    }

    public function testQuery()
    {
        $result = $this->getPgsql()->query("select * from company limit 100;",[]);
        var_dump($result);
        $this->assertIsArray($result);
    }

    public function testExecute()
    {
        $result = $this->getPgsql()->execute("update company set name = $1 where id = $2",['xjj2',1]);
        var_dump($result);
        $this->assertSame(1, $result);
    }

    public function testInitSync()
    {
        $result = $this->getPgsql()->query("select init_sync($1,$2)",['wukong_app.products','php_test']);
        var_dump($result);
    }

    public function testSyncOne()
    {
        $result = $this->getPgsql()->query("select sync_task()",[]);
        var_dump($result);
    }


    private function getPgsql():Pgsql
    {
        $container = $this->getContainer();
        $container->get(PoolFactory::class);
        return $container->get(Pgsql::class);
    }

}
