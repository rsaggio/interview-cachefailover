<?php

namespace AppBundle\Tests\Dao;

use phpunit\framework\TestCase;
use AppBundle\Dao\CustomersDao;

class RedisCacheServiceTest extends TestCase
{

    private $customersDao;
    private $cacheService;
    private $mongoCollection;


    public function setUp() {

        $this->cacheService = $this->getMockBuilder('AppBundle\Service\CacheService')->disableOriginalConstructor()->getMock();

        $databaseService = $this->getMockBuilder('AppBundle\Service\DatabaseService')->disableOriginalConstructor()->getMock();

        $this->mongoCollection = $this->getMockBuilder('MongoCollection')->setMethods(['find','get','insert','drop'])->disableOriginalConstructor()->getMock();

        $databaseService->method('getCollection')->willReturn($this->mongoCollection);

        $this->customersDao = new CustomersDao($databaseService,$this->cacheService);
 
    }

    public function testIfCacheIsDownGetFromDatabase()
    {
        $this->cacheService->method('isAvaliable')->willReturn(false);

        $this->mongoCollection->method('find')->willReturn(new \ArrayObject());

        $this->mongoCollection->expects($this->once())->method('find');

        $this->customersDao->getAll();

    }

    
    
    public function testIfCacheIsUpAndHaveDataGetFromCache() {

        $this->cacheService->method('isAvaliable')->willReturn(true);      

        $this->cacheService->method('get')->willReturn('[{"name":"leandro", "age":26}]');        

        $this->mongoCollection->expects($this->never())->method('find');

        $this->customersDao->getAll();

    }

    
    public function testIfCacheIsEmptyGetFromDatabase() {

        $this->cacheService->method('isAvaliable')->willReturn(true);      

        $this->cacheService->method('get')->willReturn([]);        

        $this->mongoCollection->method('find')->willReturn(new \ArrayObject());

        $this->mongoCollection->expects($this->once())->method('find');

        $this->customersDao->getAll();
    }

    
    public function testIfInsertCustomerUpdateCache() {

        $this->cacheService->method('isAvaliable')->willReturn(true);      

        $this->mongoCollection->expects($this->once())->method('insert');

        $this->cacheService->expects($this->once())->method('set');

        $this->customersDao->insert('[{"name":"leandro", "age":26}]');

    }

    
    public function testIfDropCustomersDropCache() {

        $this->cacheService->method('isAvaliable')->willReturn(true);              

        $this->cacheService->expects($this->once())->method('del');

        $this->mongoCollection->expects($this->once())->method('drop');

        $this->customersDao->drop();

    }

    
}
