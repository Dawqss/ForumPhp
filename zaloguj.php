<?php
	session_start();
	if(isset($_SESSION['done'])){
		echo '<h2>Gratujację! Teraz możesz się zalogować </h2>';
		unset($_SESSION['done']);
	}
	if(isset($_SESSION['loged'])){
		header('Location:index.php');
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
		<?php
			if(isset($_SESSION['e'])){
				echo $_SESSION['e'];
			}
		?>
		<div id="logdiv">
			<form method="post" action="zalogujakcja.php">
				<p>Login:</p>
				<p><input type="text" name="login" placeholder="tutaj wpisz login"></p>
				<p>Hasło:</p>
				<p><input type="password" name="pass" placeholder="tutaj wpisz hasło"></p>
				<?php
					if(isset($_SESSION['error'])){
						echo $_SESSION['error'];
					}
				?>
				<p><input type="submit" value="zaloguj się"></p>
				<a href="rejestracja.php">zarejestruj się</a>
			</form>
		</div>
	</body>
</html>