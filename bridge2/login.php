<!-- 
   EarTraining, a web application for people who want to train their ear for music 
   Copyright (C) 2020  Emanuele De Santis
   EarTraining is free software: you can redistribute it and/or modify
   it under the terms of the GNU Affero General Public License as published
   by the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.
   EarTraining is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU Affero General Public License for more details.
   You should have received a copy of the GNU Affero General Public License
   along with EarTraining.  If not, see <https://www.gnu.org/licenses/>.
-->

<?php 

require_once('connection.php');
session_start();

if (!isset($_SESSION['current_page'])) $_SESSION['current_page'] = '../index.php';

$username = "";
$password = "";

?>

<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head><title>Bridge</title>
<meta name="viewport" content="viewport">
<link rel="shortcut icon" type="image/svg" href="../MySite/icon.svg">
<link rel="stylesheet" type="text/css" href="login.css">
<link rel="stylesheet" type="text/css" href="https://use.fontawesome.com/releases/v5.13.0/css/all.css"> 
</head>
<body>
	<div class="topnav">
	<ul class="menu">
		<li class="icons"><a href="../index.php">&#x1f3e1;</a></li>
		<li><a href="../MySite/aboutme.html">About Me</a></li>
		<li class="icons"><a href="#"><i class="fab fa-facebook-square"></i></a></li>
		<li class="icons"><a href="#"><i class="fab fa-instagram"></i></a></li>
		<li><a href="mailto:musicskills.altervista.org@gmail.com">Mail me</a></li>
		<li id="stato"><a href="<?php echo $_SERVER['PHP_SELF']; ?>">Login</a></li>
	</ul> 
	</div>
	<div class="container">
	<?php 
	if (isset($_POST['login'])) {
		if (isset($_POST['username'])) $username = mysqli_real_escape_string($mysqli_connection, $_POST['username']);
  		if (isset($_POST['password'])) $password = mysqli_real_escape_string($mysqli_connection, $_POST['password']);
		if (empty($_POST['username']) || empty($_POST['password'])) echo "<p>Dati mancanti</p>";
		else {		
			$utenti = "SELECT * FROM $tab_utenti WHERE username = '$username' or email = '$username';";
			if (!$risq = mysqli_query($mysqli_connection, $utenti)) {
				echo "<p>Problemi con la query</p>";
				exit();
			}
			$arr = mysqli_fetch_array($risq);
			if ($arr) {
				if (password_verify($password, $arr['password'])) {
					if ($arr['status'] == "sospeso") echo "<p>Questo utente &egrave; stato sospeso!</p>";
					elseif ($arr['status'] == "cancellato") echo "<p>Questo utente &egrave; stato eliminato!</p>";
					else {
						$_SESSION['id_user'] = $arr['id']; 
						$_SESSION['usernome'] = $arr['username'];
						$_SESSION['accesso_permesso'] = $arr['status'];
						$_SESSION['accesso_admin'] = $arr['admin'];
						header('Location: '.$_SESSION['current_page'].'');
						exit();
					}
				}
				else echo "<p>La password inserita non &egrave; corretta!</p>";
			}
			else echo "<p>L'username o la mail inseriti non sono corretti!</p>";
		}
	}
	?> 
	<div class="main">
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" autocomplete="off">
		<h2>Login</h2>
		<div class="input-div username">
			<div class="icone"><i class="fas fa-user"></i></div>
			<div>	
				<input type="text" name="username" value="<?php echo $username; ?>" placeholder=" " autofocus>			
				<h5>Username/email</h5>
			</div>
		</div>
		<div class="input-div password">
			<div class="icone"><i class="fas fa-lock"></i></div>
			<div>
				<input type="password" name="password" value="<?php echo $password; ?>" placeholder=" ">
				<h5>Password</h5>
			</div>
		</div>
		<input class="login" type="submit" name="login" value="LOGIN">
		<p>Non hai un account?</p>
	</form>  
		<a class="registrati" href="registrati.php">REGISTRATI</a>
		<p>Hai dimenticato la password?</p>	
		<a class="recupero_password" href="recupero_password.php">RECUPERA LA PASSWORD</a>		
	</div>
	</div>
</body>
</html>