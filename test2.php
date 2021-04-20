<?php

//Подключение к Redis
function connectToRedis() {
	$redis = new Redis();
	$redis->connect('127.0.0.1', 6379);
	$redis->auth('justfortest');
	return $redis;
}

//Проверить, существует ли запись с ключом "IP клиента"
function checkIpExists($conn, $ip) {
	return $conn->exists($ip);
}

//Вывести все записи
function showAllKeys($conn) {
	$allKeys = $conn->keys("*");

	foreach ($allKeys as $key) {
		$value = $conn->get($key);
		echo "$key => $value<br>";
	}
}

//Обработать IP
function processIp($conn, $ip) {
	$ipExists = checkIpExists($conn, $ip);

	//Если запись с ключом "IP клиента" существует
	if ($ipExists) {
		//Увеличить значение на 1
		$connCount = $conn->get($ip);
		$conn->set($ip, $connCount + 1);
	} else {
		//Иначе добавить запись со значением 1
		$conn->set($ip, 1);
	}
}

//Обработать клиент
function processClient() {
	$conn = connectToRedis();

	//Обращаюсь напрямую к Apache
	$ip = $_SERVER['REMOTE_ADDR'];
	//В случае Apache+Nginx или иного прокси
	//$ip = $_SERVER['HTTP_X_FORWARDED_FOR']

	processIp($conn, $ip);
	showAllKeys($conn);

	$conn->close();
}

//Очистить список IP (для наглядности)
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
	<input type="submit" name="submit" value="Очистить">
</form>