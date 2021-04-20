<?php

//Подключение БД
function connectDB() {
	$dbhost = "localhost";
	$dbuser = "root";
	$dbpass = "";
	$conn = new mysqli($dbhost, $dbuser, $dbpass) or die("Connect failed: %s\r\n". $conn -> error);

	return $conn;
}

//Создание базы данных interview_test1 если ее не существует
function createDB() {
	$conn = connectDB();

	$query = "CREATE DATABASE IF NOT EXISTS `interview_test1`";

	if (!$conn->query($query)) {
		echo "Error: " . $conn->error;
	}

	$conn->close();
}

//Создание таблицы messages если ее не существует
function createTableMessages() {
	$conn = connectDB();

	$query = "CREATE TABLE IF NOT EXISTS `interview_test1`.`messages`(`id` int not null auto_increment, `message` varchar(1024), `datetime` datetime, PRIMARY KEY (id)) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

	if (!$conn->query($query)) {
		echo "Error: " . $conn->error;
	}

	$conn->close();
}


//Отправка сообщения в БД
function sendMessage() {
	if (isset($_POST['submit'])) {
		$conn = connectDB();

		$query = "INSERT INTO `interview_test1`.`messages`(`message`, `datetime`) VALUES ('" . $_POST['message'] . "'" . ", " . "NOW()" . ");";

		if (!$conn->query($query)) {
			echo "Error: " . $conn->error;
		}

		$conn->close();
	}
}

//Получение данных о сообщениях из БД
function getMessages() {
	$conn = connectDB();

	$query = "SELECT * FROM `interview_test1`.`messages` ORDER BY datetime DESC;";
	$result = $conn->query($query);
	if (!$result) {
		echo "Error: " . $conn->error;
	}

	$conn->close();

	return mysqli_fetch_all($result);
}

//Отрисовка всей таблицы
function printTableWMessages () {
	//Заголовок
	echo '<table class="table"><tr><th>id</th><th>message</th><th>datetime</th></tr>';
	//Данные
	printTableContents(getMessages());

	echo '</table>';
}

//Отрисовка содержимого таблицы
function printTableContents($data) {
	foreach ($data as $row) {
		printTableRow($row);
	}
}

//Отрисовка одного ряда
function printTableRow($row) {
	echo "<tr>";
	foreach ($row as $col) {
		echo "<td>$col</td>";
	}
	echo "</tr>";
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Interview Test1</title>
</head>
<style type="text/css">
	table, th, td {
		padding: 5px;
		border: 1px black solid;
		border-collapse: collapse;
	}
	table {
		margin-bottom: 10px;
	}
</style>
<body>
	<?php
	createDB();
	createTableMessages();
	sendMessage();
	printTableWMessages(); 
	?>
	<form action="test1.php" method="POST">
		<div><textarea rows="5" cols="25" placeholder="Введите сообщение..." name="message"></textarea></div>
		<input type="submit" name="submit">
	</form>
</body>
</html>