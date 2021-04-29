<?php

//Connect DB
function connectDB() {
	$dbhost = "localhost";
	$dbuser = "root";
	$dbpass = "";
	$conn = new mysqli($dbhost, $dbuser, $dbpass) or die("Connect failed: %s\r\n". $conn -> error);

	return $conn;
}

//Create DB interview_test1 if not exists
function createDB() {
	$conn = connectDB();

	$query = "CREATE DATABASE IF NOT EXISTS `interview_test1`";

	if (!$conn->query($query)) {
		echo "Error: " . $conn->error;
	}

	$conn->close();
}

//Create table messages if not exists
function createTableMessages() {
	$conn = connectDB();

	$query = "CREATE TABLE IF NOT EXISTS `interview_test1`.`messages`(`id` int not null auto_increment, `message` varchar(1024), `datetime` datetime, PRIMARY KEY (id)) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

	if (!$conn->query($query)) {
		echo "Error: " . $conn->error;
	}

	$conn->close();
}


//Send message to DB
function sendMessage() {
	if (isset($_POST['submit'])) {
		$conn = connectDB();

		$query = "INSERT INTO `interview_test1`.`messages`(`message`, `datetime`) VALUES ('" . mysql_real_escape_string($_POST['message']) . "'" . ", " . "NOW()" . ");";

		if (!$conn->query($query)) {
			echo "Error: " . $conn->error;
		}

		$conn->close();
	}
}

//Get message data from DB
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

//Draw table
function printTableWMessages () {
	//Header
	echo '<table class="table"><tr><th>id</th><th>message</th><th>datetime</th></tr>';
	//Data
	printTableContents(getMessages());

	echo '</table>';
}

//Draw table contents
function printTableContents($data) {
	foreach ($data as $row) {
		printTableRow($row);
	}
}

//Draw single row
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
		<div><textarea rows="5" cols="25" placeholder="Input message..." name="message"></textarea></div>
		<input type="submit" name="submit">
	</form>
</body>
</html>
