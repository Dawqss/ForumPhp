<?php
	session_start();

	if(!isset($_SESSION['loged'])){
		header('Location: zaloguj.php');
		exit();
	}

	require_once "connect.php";
	mysqli_report(MYSQLI_REPORT_STRICT);

	if(isset($_POST['catname']) && isset($_POST['catdesc'])){

		if($_SESSION['user_rank'] != 'admin'){
			$_SESSION['e_cat'] = 'Aby stworzyć kategorię musisz mieć status administratora';
		} else {
			try {
				$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
				$polaczenie->set_charset("utf8");
				if($polaczenie->connect_errno != 0){
					throw new Exception(mysqli_connect_errno());
				} else {
					$catname = $_POST['catname'];
					$catdesc = $_POST['catdesc'];
					$catname = htmlentities($catname, ENT_QUOTES, "UTF-8");
					$catdesc = htmlentities($catdesc, ENT_QUOTES, "UTF-8");
					$rezultat = $polaczenie->query(sprintf("INSERT INTO categories VALUES(NULL, '%s', '%s')",
						mysqli_real_escape_string($polaczenie, $catname),
						mysqli_real_escape_string($polaczenie, $catdesc)));
					
					if(!$rezultat) {
						throw new Exception($polaczenie->error);
					} else {
						$_SESSION['i_cat'] = 'Stworzyłeś nową kategorię!';
					}
					$polaczenie->close();
				}

			} catch(Exception $e){
				$_SESSION['e_error'] = $e;
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
		<section id="catSection">
			<?php
				if(isset($_SESSION['e_error'])){
					echo '<p style="color: red;">'.$_SESSION['e_error'].'</p>';
					unset($_SESSION['e_error']);
					}
			?>
			<form method="post">
				<p>Nazwa Kategori:</p>
				<p><input type="text" name="catname"></p>
				<p>Opis Kategori:</p>
				<p><input type="text" name="catdesc"></p>
				<p><input type="submit" value="Stwórz kategorię"></p>
				<?php
					if(isset($_SESSION['e_cat'])){
						echo '<p style="color: red;">'.$_SESSION['e_cat'].'</p>';
						unset($_SESSION['e_cat']);
						}
					if(isset($_SESSION['i_cat'])){
						echo '<p style="color: red;">'.$_SESSION['i_cat'].'</p>';
						unset($_SESSION['i_cat']);
					}	
				?>
			</form>
		</section>
		<footer>
			<p>Copyrights Dawid Wyrkowski 2016</p>
		</footer>
	</body>
</html>