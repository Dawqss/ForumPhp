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
				require_once "connect.php";
				mysqli_report(MYSQLI_REPORT_STRICT);	
				try{
					$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
					$polaczenie->set_charset("utf8");
					if($polaczenie->errno != 0){
						throw new Exception(mysqli_connect_errno());
						$error = "Problem z połączeniem z bazą danych";
					} else {
						$rezultat = $polaczenie->query("SELECT cat_id, cat_name, cat_description FROM categories");
						$howMuchRows = $rezultat->num_rows;
						if($howMuchRows = 0){
							$_SESSION['no_cat'] = "Nie istnieją jeszcze żadne kategorie";
								echo '<tr><td>'.$_SESSION['no_cat'].'</td></tr>';
						} else {
							echo '<table>';
							echo	'<tr>';
							echo		'<th>Kategoria</th>';
							echo		'<th>Ostatni Temat</th>';
							echo 	'</tr>';
							
							while($row = $rezultat->fetch_assoc()){
								echo '<tr>';
									echo '<td>';
										echo '<h3><a href="category.php?cat_id='.$row['cat_id'].'">'. $row['cat_name'].'</a></h3>'
										.$row['cat_description'];
									echo '</td>';
									echo '<td>';
										echo '<a href="topic.php?id=">Topic subject</a> at 10-10';
									echo '</td>';
								echo '</tr>';				
							}
							echo '</table>';
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
			?>
		</section>
		<footer>
			<p>Copyrights Dawid Wyrkowski 2016</p>
		</footer>
	</body>
</html>