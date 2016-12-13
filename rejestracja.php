<?php
	session_start();
	if(isset($_POST['email'])){
		$everythingOk = true;
		$DateTable = date_parse($_POST['born']);
		$born = $_POST['born'];
		$nick = $_POST['login'];
		$email = $_POST['email'];
		$emailB = filter_var($email, FILTER_SANITIZE_EMAIL);
		$pass1 = $_POST['pass1'];
		$pass2 = $_POST['pass2'];
		$pass_hash = password_hash($pass1, PASSWORD_DEFAULT);
		$sekret = '6LeMrg4UAAAAANj6HhKIVHfAWcs46Qubft5Vm7bf';
		$check = file_get_contents('https://google.com/recaptcha/api/siteverify?secret='.$sekret.'&response='.$_POST['g-recaptcha-response']);
		$anserw = json_decode($check);

		if(strlen($nick) < 3 || strlen($nick) > 30){
			$everythingOk = false;
			$_SESSION['e_login'] = "Login może posiadać od 3 do 30 znaków";
		}

		if(!ctype_alnum($nick)){
			$everythingOk = false;
			$_SESSION['e_login'] = "Login nie może posiadać znaków spejalnych lub polskich";
		}

		if(!filter_var($email, FILTER_VALIDATE_EMAIL) || $emailB != $email){
			$everythingOk = false;
			$_SESSION['e_mail'] = "Podaj poprawny email";
		}

		if(strlen($pass1) < 8 || strlen($pass1) > 20){
			$everythingOk = false;
			$_SESSION['e_pass'] = "Hasło musi posiadać od 8 do 20 znaków";
		}

		if($pass1 != $pass2){
			$everythingOk = false;
			$_SESSION['e_pass'] = "Podane hasła nie są takie same";
		}

		if(!checkdate($DateTable['month'], $DateTable['day'], $DateTable['year'])){
			$everythingOk = false;
			$_SESSION['e_date'] = 'Podaj poprawną datę np "1973-04-01"';
		}

		if(!isset($_POST['check'])){
			$everythingOk = false;
			$_SESSION['e_check'] = 'Zakceptuj regulamin';
		}

		if($anserw->success == false){
			$everythingOk = false;
			$_SESSION['e_bot'] = "Potwierdź że nie jesteś botem";
		}

		require_once "connect.php";
		mysqli_report(MYSQLI_REPORT_STRICT);

		try {
			$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
			if($polaczenie->connect_errno != 0) {
				throw new Exception(mysqli_connect_errno());
			} else {
				$rezultat = $polaczenie->query("SELECT user_email FROM users WHERE user_email = '$email'");
				if(!$rezultat) throw new Exception($polaczenie->error);

				$howMuchMails = $rezultat->num_rows;

				if($howMuchMails > 0) {
					$everythingOk = false;
					$_SESSION['e_mail'] = "Podany adres e-mail jest już przypisany do konta";
				}

				$rezultat = $polaczenie->query("SELECT user_name FROM users WHERE user_name = '$nick'");
				if(!$rezultat) throw new Exception($polaczenie->error);

				$howMuchNicks = $rezultat->num_rows;

				if($howMuchNicks > 0) {
					$everythingOk = false;
					$_SESSION['e_login'] = "Podany login jest już zajęty";
				}

				if($everythingOk){
					if($polaczenie->query("INSERT INTO users VALUES (NULL, '$nick', '$pass_hash', '$email', '$born', 1, 'regular')")){
						$_SESSION['done'] = true;
						header('Location: index.php');
					} else {
						throw new Exception($polaczenie->error);
						
					}
				}

				$polaczenie->close();
			}

		} catch(Exception $e) {
			echo '<div class = "error">błąd serwera! Przepraszamy za niedogodności i prosimy o rejestrację w innym terminie!</div>';
			echo '<p>Info dla developera</p>'.$e;
		}
	}
?>


<!DOCTYPE html>
<html lang="pl-PL">
	<head>
		<title>Zarejestruj się</title>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<script src='https://www.google.com/recaptcha/api.js'></script>
	</head>
	<body id="regbody">
		<div id="regdiv">
			<h1>Rejestracja</h1>
			<form method="post">
				<p>Podaj login (3-30 znaków)</p>
				<p><input type="text" name="login" placeholder="np. Jan"></p>
				<?php
					if(isset($_SESSION['e_login'])){
						echo '<p style="color: red; font-size: 15px;">'.$_SESSION['e_login'].'</p>';
						unset($_SESSION['e_login']);
					}
				?>
				<p>Podaj adres e-mail</p>
				<p><input type="text" name="email" placeholder="np. Jan@gmail.com"></p>
				<?php
					if(isset($_SESSION['e_mail'])){
						echo '<p style="color: red; font-size: 15px;">'.$_SESSION['e_mail'].'</p>';
						unset($_SESSION['e_mail']);
					}
				?>
				<p>Podaj hasło (8-20 znaków)</p>
				<p><input type="password" name="pass1" placeholder="hasło"></p>
				<p>Powtórz hasło</p>
				<p><input type="password" name="pass2" placeholder="powtórz hasło"></p>
				<?php
					if(isset($_SESSION['e_pass'])){
						echo '<p style="color: red; font-size: 15px;">'.$_SESSION['e_pass'].'</p>';
						unset($_SESSION['e_pass']);
					}
				?>

				<p>Podaj datę urodzenia</p>
				<p><input type="text" name="born" placeholder="RRRR-MM-DD"></p>
				<?php
					if(isset($_SESSION['e_date'])){
						echo '<p style="color: red; font-size: 15px;">'.$_SESSION['e_date'].'</p>';
						unset($_SESSION['e_date']);
					}
				?>
				<label>
					<p><input type="checkbox" name="check" id="box">Akceptacja Regulaminu</p>
				</label>
				<?php
					if(isset($_SESSION['e_check'])){
						echo '<p style="color: red; font-size: 15px;">'.$_SESSION['e_check'].'</p>';
						unset($_SESSION['e_check']);
					}
				?>
				<div class="g-recaptcha" data-sitekey="6LeMrg4UAAAAAACaMP-75Ac1gXUniFypQMXp-SUb"></div>
				<?php
					if(isset($_SESSION['e_bot'])){
						echo '<p style="color: red; font-size: 15px;">'.$_SESSION['e_bot'].'</p>';
						unset($_SESSION['e_bot']);
					}
				?>
				<p><input type="submit" value="Zarejestruj się!"></p>
			</form>
		</div>
	</body>
</html>