<?php
	if(!isset($_POST['login'])){
		header('Location:zaloguj.php');
	}
	session_start();
	require_once "connect.php";
	mysqli_report(MYSQLI_REPORT_STRICT);

	try{
		$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
		if($polaczenie->connect_errno != 0){
			throw new Exception(mysqli_connect_errno());
		} else {
			$login = $_POST['login'];
			$password = $_POST['pass'];
			
			$login = htmlentities($login, ENT_QUOTES, "UTF-8");
			$rezultat = $polaczenie->query(sprintf("SELECT * FROM users WHERE user_name = '%s'",
				mysqli_real_escape_string($polaczenie, $login)));
			if(!$rezultat) throw new Exception($polaczenie->error);

			$howMuchUsers = $rezultat->num_rows;
			if($howMuchUsers > 0){
				$row = $rezultat->fetch_assoc();

				if(password_verify($password, $row['user_pass'])){
					$_SESSION['user_id'] = $row['user_id'];
					$_SESSION['user_name'] = $row['user_name'];
					$_SESSION['user_rank'] = $row['user_rank'];
					$_SESSION['user_level'] = $row['user_level'];
					$_SESSION['user_date'] = $row['user_date'];
					$_SESSION['user_email'] = $row['user_email'];
					$_SESSION['loged'] = true;
					header('location: index.php');
					$rezultat->close();
				} else {
					$_SESSION['error'] = '<span style="color: red; font-size: 15px;">Nieprawidłowy login lub hasło!</span>';
					header('Location:zaloguj.php');
				}
			} else {
				$_SESSION['error'] = '<span style="color: red; font-size: 15px;">Nieprawidłowy login lub hasło!</span>';
				header('Location:zaloguj.php');
			}
		}

	} catch(Exception $e){
		$_SESSION['e'] = $e;
	}
?>