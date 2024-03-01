<?php

use Elastic\Transport\Exception\NoNodeAvailableException;

class ESconnect {
	private static $client;
	
	public static function getConn() {
		if(!isset(self::$client)) {
			require 'vendor/autoload.php';
			$hosts = array("http://localhost:9200");
			self::$client = Elasticsearch\ClientBuilder::create()       // Instantiate a new ClientBuilder
	                    ->setHosts($hosts)
	                    ->setRetries(2)      // Set the hosts
	                    ->build();
	        try {
			    $reponse = self::$client->info();
			} catch (NoNodeAvailableException $e) {
			    printf("No nodes alive: %s", $e->getMessage());
			    exit;
			}            
	    }
	    return self::$client; 
	}
}

?>
