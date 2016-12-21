<?php
	session_start();
	if(!isset($_SESSION['loged'])){
		header('Location: zaloguj.php');
		exit();
	}
?>


<!DOCTYPE html>
<html>
	<head>
		<title>Forum</title>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="css/style.css">
	</head>
	<body id="index">
		<header>
			<nav>
				<ul>
					<li>
						<a href="index.php">Strona Główna</a>  
					</li>
					<li>
						<a href="makecat.php">Stwórz Kategorię</a>
					</li>
					<li>
						<a href="maketopic.php">Stwórz Temat</a>
					</li>
				</ul>
				<ul>
					<li>
						<p>Witaj <?php echo $_SESSION['user_name'];?> !</p>
					</li>
					<li>
						<a href="logout.php">Wyloguj się</a>
					</li>
				</ul>
			</nav>
		</header>
		<section id="indexSection">
			<?php
				$topicid = $_GET['id'];
				$topicid = htmlentities($topicid, ENT_QUOTES, "UTF-8");
				$_SESSION['topicid'] = $topicid;
				require_once "connect.php";
				mysqli_report(MYSQLI_REPORT_STRICT);
				try {
					$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
					$polaczenie->set_charset("utf8");
					if($polaczenie->connect_errno != 0){
						throw new Exception(mysqli_connect_errno());
					} else {
						$rezultat = $polaczenie->query("SELECT topic_id, topic_subject FROM topics WHERE topic_id = '$topicid'");
						if(!$rezultat){
							throw new Exception($polaczenie->error);
						} else {
							$howMuchRows = $rezultat->num_rows;
							if($howMuchRows == 0){
								$_SESSION['no_topic'] = "Nie istnieje temat o takim id";
							} else {
								$row = $rezultat->fetch_assoc();
								echo '<table>';
								echo 	'<tr>';
								echo 		'<th colspan="2">';
								echo 			$row['topic_subject'];
								echo 		'</th>';
								echo 	'</tr>';
								$rezultat = $polaczenie->query("SELECT posts.post_topic, posts.post_content, posts.post_date, posts.post_by, users.user_id, users.user_name FROM posts LEFT JOIN users ON posts.post_by = users.user_id WHERE posts.post_topic = '$topicid'");
								if(!$rezultat){
									throw new Exception($polaczenie->error);
								} else {
									if ($rezultat->num_rows == 0){
										echo '<tr>';
										echo 	'<td>';
										echo 		'<p>W tym temacie nie ma jeszcze żadnych wypowiedzi</p>';
										echo 	'</td>';
										echo '</tr>';
									} else {
										while($row = $rezultat->fetch_assoc()){
										echo '<tr>';
										echo 	'<td class="topic">';
										echo 		'<p>'.$row['user_name'].'</p>'.$row['post_date'];
										echo 	'</td>';
										echo 	'<td>';
										echo 		'<p>'.$row['post_content'].'</p>';
										echo 	'</td>';
										echo '</tr>';		
										}
									}
								}
								echo '</table>';
								echo '<form method = "POST" action = "reply.php">';
								echo '<textarea name = "reply_content" placeholder = "Tutaj wpisz treść posta"></textarea>';
								if(isset($_SESSION['e_pcont'])){
									echo '<p style = "color: red; font-size: 15px;">'.$_SESSION['e_pcont'].'</p>';
									unset($_SESSION['e_pcont']);
								}
								if(isset($_SESSION['d_post'])){
									echo '<p style = "color: red; font-size: 15px;">'.$_SESSION['d_post'].'</p>';
									unset($_SESSION['d_post']);
								}
								echo '<p><input type = "submit" value = "Wyślij"></p>';
								echo '</form>';
							}
						}
						$polaczenie->close();
					}

				} catch(Exception $e){
					$_SESSION['e'] = $e;
				}
				if(isset($_SESSION['e'])){
					echo $_SESSION['e'];
					unset($_SESSION['e']);
				}
			?>
		</section>
		<footer>
			<p>Copyrights Dawid Wyrkowski 2016</p>
		</footer>
	</body>
</html>