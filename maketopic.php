<?php
	session_start();
	if(!isset($_SESSION['loged'])){
		header('Location: zaloguj.php');
		exit();
	}

	if(isset($_POST['topic'])) {
		if(strlen($_POST['topic']) < 3){
			$_SESSION['e_topic'] = "Temat musi zawierać więcej niż 3 znaki";
		} else {
			if(strlen($_POST['post_content']) < 10){
				$_SESSION['e_post'] = "Treść musi zawierać więcej niż 10 znaków";
			} else {
				require_once "connect.php";
				mysqli_report(MYSQLI_REPORT_STRICT);
				try{
					$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
					$polaczenie->set_charset("utf8");
					if($polaczenie->connect_errno != 0){
						throw new Exception(mysqli_connect_errno());
					} else {
						$rezultat = $polaczenie->query("BEGIN WORK;");
						if(!$rezultat){
							throw new Exception($polaczenie->error);
						} else {
							$topic = $_POST['topic'];
							$post_content = $_POST['post_content'];
							$topic_cat = $_POST['topic_cat'];
							$user_id = $_SESSION['user_id'];
							$topic = htmlentities($topic, ENT_QUOTES, "UTF-8");
							$post_content = htmlentities($post_content, ENT_QUOTES, "UTF-8");
							$rezultat = $polaczenie->query(sprintf("INSERT INTO topics VALUES(NULL, '%s', NOW(), '$topic_cat', '$user_id')", mysqli_real_escape_string($polaczenie, $topic)));
							if(!$rezultat){
								throw new Exception($polaczenie->error);
								$rezultat = $polaczenie->query("ROLLBACK;");
							} else {
								$topicid = $polaczenie->insert_id;
								$rezultat = $polaczenie->query(sprintf("INSERT INTO posts VALUES(NULL, '%s', NOW(), '$topicid', '$user_id')",
									mysqli_real_escape_string($polaczenie, $post_content)));
								if(!$rezultat){
									throw new Exception($polaczenie->error);
									$rezultat = $polaczenie->query("ROLLBACK;");
								} else {
									$rezultat = $polaczenie->query("COMMIT;");
									$_SESSION['d_topic'] = 'Udało Ci się stworzyć nowy temat <a href="topic.php?id='. $topicid . '">Twój nowy temat</a>.';
								}
							}
						}
						$polaczenie->close();
					}

				} catch (Exception $e){
					$_SESSION['e'] = $e;
				}
			}
		}
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
		<section id="topSection">
		<?php
			require_once "connect.php";
			mysqli_report(MYSQLI_REPORT_STRICT);
			try{
				$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
				$polaczenie->set_charset("utf8");
				if(!$polaczenie){
					throw new Exception(mysqli_connect_errno()); 
				} else {
					$rezultat = $polaczenie->query("SELECT cat_id, cat_name, cat_description FROM categories");
					if(!$rezultat){
						throw new Exception($polaczenie->error);
					} else {
						$howMuchRows = $rezultat->num_rows;
						if($howMuchRows == 0){
							echo 'Nie ma jeszcze żadnej kategori!';
						} else {
							echo '<form method = "Post">';
							echo '<p>Temat:</p>';
							echo '<p><input type = "text" name = "topic"></p>';
								if(isset($_SESSION['e_topic'])){
									echo '<p style="color: red; font-size: 15px;">'.$_SESSION['e_topic'].'</p>';
									unset($_SESSION['e_topic']);
								}
							echo '<p>Kategoria:</p>';
							echo '<select name = "topic_cat">';
							while($row = $rezultat->fetch_assoc()){
								echo '<option value ="'.$row['cat_id'].'">'.$row['cat_name'].'</option>';
							}
							echo '</select>';
							echo '<p>Treść:</p>';
							echo '<textarea name = "post_content"></textarea>';
								if(isset($_SESSION['e_post'])){
									echo '<p style="color: red; font-size: 15px;">'.$_SESSION['e_post'].'</p>';
									unset($_SESSION['e_post']);
								}
							echo '<p><input type = "submit" value = "stwórz temat"></p>';
								if(isset($_SESSION['d_topic'])){
									echo '<p style="color: red; font-size: 15px;">'.$_SESSION['d_topic'].'</p>';
									unset($_SESSION['d_topic']);
								}
						}
					}
				}
			} catch (Exception $e){
				$_SESSION['e'] = $e;
			}
			if(isset($e)){
				echo $e;
				unset($e);
			}
		?>
		</section>
		<footer>
			<p>Copyrights Dawid Wyrkowski 2016</p>
		</footer>
	</body>
</html>