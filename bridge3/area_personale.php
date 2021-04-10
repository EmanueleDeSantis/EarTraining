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
ob_start(); //Per poter riaggiornare correttamente la pagina dopo aver resettato i dati relativi ad una stagione
require_once('../bridge2/connection.php');
session_start(); 

setlocale(LC_TIME, 'Italian');

$_SESSION['current_page'] = $_SERVER['PHP_SELF'];

if (!isset($_SESSION['accesso_permesso'])) {
	header('Location: effettua_login.html');
	exit();
}
elseif ($_SESSION['accesso_permesso'] == "sospeso") {
	header('Location: utente_sospeso.html');
	exit();
}
elseif ($_SESSION['accesso_permesso'] == "cancellato") {
	header('Location: utente_eliminato.html');
	exit();
}
if ($_SESSION['accesso_admin'] == "1") {
	header('Location: area_personale_admin.php');
	exit();
}

if (isset($_POST['logout'])) {
	unset($_SESSION);
	session_destroy();
	header("location: ../index.php");
	exit();
}

if (!isset($_SESSION['data'])) {
	$_SESSION['data'] = date_create(date('Y-m'));
	$_SESSION['data'] = date_format($_SESSION['data'], 'Y-m');
}

if (isset($_POST['inserisci_data'])) {
	if (isset($_POST['data_inserita'])) {
		$_SESSION['data'] = date_create($_POST['data_inserita']);
		$_SESSION['data'] = date_format($_SESSION['data'], 'Y-m');
	}
}

if (isset($_POST['mese_prima'])) {
	$_SESSION['data'] = date_create(date($_SESSION['data']));
	date_modify($_SESSION['data'], '-1 month');
	$_SESSION['data'] = date_format($_SESSION['data'], 'Y-m');
}

if (isset($_POST['mese_dopo'])) {
	$_SESSION['data'] = date_create($_SESSION['data']);
	date_modify($_SESSION['data'], '+1 month');
	$_SESSION['data'] = date_format($_SESSION['data'], 'Y-m');
}

?>

<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head><title>Bridge</title>
<meta name="viewport" content="viewport">
<link rel="shortcut icon" type="image/svg" href="../MySite/icon.svg">
<link rel="stylesheet" type="text/css" href="area_personale.css">
<link rel="stylesheet" type="text/css" href="https://use.fontawesome.com/releases/v5.13.0/css/all.css"> 
</head>
<body>
	<div class="topnav">
	<ul class="menu">
		<li class="icons" id="stato"><a href="../index.php">&#x1f3e1;</a></li>
		<li><a href="../MySite/aboutme.html">About Me</a></li>
		<li class="icons"><a href="#"><i class="fab fa-facebook-square"></i></a></li>
		<li class="icons"><a href="#"><i class="fab fa-instagram"></i></a></li>
		<li><a href="mailto:musicskills.altervista.org@gmail.com">Mail me</a></li>
	</ul>
	<form class="logout" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<span title="Area personale" class="nome"><a href="<?php echo $_SERVER['PHP_SELF']; ?>"><?php echo $_SESSION['usernome']; ?></a></span>
		<sub><input title="logout" type="submit" name="logout" class="fas fa-sign-out-alt" value="&#xf2f5;"></sub>
	</form>
	</div>
	<div class="container">
	<?php
	$xmlString = "";
	foreach (file("XML/risultati_allenamento.xml") as $node) $xmlString .= trim($node);
	$doc = new DOMDocument();
	$doc->loadXML($xmlString);

	$XML_lista_utenti = $doc->documentElement;
	$XML_utenti = $XML_lista_utenti->childNodes;

	$xmlString_comp = "";
	foreach (file("XML/risultati_competitiva.xml") as $node) $xmlString_comp .= trim($node);
	$doc_comp = new DOMDocument();
	$doc_comp->loadXML($xmlString_comp);

	$XML_lista_utenti_comp = $doc_comp->documentElement;
	$XML_utenti_comp = $XML_lista_utenti_comp->childNodes;
	?>
	<div class="risultati">
		<div class="rcont-dx">
		<h1>Risultati allenamento</h1>
		<div class="div-ris personali">
			<div class="div-thead">
				<div class="div-tr">
					<div class="div-th" style="color: #ff00ff; border-right: 0.1rem groove #1E90FF;">Livello</div>
					<div class="div-th" style="color: blue; border-right: 0.1rem groove #1E90FF;">Successi</div>
					<div class="div-th" style="color: red; border-right: 0.1rem groove #1E90FF;">Tentativi</div>
					<div class="div-th" style="color: green;">%Successi</div>
				</div>
			</div>
			<div class="div-tbody">
			<?php
			$XML_tentativi_tot = 0;
			for ($i = 0; $i < $XML_utenti->length; $i++) {
				$XML_utente = $XML_utenti->item($i);
				$XML_utente_id = $XML_utente->getAttribute('id');
				if ($XML_utente_id == $_SESSION['id_user']) {
					$XML_stagioni = $XML_utente->childNodes;
					for ($j = 0; $j < $XML_stagioni->length; $j++) {
						$XML_stagione = $XML_stagioni->item($j);
						$XML_stagione_mese = $XML_stagione->getAttribute('mese');
						if ($XML_stagione_mese == $_SESSION['data']) {
							$XML_livelli = $XML_stagione->getElementsByTagName('livello');
							for ($k = 0; $k < $XML_livelli->length; $k++) {
								$XML_livello = $XML_livelli->item($k);
								$XML_livello_id = $XML_livello->getAttribute('id');
								$XML_successi = $XML_livello->firstChild;
								$XML_successi_value = $XML_successi->textContent;
								$XML_tentativi = $XML_livello->lastChild;
								$XML_tentativi_value = $XML_tentativi->textContent;
								$XML_perc = ($XML_successi_value / $XML_tentativi_value) * 100;
								$XML_tentativi_tot += $XML_tentativi_value;
								echo "<div class=\"div-tr\"><div class=\"div-td\">".$XML_livello_id."</div><div class=\"div-td\">".$XML_successi_value."</div><div class=\"div-td\">".$XML_tentativi_value."</div><div class=\"div-td\">".number_format($XML_perc,2,",",".")."%</div></div>";
							}
						}
					}
				}
			}
			?>
			</div>
		</div>
		</div>
		<div class="rcont-sx">
		<h1>Statistiche note</h1>
		<div class="div-ris personali">
			<div class="div-thead">
				<div class="div-tr">
					<div class="div-th" style="color: #ff00ff; border-right: 0.1rem groove #1E90FF;">Nota</div>
					<div class="div-th" style="color: blue; border-right: 0.1rem groove #1E90FF;">Successi</div>
					<div class="div-th" style="color: red; border-right: 0.1rem groove #1E90FF;">Tentativi</div>
					<div class="div-th" style="color: green;">%Successi</div>
				</div>
			</div>
			<div class="div-tbody">
			<?php
			$XML_note_proposte = 0;
			for ($i = 0; $i < $XML_utenti->length; $i++) {//va eseguito il controllo sull'utente essendoci la possibilità che questo non sia stato ancora creato
				$XML_utente = $XML_utenti->item($i);
				$XML_utente_id = $XML_utente->getAttribute('id');
				if ($XML_utente_id == $_SESSION['id_user']) {
					$XML_stagioni = $XML_utente->childNodes;
					for ($j = 0; $j < $XML_stagioni->length; $j++) {
						$XML_stagione = $XML_stagioni->item($j);
						$XML_stagione_mese = $XML_stagione->getAttribute('mese');
						if ($XML_stagione_mese == $_SESSION['data']) {
							$XML_note = $XML_stagione->getElementsByTagName('nota');
							for ($k = 0; $k < $XML_note->length; $k++) {
								$XML_nota = $XML_note->item($k);
								$XML_nota_nome = $XML_nota->getAttribute('nome');
								$XML_successi = $XML_nota->firstChild;
								$XML_successi_value = $XML_successi->textContent;
								$XML_tentativi = $XML_nota->lastChild;
								$XML_tentativi_value = $XML_tentativi->textContent;
								$XML_perc = ($XML_successi_value / $XML_tentativi_value) * 100;
								$XML_note_proposte += $XML_tentativi_value;
								echo "<div class=\"div-tr\"><div class=\"div-td\">".$XML_nota_nome."</div><div class=\"div-td\">".$XML_successi_value."</div><div class=\"div-td\">".$XML_tentativi_value."</div><div class=\"div-td\">".number_format($XML_perc,2,",",".")."%</div></div>";
							}
						}
					}
				}
			}
			?>
			</div>
		</div>
		</div>
	<h2>Tentativi totali nel mese selezionato: <?php echo $XML_tentativi_tot; ?></h2>
	<h2>Note totali proposte nel mese selezionato: <?php echo $XML_note_proposte; ?></h2>
	</div>
	<div class="ris-form">
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			<input type="submit" name="mese_prima" class="fas fa-caret-left" value="&#xf0d9;">
		</form>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			<input type="month" name="data_inserita" value="<?php echo $_SESSION['data']; ?>">
			<input type="submit" name="inserisci_data" value="Inserisci">
		</form>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			<input type="submit" name="mese_dopo" class="fas fa-caret-right" value="&#xf0da;">
		</form>
	</div>
	<?php
	$alert = "";
	$conf_pass = 'none';
	$mod_pass = 'block';
	$res_dati = 'block';
	$del_acc = 'block';
	$mod_user = 'block';
	$mod_email = 'block';
	$mod_sec_email = 'block';
	$mod_addr = 'block';
	$mod_cap = 'block';
	$mod_bday = 'block';
	$password = '';
	$password_attuale = '';
	if (!isset($_SESSION['case'])) $_SESSION['case'] = 0;
	if (!isset($_SESSION['content'])) $_SESSION['content'] = "";

	$sql = "SELECT * FROM $tab_utenti WHERE id = '{$_SESSION['id_user']}';";
	$risq = mysqli_query($mysqli_connection, $sql);
	$row = mysqli_fetch_array($risq);

	if (isset($_POST['modifica_email'])) {
		if (isset($_POST['email'])) $email = mysqli_real_escape_string($mysqli_connection, $_POST['email']);
		$sql_email = "SELECT * FROM $tab_utenti WHERE email = '$email';";
		$risq_email = mysqli_query($mysqli_connection, $sql_email);
		if (mysqli_num_rows($risq_email) > 0) $alert = "<p>L'email inserita &egrave; gi&agrave; stata scelta!</p>";
		elseif (strlen($email) < 8) $alert = "<p>L'email deve contenere almeno 8 caratteri!</p>";
		elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $alert = "<p>L'email inserita non &egrave; valida!</p>";
		else {
			$_SESSION['case'] = 1;
			$_SESSION['content'] = $email;
			$alert = "<p>Sei sicuro di voler modificare la tua email? Per confermare inserisci la tua password e premi &quot;Conferma&quot;.</p>";
			$conf_pass = 'block';
			$mod_email = 'none';	
		}
	}
	if (isset($_POST['modifica_secondary_email'])) {
		if (isset($_POST['secondary_email'])) $secondary_email = mysqli_real_escape_string($mysqli_connection, $_POST['secondary_email']);
		if ($secondary_email == $row['email']) $alert = "<p>L'email secondaria non pu&ograve essere uguale a quella principale!";
		elseif (strlen($secondary_email) < 8) $alert = "<p>L'email deve contenere almeno 8 caratteri!</p>";
		elseif (!filter_var($secondary_email, FILTER_VALIDATE_EMAIL)) $alert = "<p>L'email inserita non &egrave; valida!</p>";
		else {
			$_SESSION['case'] = 2;
			$_SESSION['content'] = $secondary_email;
			$alert = "<p>Sei sicuro di voler modificare la tua email? Per confermare inserisci la tua password e premi &quot;Conferma&quot;.</p>";
			$conf_pass = 'block';
			$mod_sec_email = 'none';	
		}
	}
	if (isset($_POST['modifica_username'])) {
		if (isset($_POST['username'])) $username = mysqli_real_escape_string($mysqli_connection, $_POST['username']);
		$sql_username = "SELECT * FROM $tab_utenti WHERE username = '$username';";
		$risq_username = mysqli_query($mysqli_connection, $sql_username);
		if (mysqli_num_rows($risq_username) > 0) $alert = "<p>L'username inserito &egrave; gi&agrave; stato scelto!</p>";
		elseif (strlen($username) < 4) $alert = "<p>L'username deve contenere almeno 4 caratteri!</p>";
		elseif (strlen($username) > 9) $alert = "<p>L'username pu&ograve; contenere al massimo 9 caratteri!</p>";
		else {
			$_SESSION['case'] = 3;
			$_SESSION['content'] = $username;
			$alert = "<p>Sei sicuro di voler modificare il tuo username? Per confermare inserisci la tua password e premi &quot;Conferma&quot;.</p>";
			$conf_pass = 'block';
			$mod_user = 'none';
		}
	}
	if (isset($_POST['modifica_password'])) {
		if (isset($_POST['password'])) $password = mysqli_real_escape_string($mysqli_connection, $_POST['password']);
		if (strlen($password) < 4) $alert = "<p>La password deve contenere almeno 4 caratteri!</p>";
		else {
			$_SESSION['case'] = 4;
			$_SESSION['content'] = $password;
			$alert = "<p>Sei sicuro di voler modificare la tua password? Per confermare inserisci la tua password e premi &quot;Conferma&quot;.</p>";
			$conf_pass = 'block';
			$mod_pass = 'none';
		}
	}
	if (isset($_POST['modifica_address'])) {
		if (isset($_POST['address'])) $address = $_POST['address'];
		if (empty($address)) $alert = "<p>L'indirizzo non pu&ograve; vuoto!</p>";
		else {
			$_SESSION['case'] = 5;
			$_SESSION['content'] = $address;
			$alert = "<p>Sei sicuro di voler modificare il tuo indirizzo? Per confermare inserisci la tua password e premi &quot;Conferma&quot;.</p>";
			$conf_pass = 'block';
			$mod_addr = 'none';
		}
	}
	if (isset($_POST['modifica_cap'])) {
		if (isset($_POST['cap'])) $cap = $_POST['cap'];
		if (strlen($cap) != 5) $alert = "<p>Il cap inserito non &egrave; valido!</p>";
		else {
			$_SESSION['case'] = 6;
			$_SESSION['content'] = $cap;
			$alert = "<p>Sei sicuro di voler modificare il tuo indirizzo? Per confermare inserisci la tua password e premi &quot;Conferma&quot;.</p>";
			$conf_pass = 'block';
			$mod_cap = 'none';
		}
	}
	if (isset($_POST['modifica_birthday'])) {
		if (isset($_POST['birthday'])) $birthday = $_POST['birthday'];
		if (!DateTime::createFromFormat("Y-m-d", $birthday)) $alert =  "<p>La data inserita non &egrave; valida!</p>";
		else {
			$_SESSION['case'] = 7;
			$_SESSION['content'] = $birthday;
			$alert = "<p>Sei sicuro di voler modificare il tuo indirizzo? Per confermare inserisci la tua password e premi &quot;Conferma&quot;.</p>";
			$conf_pass = 'block';
			$mod_bday = 'none';
		}
	}
	if (isset($_POST['resetta_dati'])) {
		$_SESSION['case'] = 8;
		$alert = "<p>Sei sicuro di voler resettare i tuoi dati di ".strftime('%B %Y', strtotime($_SESSION['data']))."? Per confermare inserisci la tua password e premi &quot;Conferma&quot;.</p>";
		$conf_pass = 'block';
		$res_dati = 'none';
	}
	if (isset($_POST['elimina_account'])) {
		$_SESSION['case'] = 9;
		$alert = "<p>Sei sicuro di voler eliminare il tuo account? Tutti i tuoi dati verranno eliminati. Per confermare inserisci la tua password e premi &quot;Conferma&quot;.</p>";
		$conf_pass = 'block';
		$del_acc = 'none';
	}
	if (isset($_POST['conferma_password'])) {
		$conf_pass = 'block';
		switch ($_SESSION['case']) {
			case '1':
				$mod_email = 'none';
				if (isset($_POST['conf_password'])) $password_attuale = mysqli_real_escape_string($mysqli_connection, $_POST['conf_password']);
				if (!password_verify($password_attuale, $row['password'])) $alert = "<p>La password inserita non &egrave; la password di questo account!</p>";
				else {
					$utenti = mysqli_query($mysqli_connection, "UPDATE $tab_utenti SET email = '{$_SESSION['content']}' WHERE id = '{$_SESSION['id_user']}';");
					unset($_SESSION['content']);
					header('location: '.$_SERVER['PHP_SELF'].'');
					exit();
				}
				break;
			case '2':
				$mod_sec_email = 'none';
				if (isset($_POST['conf_password'])) $password_attuale = mysqli_real_escape_string($mysqli_connection, $_POST['conf_password']);
				if (!password_verify($password_attuale, $row['password'])) $alert = "<p>La password inserita non &egrave; la password di questo account!</p>";
				else {
					$utenti = mysqli_query($mysqli_connection, "UPDATE $tab_utenti SET secondary_email = '{$_SESSION['content']}' WHERE id = '{$_SESSION['id_user']}';");
					unset($_SESSION['content']);
					header('location: '.$_SERVER['PHP_SELF'].'');
					exit();
				}
				break;
			case '3':
				$mod_user = 'none';
				if (isset($_POST['conf_password'])) $password_attuale = mysqli_real_escape_string($mysqli_connection, $_POST['conf_password']);
				if (!password_verify($password_attuale, $row['password'])) $alert = "<p>La password inserita non &egrave; la password di questo account!</p>";
				else {
					$utenti = mysqli_query($mysqli_connection, "UPDATE $tab_utenti SET username = '{$_SESSION['content']}' WHERE id = '{$_SESSION['id_user']}';");
					for ($i = 0; $i < $XML_utenti->length; $i++) {
						$XML_utente = $XML_utenti->item($i);
						$XML_utente_id = $XML_utente->getAttribute('id');
						if ($XML_utente_id == $_SESSION['id_user']) {
							$XML_utente->removeAttribute('username');
							$XML_utente->setAttribute('username', $_SESSION['content']);
							$doc->formatOutput = true;
							$doc->save('XML/risultati_allenamento.xml');
						}
					}
					for ($i = 0; $i < $XML_utenti_comp->length; $i++) {
						$XML_utente_comp = $XML_utenti_comp->item($i);
						$XML_utente_comp_id = $XML_utente_comp->getAttribute('id');
						if ($XML_utente_comp_id == $_SESSION['id_user']) {
							$XML_utente_comp->removeAttribute('username');
							$XML_utente_comp->setAttribute('username', $_SESSION['content']);
							$doc_comp->formatOutput = true;
							$doc_comp->save('XML/risultati_competitiva.xml');
						}
					}
					$_SESSION['usernome'] = $_SESSION['content'];
					unset($_SESSION['content']);
					header('location: '.$_SERVER['PHP_SELF'].'');
					exit();
				}
				break;
			case '4':
				$mod_pass = 'none';
				if (isset($_POST['conf_password'])) $password_attuale = mysqli_real_escape_string($mysqli_connection, $_POST['conf_password']);
				if (!password_verify($password_attuale, $row['password'])) $alert = "<p>La password inserita non &egrave; la password di questo account!</p>";
				else {
					$hashed_password = password_hash($_SESSION['content'], PASSWORD_DEFAULT);
					$utenti = mysqli_query($mysqli_connection, "UPDATE $tab_utenti SET password = '$hashed_password' WHERE id = '{$_SESSION['id_user']}';");
					$alert = "<p style='color: blue;'>Password cambiata con successo!";
					$mod_pass = 'block';
					$conf_pass = 'none';
					unset($_SESSION['content']);
					$password_attuale = '';
				}
				break;
			case '5':
				$mod_addr = 'none';
				if (isset($_POST['conf_password'])) $password_attuale = mysqli_real_escape_string($mysqli_connection, $_POST['conf_password']);
				if (!password_verify($password_attuale, $row['password'])) $alert = "<p>La password inserita non &egrave; la password di questo account!</p>";
				else {
					$utenti = mysqli_query($mysqli_connection, "UPDATE $tab_utenti SET address = '{$_SESSION['content']}' WHERE id = '{$_SESSION['id_user']}';");
					unset($_SESSION['content']);
					header('location: '.$_SERVER['PHP_SELF'].'');
					exit();
				}
				break;
			case '6':
				$mod_cap = 'none';
				if (isset($_POST['conf_password'])) $password_attuale = mysqli_real_escape_string($mysqli_connection, $_POST['conf_password']);
				if (!password_verify($password_attuale, $row['password'])) $alert = "<p>La password inserita non &egrave; la password di questo account!</p>";
				else {
					$utenti = mysqli_query($mysqli_connection, "UPDATE $tab_utenti SET cap = '{$_SESSION['content']}' WHERE id = '{$_SESSION['id_user']}';");
					unset($_SESSION['content']);
					header('location: '.$_SERVER['PHP_SELF'].'');
					exit();
				}
				break;
			case '7':
				$mod_bday = 'none';
				if (isset($_POST['conf_password'])) $password_attuale = mysqli_real_escape_string($mysqli_connection, $_POST['conf_password']);
				if (!password_verify($password_attuale, $row['password'])) $alert = "<p>La password inserita non &egrave; la password di questo account!</p>";
				else {
					$utenti = mysqli_query($mysqli_connection, "UPDATE $tab_utenti SET birthday = '{$_SESSION['content']}' WHERE id = '{$_SESSION['id_user']}';");
					unset($_SESSION['content']);
					header('location: '.$_SERVER['PHP_SELF'].'');
					exit();
				}
				break;
			case '8':
				$res_dati = 'none';
				if (isset($_POST['conf_password'])) $password_attuale = mysqli_real_escape_string($mysqli_connection, $_POST['conf_password']);
				if (!password_verify($password_attuale, $row['password'])) $alert = "<p>La password inserita non &egrave; la password di questo account!</p>";
				else {
					for ($j = 0; $j < $XML_stagioni->length; $j++) {
						$XML_stagione = $XML_stagioni->item($j);
						$XML_stagione_mese = $XML_stagione->getAttribute('mese');
						if ($XML_stagione_mese == $_SESSION['data']) {
							$XML_stagione->parentNode->removeChild($XML_stagione);
							$doc->formatOutput = true;
							$doc->save('XML/risultati_allenamento.xml');
						}
					}
					unset($_SESSION['content']);
					header('location: '.$_SERVER['PHP_SELF'].'');
					exit();
				}
				break;				
			case '9':
				$del_acc = 'none';
				if (isset($_POST['conf_password'])) $password_attuale = mysqli_real_escape_string($mysqli_connection, $_POST['conf_password']);
				if (!password_verify($password_attuale, $row['password'])) $alert = "<p>La password inserita non &egrave; la password di questo account!</p>";
				else {
					$utenti = mysqli_query($mysqli_connection, "UPDATE $tab_utenti SET status = 'cancellato' WHERE id = '{$_SESSION['id_user']}';");
					for ($i = 0; $i < $XML_utenti->length; $i++) {
						$XML_utente = $XML_utenti->item($i);
						$XML_utente_id = $XML_utente->getAttribute('id');
						if ($XML_utente_id == $_SESSION['id_user']) {
							$XML_utente->parentNode->removeChild($XML_utente);
							$doc->formatOutput = true;
							$doc->save('XML/risultati_allenamento.xml');
						}
					}
					for ($i = 0; $i < $XML_utenti_comp->length; $i++) {
						$XML_utente_comp = $XML_utenti_comp->item($i);
						$XML_utente_comp_id = $XML_utente_comp->getAttribute('id');
						if ($XML_utente_comp_id == $_SESSION['id_user']) {
							$XML_utente_comp->parentNode->removeChild($XML_utente_comp);
							$doc_comp->formatOutput = true;
							$doc_comp->save('XML/risultati_competitiva.xml');
						}
					}
					unset($_SESSION);
					session_destroy();
					header("location: ../index.php");
					exit();
				}
				break;			
			default:
				break;
		}
	}
	?>
	<div class="main">
		<h1>Dati personali</h1>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display: <?php echo $mod_email; ?>" method="post">
			<div><i style="color: blueviolet;" class="far fa-envelope"></i><h3>Modifica la tua <span style="color: blueviolet;">email:</h3><input type="email" name="email" value="<?php echo $row['email']; ?>"><input type="submit" name="modifica_email" value="Modifica"></div>
		</form>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display: <?php echo $mod_sec_email; ?>" method="post">
			<div><i style="color: blueviolet;" class="far fa-envelope"></i><h3>Modifica la tua <span style="color: blueviolet;">email secondaria:</h3><input type="email" name="secondary_email" value="<?php echo $row['secondary_email']; ?>"><input type="submit" name="modifica_secondary_email" value="Modifica"></div>
		</form>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display: <?php echo $mod_user; ?>" method="post">
			<div><i style="color: green;" class="fas fa-user"></i><h3>Modifica il tuo <span style="color: green;">username:</span></h3><input type="text" name="username" value="<?php echo $row['username']; ?>"><input type="submit" name="modifica_username" value="Modifica"></div>
		</form>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display: <?php echo $mod_pass; ?>" method="post">
			<div><i style="color: blue;" class="fas fa-lock"></i><h3>Modifica la tua <span style="color: blue;">password:</span></h3><input type="password" name="password" placeholder="Password" value="<?php echo $password; ?>"><input type="submit" name="modifica_password" value="Modifica"></div>
		</form>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display: <?php echo $mod_addr; ?>" method="post">
			<div><i style="color: deeppink;" class="fas fa-map-marked-alt"></i><h3>Modifica il tuo <span style="color: deeppink;">indirizzo:</h3><input type="text" name="address" value="<?php echo $row['address']; ?>"><input type="submit" name="modifica_address" value="Modifica"></div>
		</form>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display: <?php echo $mod_cap; ?>" method="post">
			<div><i style="color: brown;" class="fas fa-map-marker-alt"></i><h3>Modifica il tuo <span style="color: brown;">codice postale:</h3><input type="number" name="cap" value="<?php echo $row['cap']; ?>"><input type="submit" name="modifica_cap" value="Modifica"></div>
		</form>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display: <?php echo $mod_bday; ?>" method="post">
			<div><i style="color: darkorange;" class="fas fa-birthday-cake"></i><h3>Modifica la tua <span style="color: darkorange;">data di nascita:</h3><input type="date" name="birthday" value="<?php echo $row['birthday']; ?>"><input type="submit" name="modifica_birthday" value="Modifica"></div>
		</form>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display: <?php echo $res_dati; ?>" method="post">
			<div><i style="color: red;" class="fas fa-trash"></i><h3 style="color: red;">Resetta i tuoi risultati:</h3><input type="submit" name="resetta_dati" value="Resetta"></div>
		</form>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display: <?php echo $del_acc; ?>" method="post">
			<div><i style="color: red;" class="fas fa-user-times"></i><h3 style="color: red;">Elimina il tuo account:</h3><input type="submit" name="elimina_account" value="Elimina"></div>
		</form>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display: <?php echo $conf_pass; ?>" method="post">
			<div><i style="color: red;" class="fas fa-lock"></i><h3>Inserisci la tua <span style="color: red;">password attuale:</span></h3><input type="password" name="conf_password" placeholder="Password" value="<?php echo $password_attuale; ?>"><input type="submit" name="conferma_password" value="Conferma"></div>
		</form>
		<?php echo $alert; ?>
	</div>
	</div>
</body>
</html>