<?php

require "helper.php";
$client = ESconnect::getConn();

$settingMapper = [
    'index' => 'documents',
    'body' => [
        'settings' => [
            'number_of_shards' => 2,
            'number_of_replicas' => 1,
            /*'tokenizer' => [
            	'text_ngram_tokenizer' => [
            		'type' => 'ngram',
            		'min_gram' => 3,
            		'max_gram' => 20
            	]
            ],*/
            'analysis' => [
            	'analyzer' => [
                    'text_analyzer' => [
                        'type' => 'custom',
                        'tokenizer' => 'standard',
                        'filter' => ['lowercase', 'stop', 'kstem']
                    ]/*,
                    'text_ngram_analyzer' => [
                        'type' => 'custom',
                        'tokenizer' => 'text_ngram_tokenizer',
                        'filter' => ['lowercase']
                    ]*/
                ]
            ]
        ],
        'mappings' => [
            '_source' => [
                'enabled' => true
            ],
            'properties' => [
                'name' => [
                    'type' => 'text',
                    'analyzer' => 'text_analyzer'
                ],
                'description' => [
                    'type' => 'text',
                    'analyzer' => 'text_analyzer'
                ],
                'manufacturerName' => [
                    'type' => 'text'
                ],
                'category' => [
                    'type' => 'text'
                ],
                'relevance' => [
                    'type' => 'double'
                ],
                'url' => [
                    'type' => 'text'
                ],
                'image' => [
                    'type' => 'text'
                ],
                'price' => [
                    'type' => 'double'
                ]
            ]
        ]
    ]
];
$params = array('index' => 'documents');
if(isset($_GET['delete']) && $_GET['delete'] == 1) {
	if($client->indices()->exists($params)) {
		$response = $client->indices()->delete($params);
		echo "Index deleted successfully";
	} else {
		echo "Index already deleted";
	}
	exit;
}
if(empty($_GET['delete'])) {
	if(!$client->indices()->exists($params)) {
		$response = $client->indices()->create($settingMapper);
	} else {
		echo "Index already exist please delete documents index";
		exit;
	}
}

indexRecords();
//fetch json file and process it
function readJson() {
	$json = file_get_contents('search-data.json');
  	// Decode the JSON file
	$json_data = json_decode($json, true);
  	return $json_data;
}

//bulk insertion in batches of 1000
function indexRecords() {
	global $client;
	$json_data = readJson();
	$params = ['body' => []];

	$i = 1;
	foreach($json_data as $values) {
	    $params['body'][$i] = [
	        'index' => [
	            '_index' => 'documents',
	            '_id'    => $i
	        ]
	    ];

	    foreach($values as $key => $value) {
		    $params['body'][$i][$key] = $value;
		}

	    if ($i % 1000 == 0) {
	        $responses = $client->bulk($params);
	        $params = ['body' => []];
	        unset($responses);
	    }
	    $i++;
	}//echo "<pre>";print_r($params['body']);//exit;
	if (!empty($params['body'])) {
	    $responses = $client->bulk($params);
	}
}

?>