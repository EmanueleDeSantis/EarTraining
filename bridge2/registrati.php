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

$email = "";
$secondary_email = "";
$username = "";
$password = "";
$address = "";
$cap = "";
$birthday = "";

?>

<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head><title>Bridge</title>
<meta name="viewport" content="viewport">
<link rel="shortcut icon" type="image/svg" href="../MySite/icon.svg">
<link rel="stylesheet" type="text/css" href="registrati.css">
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
		<li id="stato"><a href="login.php">Login</a></li>
	</ul> 
	</div>
	<div class="container">
	<?php  
	if (isset($_POST['registrati'])) {
		if (isset($_POST['email'])) $email = mysqli_real_escape_string($mysqli_connection, $_POST['email']);
		if (isset($_POST['secondary_email'])) $secondary_email = mysqli_real_escape_string($mysqli_connection, $_POST['secondary_email']);
		if (isset($_POST['username'])) $username = mysqli_real_escape_string($mysqli_connection, $_POST['username']); 
  		if (isset($_POST['password'])) $password = mysqli_real_escape_string($mysqli_connection, $_POST['password']);
  		if (isset($_POST['address'])) $address = $_POST['address'];
  		if (isset($_POST['cap'])) $cap = $_POST['cap'];
  		if (isset($_POST['birthday'])) $birthday = $_POST['birthday'];

  		$hashed_password = password_hash($password, PASSWORD_DEFAULT);

		$sql_email = "SELECT * FROM $tab_utenti WHERE email = '$email';";
		$risq_email = mysqli_query($mysqli_connection, $sql_email);
		$sql_username = "SELECT * FROM $tab_utenti WHERE username = '$username';";
		$risq_username = mysqli_query($mysqli_connection, $sql_username);

		if (empty($_POST['email']) || empty($_POST['secondary_email']) || empty($_POST['username']) || empty($_POST['password']) || empty($_POST['address']) || empty($_POST['cap']) || empty($_POST['birthday'])) echo "<p>Dati mancanti!</p>";
		elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) echo "<p>L'email inserita non &egrave; valida!";
		elseif (!filter_var($secondary_email, FILTER_VALIDATE_EMAIL)) echo "<p>L'email inserita non &egrave; valida!";
		elseif ($secondary_email == $email) echo "<p>L'email secondaria non pu&ograve essere uguale a quella principale!";
		elseif (mysqli_num_rows($risq_email) > 0) echo "<p>L'email inserita &egrave; gi&agrave; stata scelta!</p>";
		elseif (strlen($email) < 8) echo "<p>L'email deve contenere almeno 8 caratteri!</p>";
		elseif (strlen($username) < 4) echo "<p>L'username deve contenere almeno 4 caratteri!</p>";
		elseif (strlen($username) > 9) echo "<p>L'username pu&ograve; contenere al massimo 9 caratteri!</p>";
		elseif (mysqli_num_rows($risq_username) > 0) echo "<p>Questo username &egrave; gi&agrave; stato scelto!</p>";
		elseif (strlen($password) < 4) echo "<p>La password deve contenere almeno 4 caratteri!</p>";
		elseif (strlen($cap) != 5) echo "<p>L'indirizzo postale inserito non &egrave; valido!</p>";
		elseif (!DateTime::createFromFormat("Y-m-d", $birthday)) echo "<p>La data inserita non &egrave; valida!</p>";
		else {		
			$utenti = mysqli_query($mysqli_connection, "INSERT INTO $tab_utenti(email, secondary_email, username, password, address, cap, birthday) VALUES('".$email."', '".$secondary_email."', '".$username."', '".$hashed_password."', '".$address."', '".$cap."', '".$birthday."');");
			$sql_id = "SELECT * FROM $tab_utenti WHERE username = '$username';";
			$risq_id = mysqli_query($mysqli_connection, $sql_id);
			$row_id = mysqli_fetch_array($risq_id);
			$_SESSION['id_user'] = $row_id['id'];
			$_SESSION['usernome'] = $username;
			$_SESSION['accesso_permesso'] = $row_id['status'];
			$_SESSION['accesso_admin'] = $row_id['admin'];
			header('Location: '.$_SESSION['current_page'].'');
			exit();
		}
	}
	?>
	<div class="main">
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" autocomplete="off">
		<h2>Login</h2>
		<div class="input-div email">
			<div class="icone"><i class="far fa-envelope" style="font-weight: 600;"></i></div>
			<div>
				<input type="email" name="email" value="<?php echo $email; ?>" placeholder=" " autofocus>
				<h5>Email</h5>
			</div>
		</div>
		<div class="input-div secondary_email">
			<div class="icone"><i class="far fa-envelope" style="font-weight: 600;"></i></div>
			<div>
				<input type="email" name="secondary_email" value="<?php echo $secondary_email; ?>" placeholder=" ">
				<h5>Email secondaria</h5>
			</div>
		</div>
		<div class="input-div username">
			<div class="icone"><i class="fas fa-user"></i></div>
			<div>	
				<input type="text" name="username" value="<?php echo $username; ?>" placeholder=" ">			
				<h5>Username</h5>
			</div>
		</div>
		<div class="input-div password">
			<div class="icone"><i class="fas fa-lock"></i></div>
			<div>
				<input type="password" name="password" value="<?php echo $password; ?>" placeholder=" ">
				<h5>Password</h5>
			</div>
		</div>
		<div class="input-div address">
			<div class="icone"><i class="fas fa-map-marked-alt"></i></div>
			<div>
				<input type="text" name="address" value="<?php echo $address; ?>" placeholder=" ">
				<h5>Indirizzo + n&deg; civico</h5>
			</div>
		</div>
		<div class="input-div cap">
			<div class="icone"><i class="fas fa-map-marker-alt"></i></div>
			<div>
				<input type="number" name="cap" value="<?php echo $cap; ?>" min="0" placeholder=" ">
				<h5>Codice postale</h5>
			</div>
		</div>
		<div class="input-div birthday">
			<div class="icone"><i class="fas fa-birthday-cake"></i></div>
			<div>
				<input type="date" name="birthday" value="<?php echo $birthday; ?>" min="0" placeholder=" ">
				<h5>Data di nascita</h5>
			</div>
		</div>
		<input class="registrati" type="submit" name="registrati" value="REGISTRATI">
	</form>  
	</div>
	</div>
</body>
</html>