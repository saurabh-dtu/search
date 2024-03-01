<?php

require "helper.php";
$client = ESconnect::getConn();

function sendRequest($str) {
	global $client;
	$params = [
	    'index' => 'documents',
	    'body'  => [
	        'query' => [
	            'simple_query_string' => [
	                'query' => $str,
	                'fields' => ['name^3','description']
	            ]
	        ]
	    ]
	];
	if(isset($_GET['sort']) && $_GET['sort'] == 1) {
		$params['body']['sort'] = array("relevance" => array("order"=> "desc"));
	}
	if(isset($_GET['sort']) && $_GET['sort'] == 2) {
		$params['body']['sort'] = array("relevance" => array("order"=> "asc"));
	}
	$results = $client->search($params);
	return $results;
}

?>