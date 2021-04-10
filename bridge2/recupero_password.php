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
$username = "";
$password = "";

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
	if (isset($_POST['recupero_password'])) {
		if (isset($_POST['email'])) $email = mysqli_real_escape_string($mysqli_connection, $_POST['email']);
		$sql_email = "SELECT * FROM $tab_utenti WHERE email = '$email';";
		$risq_email = mysqli_query($mysqli_connection, $sql_email);
		$row_email = mysqli_fetch_array($risq_email);

		if (empty($_POST['email'])) echo "<p>Dati mancanti!</p>";
		elseif ($email != $row_email['email']) echo "<p>L'email inserita inserita non &egrave; registrata!";
		else {
			function randomPassword() {
	   			$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
	    		$pass = array();
	    		$alphaLength = strlen($alphabet) - 1;
	    		for ($i = 0; $i < 8; $i++) {
	       			$n = rand(0, $alphaLength);
	        		$pass[] = $alphabet[$n];
	    		}
	    		return implode($pass);
	    	}
	    	$password = randomPassword();

	    	$to = "$email";
			$subject = 'Recupero password';
			$message = "Questa è la tua nuova password: ".$password.". Nonostante sia criptata, sei invitato a cambiarla il prima possibile.";
			$headers = "From: " . 'Web Master' . " <" .  'musicskills.altervista.org@gmail.com' . ">\r\n";
			$headers .= "Reply-To: " . 'musicskills.altervista.org@gmail.com' . "\r\n";
			$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

	    	if (mail($to, $subject, $message, $headers)) {
	    		echo "<p>Messaggio inviato con successo a " .$to. "</p>";
	    		$hashed_password = password_hash($password, PASSWORD_DEFAULT);
	    		$utenti = mysqli_query($mysqli_connection, "UPDATE $tab_utenti SET password = '$hashed_password' WHERE id = '{$row_email['id']}';");
	    	}
			else echo "<p>Errore. Non &egrave; possibile inviare l'email all'indirizzo inserito</p>";
		}
	}
	?>
	<div class="main">
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" autocomplete="off">
		<h2>Recupero password</h2>
		<div class="input-div email">
			<div class="icone"><i class="far fa-envelope"></i></div>
			<div>
				<input type="email" name="email" value="<?php echo $email; ?>" placeholder=" " autofocus>
				<h5>Email</h5>
			</div>
		</div>
		<input class="registrati" type="submit" name="recupero_password" value="PROCEDI">
	</form>  
	</div>
	</div>
</body>
</html>