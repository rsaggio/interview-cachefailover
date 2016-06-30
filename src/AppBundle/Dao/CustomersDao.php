<?php 

namespace AppBundle\Dao;

use AppBundle\Service\DatabaseService;
use AppBundle\Service\cacheService;

class CustomersDao {

	private $database;
	private $cacheService;
	const cacheKey = "customers";

	public function __construct(DatabaseService $dbService, CacheService $cacheService) {
		$this->database = $dbService->getCollection(self::cacheKey);
		$this->cacheService = $cacheService;
	}

	public function insert($customer) {

		$this->database->insert($customer);

		$this->cacheService->set(self::cacheKey,$customer);

	}

	public function drop() {
		$this->cacheService->del(self::cacheKey);
		$this->database->drop();

	}

	public function getAll() {
		
		$customers = $this->cacheService->get(self::cacheKey);

		if(empty($customers)) {

			$customers = iterator_to_array($this->database->find());

			$this->cacheService->set(self::cacheKey,$customers);

		}

		return $customers;
	}


}