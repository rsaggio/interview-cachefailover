<?php

namespace AppBundle\Service;

use Predis;
use Predis\Connection\ConnectionException;

/**
* Here you have to implement a CacheService with the operations above.
* It should contain a failover, which means that if you cannot retrieve
* data you have to hit the Database.
**/
class RedisCacheService implements CacheService
{

    private $client;

    public function __construct($host, $port, $prefix)
    {
        $this->client = new Predis\Client([
            'scheme' => 'tcp',
            'host'   => '127.0.0.1',
            'port'   => 6379,
        ]);
    }


    public function get($key) {

        $customers = null;

        if($this->isAvaliable()) {

            $customers = $this->client->get($key); 

        }

        return unserialize($customers);
    }

    public function set($key,$customer) {

        if($this->isAvaliable()) {

            $customers = $this->get($key);

            if(!$customers) {
                $customers = [];
            }

            if(!empty($customer)) {
                array_push($customers,$customer);
            }

            $this->client->set($key,serialize($customers));
        }

    }

    public function del($key)
    {  
        $this->client->del($key);
    }

    public function isAvaliable() {

        $avaliable = null;

        try {
            $avaliable = $this->client->ping();
        }catch(ConnectionException $e) {

            $avaliable = false;
        }

        return $avaliable;
    }

}
