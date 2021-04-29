<?php

//Connect to Redis
function connectToRedis() {
	$redis = new Redis();
	$redis->connect('127.0.0.1', 6379);
	$redis->auth('justfortest');
	return $redis;
}

//Check if current IP key exists
function checkIpExists($conn, $ip) {
	return $conn->exists($ip);
}

//Show all keys
function showAllKeys($conn) {
	$allKeys = $conn->keys("*");

	foreach ($allKeys as $key) {
		$value = $conn->get($key);
		echo "$key => $value<br>";
	}
}

//Process IP
function processIp($conn, $ip) {
	$ipExists = checkIpExists($conn, $ip);

	//If current IP key exists
	if ($ipExists) {
		//Increment counter by 1
		$connCount = $conn->get($ip);
		$conn->set($ip, $connCount + 1);
	} else {
		//Else add new key with value of 1
		$conn->set($ip, 1);
	}
}

//Process client
function processClient() {
	$conn = connectToRedis();

	//If connected directly to Apache
	$ip = $_SERVER['REMOTE_ADDR'];
	//If there is Apache+Nginx or other proxy
	//$ip = $_SERVER['HTTP_X_FORWARDED_FOR']

	processIp($conn, $ip);
	showAllKeys($conn);

	$conn->close();
}

//Clear all key data
function clearData() {
	$conn = connectToRedis();

	$conn->flushAll();

	$conn->close();

	echo "Cleanup done!<br>";
}

if (isset($_POST['submit'])) {
	clearData();
} else {
	processClient();
}

?>

<form action="test2.php" method="POST">
	<input type="submit" name="submit" value="Clear">
</form>
