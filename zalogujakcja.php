<?php
	session_start();
	if(!isset($_POST['login'])){
		header('Location: zaloguj.php');
	}

	if(!isset($_SESSION['loged'])){
		header('Location: index.php');
	}

?>