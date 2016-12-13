<?php
	mysqli_report(MYSQLI_REPORT_STRICT);
	session_start();
	if(isset($_SESSION['loged'])){
		header('Location: index.php');
		exit();
	}
?>


<!DOCTYPE html>
<html lang="pl-PL">
	<head>
		<title>Witaj na moim forum!</title>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="css/style.css">
	</head>
	<body id="logbody">
		<h1>Logowanie</h1>
		<div id="logdiv">
			<form method="post" action="zalogujakcja.php">
				<p>Login:</p>
				<p><input type="text" name="login" placeholder="tutaj wpisz login"></p>
				<p>Hasło:</p>
				<p><input type="password" name="pass" placeholder="tutaj wpisz hasło"></p>
				<p><input type="submit" value="zaloguj się"></p>
				<a href="rejestracja.php">zarejestruj się</a>
			</form>
		</div>
	</body>
</html>