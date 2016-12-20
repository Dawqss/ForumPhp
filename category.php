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
		<section id="category">
			<?php
				mysqli_report(MYSQLI_REPORT_STRICT);
				$cat_id = $_GET['cat_id'];
				$cat_id = htmlentities($cat_id, ENT_QUOTES, "UTF-8");
				try{
					require_once "connect.php";
					$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
					$polaczenie->set_charset("utf8");
					if(!$polaczenie){
						throw new Exception(mysqli_connect_errno());
					} else {
						$rezultat = $polaczenie->query("SELECT cat_id, cat_name, cat_description FROM categories WHERE cat_id = '$cat_id'");
						if(!$rezultat){
							throw new Exception($polaczenie->error);
						} else {
							$howMuchRows = $rezultat->num_rows;
							if($howMuchRows == 0){
								$_SESSION['no_cat'] = "Nie ma takiej kategori";
							} else {
								$row = $rezultat->fetch_assoc();
								echo '<h2>Tematy w katergori '.$row['cat_name'].'</h2>';
								$rezultat = $polaczenie->query("SELECT topic_id, topic_subject, topic_date, topic_cat FROM topics WHERE topic_cat = '$cat_id'");

								if(!$rezultat){
									throw new Exception($polaczenie->error);
								} else {
									$howMuchRows = $rezultat->num_rows;

									if($howMuchRows == 0){
										$_SESSION['no_top'] = "W tej kategori nie istnieją jeszcze żadne tematy";
									} else {
										echo '<table>';
										echo	'<tr>';
										echo		'<th>Temat</th>';
										echo		'<th>Data stworzenia</th>';
										echo 	'</tr>';
											while($row = $rezultat->fetch_assoc()){
												echo '<tr>';
													echo '<td>';
														echo '<h3><a href="topic.php?id='.$row['topic_id'].'">'.$row['topic_subject'].'</a></h3>';
													echo '</td>';
													echo '<td>';
														echo date('d-m-Y', strtotime($row['topic_date']));
													echo '</td>';
												echo '</tr>';		
											}
										echo '</table>';
									}
								}
							}
						}
						$polaczenie->close();
					}
				} catch (Exception $e) {
					$_SESSION['e'] = $e;
				}

				if(isset($e)){
					echo $e;
					unset($e);
				}

				if(isset($_SESSION['no_cat'])){
					echo $_SESSION['no_cat'];
					unset($_SESSION['no_cat']);
				}
				if(isset($_SESSION['no_top'])){
					echo '<h3>'.$_SESSION['no_top'].'</h3>';
					unset($_SESSION['no_top']);
				}
			?>
		</section>
		<footer>
			<p>Copyrights Dawid Wyrkowski 2016</p>
		</footer>
	</body>
</html>