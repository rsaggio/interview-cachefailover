<?php 

namespace AppBundle\Service;

interface CacheService {

	public function get($key);

	public function set($key,$value);

	public function del($key);

	public function isAvaliable();
}
