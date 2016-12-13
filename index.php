<?php
	session_start();
	if(!isset($_SESSION['loged'])){
		header('Location: zaloguj.php');
		exit();
	}

	if(isset($_SESSION['done'])){
		echo '<div><h1> Gratujację! Teraz możesz się zalogować </h1></div>';
		unset($_SESSION['done']);
	}
?>