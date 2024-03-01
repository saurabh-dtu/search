
<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//echo ;die;
?>

<!DOCTYPE html>
<html>
<body>

<h2>Check24 Search Forms</h2>
<?php if(!empty($_GET['search'])) { ?>
<a href=<?php echo "'" . "http://" . $_SERVER['HTTP_HOST'] . "/index.php?search=" . $_GET['search'] . "&s=Submit&sort=2" . "'"; ?>>Sort by relevance asc</a>
<br><a href=<?php echo "'" . "http://" . $_SERVER['HTTP_HOST'] . "/index.php?search=" . $_GET['search'] . "&s=Submit&sort=1" . "'"; ?>>Sort by relevance desc</a>
<?php }
?>
<form action="/index.php" method="get">
  <label for="search">Search :</label><br>
  <input type="text" id="search" name="search" placeholder="Search" <?php if(!empty($_GET['search'])) { echo "value='" . $_GET['search'] . "'"; } ?>><br>
  <input type="submit" name='s' value="Submit">
</form> 
<br>

<?php

if(!empty($_GET['search'])) {
	$s = trim($_GET['search']);
	if(!empty($s)) {
		include("getResult.php");
		$response = sendRequest($_GET['search']);
		if(!empty($response)) {
			displayResult($response);
		}
	} else {
		echo "Search string is empty";
	}
}

function displayResult($response) {
	$total = $response['hits']['total']['value'];
	if($total > 0) {
		$doc = $response['hits']['hits'];
		foreach($doc as $value) {
			?>
			<div class="show" align="left">
                <a href=<?php echo "'" . $value['_source']['url'] . "'"; ?> target="_blank" title=<?php echo "'Manufacturer: " . $value['_source']['manufacturerName'] . "\nPrice: " . $value['_source']['price'] . "'"; ?>><img src=<?php echo "'" . $value['_source']['image'] . "'"; ?> style="width:50px; height:50px; float:left; margin-right:6px;" /><span class="name"><?php echo $value['_source']['name']; ?></span></a>&nbsp;<br/><?php echo substr($value['_source']['description'],50) . "..."; ?>
            </div>
            <br><br>
	<?php	}
	} else {
		echo "No result found.";
	}
}

?>



</body>
</html>
