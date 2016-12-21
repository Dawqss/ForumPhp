<?php
	session_start();
	$post_content = $_POST['reply_content'];
	$post_content = htmlentities($post_content, ENT_QUOTES, "UTF-8");
	$post_topic = $_SESSION['topicid'];
	$post_by = $_SESSION['user_id'];
	if(strlen($post_content) < 10){
		$_SESSION['e_pcont'] = "Post musi zawierać więcej niż 10 znaków";
		header('Location:topic.php?id='.$post_topic);
	} else {
		require_once "connect.php";
		try{
			$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
			$polaczenie->set_charset('utf8');

			if($polaczenie->connect_errno != 0){
				throw new Exception(mysqli_connect_errno());
			} else {
				$rezultat = $polaczenie->query("INSERT INTO posts VALUES (NULL, '$post_content', NOW(), '$post_topic', '$post_by')");
				if(!$rezultat){
					throw new Exception($polaczenie->error);
				} else {
					$_SESSION['d_post'] = 'Post został dodany';
					header('Location:topic.php?id='.$post_topic);
				}
			}

		} catch(Exception $e) {
			$_SESSION['e'] = $e;

			if(isset($_SESSION['e'])){
				header('Location:topic.php?id='.$post_topic);
			}
		}
	}
?>