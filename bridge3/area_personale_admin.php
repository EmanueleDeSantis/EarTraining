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

if ($_SESSION['accesso_admin'] != "1") {
	header('Location: accesso_vietato.html');
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

$display_table = 'none';
$display_table_lista_utenti = 'none';
$display_data_glob = 'none';

if(!isset($_SESSION['data_glob'])) {
	$_SESSION['data_glob'] = date_create(date('Y-m'));
	$_SESSION['data_glob'] = date_format($_SESSION['data_glob'], 'Y-m');
}

if (isset($_POST['inserisci_data_glob'])) {
	$display_table = 'table';
	$display_data_glob = 'inline';
	if (isset($_POST['data_inserita_glob'])) {
		$_SESSION['data_glob'] = date_create($_POST['data_inserita_glob']);
		$_SESSION['data_glob'] = date_format($_SESSION['data_glob'], 'Y-m');
	}
}

if (isset($_POST['mese_prima_glob'])) {
	$display_table = 'table';
	$display_data_glob = 'inline';
	$_SESSION['data_glob'] = date_create(date($_SESSION['data_glob']));
	date_modify($_SESSION['data_glob'], '-1 month');
	$_SESSION['data_glob'] = date_format($_SESSION['data_glob'], 'Y-m');
}

if (isset($_POST['mese_dopo_glob'])) {
	$display_table = 'table';
	$display_data_glob = 'inline';
	$_SESSION['data_glob'] = date_create($_SESSION['data_glob']);
	date_modify($_SESSION['data_glob'], '+1 month');
	$_SESSION['data_glob'] = date_format($_SESSION['data_glob'], 'Y-m');
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
	<div class="ris-form pers">
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
	$ris_alert = "";
	if (!isset($_SESSION['show'])) $_SESSION['show'] = 0;

	if (isset($_POST['username_scelto'])) {
		$query_esiste_utente = "SELECT * FROM $tab_utenti WHERE username = '{$_POST['username_scelto']}';";
		$result_esiste_utente = mysqli_query($mysqli_connection, $query_esiste_utente);
		$fetch_esiste_utente = mysqli_fetch_array($result_esiste_utente);
		if (empty($fetch_esiste_utente)) $ris_alert = "<p style=\"color: red;\">L'utente cercato non esiste!</p>";
		else {
			$_SESSION['show'] = 1;
			$_SESSION['user_scelto'] = $_POST['username_scelto'];
			$display_table = 'table';
			$display_data_glob = 'inline';
		}
	}

	if (isset($_POST['livello_scelto'])) {
		$_SESSION['show'] = 2;
		$_SESSION['liv_scelto'] = $_POST['livello_scelto'];
		$display_table = 'table';
		$display_data_glob = 'inline';
	}

	if (isset($_POST['classifica'])) {
		$_SESSION['show'] = 3;
		$display_table = 'table';
		$display_data_glob = 'inline';
	}

	if (isset($_POST['lista_utenti'])) {
		$_SESSION['show'] = 4;
		$display_table_lista_utenti = 'table';
	}

	function XML_username_scelto() {
		global $XML_lista_utenti_comp;
		global $XML_utenti_comp;
		for ($i = 0; $i < $XML_utenti_comp->length; $i++) {
			$XML_utente = $XML_utenti_comp->item($i);
			$XML_utente_username = $XML_utente->getAttribute('username');
			if ($XML_utente_username == $_SESSION['user_scelto']) {
				$XML_stagioni = $XML_utente->childNodes;
				for ($j = 0; $j < $XML_stagioni->length; $j++) {
					$XML_stagione = $XML_stagioni->item($j);
					$XML_stagione_mese = $XML_stagione->getAttribute('mese');
					if ($XML_stagione_mese == $_SESSION['data_glob']) {
						$XML_livelli = $XML_stagione->childNodes;
						for ($k = 0; $k < $XML_livelli->length; $k++) {
							$XML_livello = $XML_livelli->item($k);
							$XML_livello_id = $XML_livello->getAttribute('id');
							$XML_successi = $XML_livello->firstChild;
							$XML_successi_value = $XML_successi->textContent;
							$XML_tentativi = $XML_livello->lastChild;
							$XML_tentativi_value = $XML_tentativi->textContent;
							$XML_perc = ($XML_successi_value / $XML_tentativi_value) * 100;
							echo "<div class=\"div-tr\"><div class=\"div-td\">".$XML_utente_username."</div><div class=\"div-td\">".$XML_livello_id."</div><div class=\"div-td\">".$XML_successi_value."</div><div class=\"div-td\">".$XML_tentativi_value."</div><div class=\"div-td\">".number_format($XML_perc,2,",",".")."%</div></div>";
						}
					}
				}
			}
		}
	}

	function XML_livello_scelto() {
		global $XML_lista_utenti_comp;
		global $XML_utenti_comp;
		$array_classifica = array();
		$array_of_array_classifica = array();
		for ($i = 0; $i < $XML_utenti_comp->length; $i++) {
			$XML_utente = $XML_utenti_comp->item($i);
			$XML_utente_username = $XML_utente->getAttribute('username');
			$XML_stagioni = $XML_utente->childNodes;
			for ($j = 0; $j < $XML_stagioni->length; $j++) {
				$XML_stagione = $XML_stagioni->item($j);
				$XML_stagione_mese = $XML_stagione->getAttribute('mese');
				if ($XML_stagione_mese == $_SESSION['data_glob']) {
					$XML_livelli = $XML_stagione->getElementsByTagName('livello');
					for ($k = 0; $k < $XML_livelli->length; $k++) {
						$XML_livello = $XML_livelli->item($k);
						$XML_livello_id = $XML_livello->getAttribute('id');
						if ($XML_livello_id == $_SESSION['liv_scelto']) {
							$XML_successi = $XML_livello->firstChild;
							$XML_successi_value = $XML_successi->textContent;
							$XML_tentativi = $XML_livello->lastChild;
							$XML_tentativi_value = $XML_tentativi->textContent;
							$XML_perc = ($XML_successi_value / $XML_tentativi_value) * 100;
							$array_classifica = array('user' => $XML_utente_username, 'liv' => $XML_livello_id, 'succ' => $XML_successi_value, 'tent' => $XML_tentativi_value, 'perc' => number_format($XML_perc,2,",","."));
							$array_of_array_classifica[] = $array_classifica;
						}
						$perc = array_column($array_of_array_classifica, 'perc');
						$succ = array_column($array_of_array_classifica, 'succ');
						array_multisort($perc, SORT_DESC, SORT_NUMERIC, $succ, SORT_DESC, SORT_NUMERIC, $array_of_array_classifica);
					}
				}
			}
		}
		foreach ($array_of_array_classifica as $key => $array) {
			if ($key <= 2) $color = 'darkgoldenrod';
			else $color = 'black';
			echo "<div class=\"div-tr\"><div class=\"div-td\" style=\"color: $color;\">".$array['user']."</div><div class=\"div-td\">".$array['liv']."</div><div class=\"div-td\">".$array['succ']."</div><div class=\"div-td\">".$array['tent']."</div><div class=\"div-td\">".$array['perc']."%</div></div>";
		}
	}

	function XML_classifica() {
		global $XML_lista_utenti_comp;
		global $XML_utenti_comp;
		$array_classifica = array();
		$array_of_array_classifica = array();
		for ($i = 0; $i < $XML_utenti_comp->length; $i++) {
			$XML_utente = $XML_utenti_comp->item($i);
			$XML_utente_username = $XML_utente->getAttribute('username');
			$XML_stagioni = $XML_utente->childNodes;
			for ($j = 0; $j < $XML_stagioni->length; $j++) {
				$XML_stagione = $XML_stagioni->item($j);
				$XML_stagione_mese = $XML_stagione->getAttribute('mese');
				if ($XML_stagione_mese == $_SESSION['data_glob']) {
					$XML_livelli = $XML_stagione->childNodes;
					for ($k = 0; $k < $XML_livelli->length; $k++) {
						$XML_livello = $XML_livelli->item($k);
						$XML_livello_id = $XML_livello->getAttribute('id');
						$XML_successi = $XML_livello->firstChild;
						$XML_successi_value = $XML_successi->textContent;
						$XML_tentativi = $XML_livello->lastChild;
						$XML_tentativi_value = $XML_tentativi->textContent;
						$XML_perc = ($XML_successi_value / $XML_tentativi_value) * 100;
						$array_classifica = array('user' => $XML_utente_username, 'liv' => $XML_livello_id, 'succ' => $XML_successi_value, 'tent' => $XML_tentativi_value, 'perc' => number_format($XML_perc,2,",","."));
						$array_of_array_classifica[] = $array_classifica;
					}
					$perc = array_column($array_of_array_classifica, 'perc');
					$succ = array_column($array_of_array_classifica, 'succ');
					$liv = array_column($array_of_array_classifica, 'liv');
					array_multisort($perc, SORT_DESC, SORT_NUMERIC, $liv, SORT_DESC, SORT_NUMERIC, $succ, SORT_DESC, SORT_NUMERIC, $array_of_array_classifica);
				}
			}
		}
		foreach ($array_of_array_classifica as $key => $array) {
			if ($key <= 2) $color = 'darkgoldenrod';
			else $color = 'black';
			echo "<div class=\"div-tr\"><div class=\"div-td\" style=\"color: $color;\">".$array['user']."</div><div class=\"div-td\">".$array['liv']."</div><div class=\"div-td\">".$array['succ']."</div><div class=\"div-td\">".$array['tent']."</div><div class=\"div-td\">".$array['perc']."%</div></div>";
		}
	}
	?>
	<?php
	$alert = "";
	$conf_pass = 'none';
	$mod_pass = 'block';
	$res_dati = 'block';
	$mod_user = 'block';
	$mod_email = 'block';
	$mod_sec_email = 'block';
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
		if ($secondary_email == $row['email']) $alert = "<p>L'email secondaria non pu&ograve essere uguale a quella principale!</p>";
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
	if (isset($_POST['resetta_dati'])) {
		$_SESSION['case'] = 5;
		$alert = "<p>Sei sicuro di voler resettare i tuoi dati di ".strftime('%B %Y', strtotime($_SESSION['data']))."? Per confermare inserisci la tua password e premi &quot;Conferma&quot;.</p>";
		$conf_pass = 'block';
		$res_dati = 'none';
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
					$alert = "<p style='color: blue;'>Password cambiata con successo!</p>";
					$mod_pass = 'block';
					$conf_pass = 'none';
					unset($_SESSION['content']);
					$password_attuale = '';
				}
				break;
			case '5':
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
			default:
				break;
		}
	}
	?>
	<div class="risultati-globali">
		<h1>Risultati globali</h1>	
		<h2 style="color: red;">Cerca per:</h2>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			<span>Username:&nbsp;</span><input type="text" name="username_scelto">
			<input type="submit" name="quale_utente" value="Cerca">
		</form>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			<span style="color: #ff00ff;">Livello:&nbsp;</span><input type="number" min="1" max="42" name="livello_scelto" value="1">
			<input type="submit" name="quale_livello" value="Cerca">
		</form>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			<span style="color: red;">Classifica completa:&nbsp;</span><input type="submit" name="classifica" value="Mostra">
		</form>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			<span style="color: blue;">Lista utenti:&nbsp;</span><input type="submit" name="lista_utenti" value="Mostra">
		</form>
	</div>
	<?php echo $ris_alert; ?>
	<div class="ris-form glob" style="display: <?php echo $display_data_glob; ?>;">
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			<input type="submit" name="mese_prima_glob" class="fas fa-caret-left" value="&#xf0d9;">
		</form>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			<input type="month" name="data_inserita_glob" value="<?php echo $_SESSION['data_glob']; ?>">
			<input type="submit" name="inserisci_data_glob" value="Inserisci">
		</form>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			<input type="submit" name="mese_dopo_glob" class="fas fa-caret-right" value="&#xf0da;">
		</form>
	</div>
	<div class="div-ris globali" style="display: <?php echo $display_table; ?>;">
		<div class="div-thead">
			<div class="div-tr">
				<div class="div-th" style="border-right: 0.1rem groove #1E90FF;">Username</div>
				<div class="div-th" style="color: #ff00ff; border-right: 0.1rem groove #1E90FF;">Livello</div>
				<div class="div-th" style="color: blue; border-right: 0.1rem groove #1E90FF;">Successi</div>
				<div class="div-th" style="color: red; border-right: 0.1rem groove #1E90FF;">Tentativi</div>
				<div class="div-th" style="color: green;">%Successi</div>
			</div>
		</div>
		<div class="div-tbody">
		<?php 
		switch ($_SESSION['show']) {
			case '1':
				XML_username_scelto();
				break;
			case '2':
				XML_livello_scelto();
				break;
			case '3':
				XML_classifica();
				break;
			default:
				break;
		}
		?>
		</div>
	</div>
	<div class="div-ris globali" style="display: <?php echo $display_table_lista_utenti; ?>;">
		<div class="div-thead">
			<div class="div-tr">
				<div class="div-th" style="border-right: 0.1rem groove #1E90FF; width: 15rem;">Username</div>
				<div class="div-th" style="color: blue; width: 15rem;">Stato</div>
			</div>
		</div>
		<div class="div-tbody">
		<?php 
		if ($_SESSION['show'] == 4) {
			$sql_lista_utenti = "SELECT username, status FROM $tab_utenti;";
			$sql_risq_lista_utenti = mysqli_query($mysqli_connection, $sql_lista_utenti);
			while ($utente = mysqli_fetch_array($sql_risq_lista_utenti)) {
				$lista_utenti[] = $utente;
			}
			asort($lista_utenti);
			foreach ($lista_utenti as $utente) {
				if (($utente['status']) == 'sospeso') $color = 'darkorange';
				elseif (($utente['status']) == 'cancellato') $color = 'red';
				else $color = 'black';
				echo "<div class=\"div-tr\"><div class=\"div-td\" style=\"width: 15rem;\">".$utente['username']."</div><div class=\"div-td\" style=\"color: $color; width: 15rem;\">".$utente['status']."</div></div>";
			}
		}
		?>
		</div>
	</div>
	<?php 
	$user_display = 'none';
	$user_conf_pass = 'none';
	$user_password = '';
	$user_password_attuale = '';
	$user_alert = "";
	if (!isset($_SESSION['user_case'])) $_SESSION['user_case'] = 0;
	if (!isset($_SESSION['user_content'])) $_SESSION['user_content'] = "";

	if (isset($_POST['modifica_user_username'])) {
		if (isset($_POST['user_username'])) $_SESSION['user'] = mysqli_real_escape_string($mysqli_connection, $_POST['user_username']);
	}
	if (isset($_SESSION['user'])) {
		$user_sql = "SELECT * FROM $tab_utenti WHERE username = '{$_SESSION['user']}';";
		$user_risq = mysqli_query($mysqli_connection, $user_sql);
		if (mysqli_num_rows($user_risq) > 0) {
			$user_row = mysqli_fetch_array($user_risq);
			if ($user_row['admin'] == '1') {
				$user_alert = "<p>Non puoi modificare i dati di un amministratore!</p>";
				$user_row = array();
				unset($_SESSION['user']);
			}
			else $user_display = 'block';
		}
		else {
			$user_alert = "<p>L'utente cercato non esiste!</p>";
			unset($_SESSION['user']);
		}
	}

	if (isset($_POST['modifica_user_status'])) {
		if (isset($_POST['user_status'])) $user_status = mysqli_real_escape_string($mysqli_connection, $_POST['user_status']);
		if (empty($user_status)) $user_alert = "<p>Seleziona uno stato!</p>";
		else {
			$_SESSION['user_case'] = 1;
			$_SESSION['user_content'] = $user_status;
			if ($user_status == 'cancellato') $user_alert = "<p>Sei sicuro di voler eliminare l'account di ".$_SESSION['user']."? Tutti i dati verranno cancellati. Per confermare inserisci la tua password e premi &quot;Conferma&quot;.</p>";
			else $user_alert = "<p>Sei sicuro di voler modificare lo stato di ".$_SESSION['user']."? Per confermare inserisci la tua password e premi &quot;Conferma&quot;.</p>";
			$user_display = 'none';
			$user_conf_pass = 'block';
		}
	}
	if (isset($_POST['modifica_user_password'])) {
		if (isset($_POST['user_password'])) $user_password = mysqli_real_escape_string($mysqli_connection, $_POST['user_password']);
		if (strlen($user_password) < 4) $user_alert = "<p>La password deve contenere almeno 4 caratteri!</p>";
		else {
			$_SESSION['user_case'] = 2;
			$_SESSION['user_content'] = $user_password;
			$user_alert = "<p>Sei sicuro di voler modificare la password di ".$_SESSION['user']."? Per confermare inserisci la tua password e premi &quot;Conferma&quot;.</p>";
			$user_display = 'none';
			$user_conf_pass = 'block';
		}
	}
	if (isset($_POST['conferma_user_password'])) {
		$user_conf_pass = 'block';
		$user_display = 'none';
		switch ($_SESSION['user_case']) {
			case '1':
				if (isset($_POST['user_conf_password'])) $user_password_attuale = mysqli_real_escape_string($mysqli_connection, $_POST['user_conf_password']);
				if (!password_verify($user_password_attuale, $row['password'])) $user_alert = "<p>La password inserita non &egrave; la password di questo account!</p>";
				else {
					$utenti = mysqli_query($mysqli_connection, "UPDATE $tab_utenti SET status = '{$_SESSION['user_content']}' WHERE username = '{$_SESSION['user']}';");
					if ($_SESSION['user_content'] == 'cancellato') {
						for ($i = 0; $i < $XML_utenti->length; $i++) {
							$XML_utente = $XML_utenti->item($i);
							$XML_utente_username = $XML_utente->getAttribute('username');
							if ($XML_utente_username == $_SESSION['user']) {
								$XML_utente->parentNode->removeChild($XML_utente);
								$doc->formatOutput = true;
								$doc->save('XML/risultati_allenamento.xml');
							}
						}
						for ($i = 0; $i < $XML_utenti_comp->length; $i++) {
							$XML_utente_comp = $XML_utenti_comp->item($i);
							$XML_utente_comp_username = $XML_utente_comp->getAttribute('username');
							if ($XML_utente_comp_username == $_SESSION['user']) {
								$XML_utente_comp->parentNode->removeChild($XML_utente_comp);
								$doc_comp->formatOutput = true;
								$doc_comp->save('XML/risultati_competitiva.xml');
							}
						}
					}
					unset($_SESSION['user_content']);
					header('location: '.$_SERVER['PHP_SELF'].'');
					exit();
				}			
				break;
			case '2':
				if (isset($_POST['user_conf_password'])) $user_password_attuale = mysqli_real_escape_string($mysqli_connection, $_POST['user_conf_password']);
				if (!password_verify($user_password_attuale, $row['password'])) $user_alert = "<p>La password inserita non &egrave; la password di questo account!</p>";
				else {
					$user_hashed_password = password_hash($_SESSION['user_content'], PASSWORD_DEFAULT);
					$utenti = mysqli_query($mysqli_connection, "UPDATE $tab_utenti SET password = '$user_hashed_password' WHERE username = '{$_SESSION['user']}';");
					$user_alert = "<p style='color: blue;'>Password cambiata con successo!</p>";
					$user_conf_pass = 'none';
					unset($_SESSION['user_content']);
					$user_password_attuale = '';
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
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display: <?php echo $res_dati; ?>" method="post">
			<div><i style="color: red;" class="fas fa-trash"></i><h3 style="color: red;">Resetta i tuoi risultati:</h3><input type="submit" name="resetta_dati" value="Resetta"></div>
		</form>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display: <?php echo $conf_pass; ?>" method="post">
			<div><i style="color: red;" class="fas fa-lock"></i><h3>Inserisci la tua <span style="color: red;">password attuale:</span></h3><input type="password" name="conf_password" placeholder="Password" value="<?php echo $password_attuale; ?>"><input type="submit" name="conferma_password" value="Conferma"></div>
		</form>
		<?php echo $alert; ?>
		<h1 style="display: inline;">Gestisci dati utente </h1>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" style="display: inline;">
			<input type="text" name="user_username" style="margin-bottom: 1rem;">
			<input type="submit" name="modifica_user_username" value="Cerca" style="margin-bottom: 1rem;">
		</form>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display: <?php echo $user_display; ?>" method="post">
			<div style="grid-template-columns: 5% 55% 8% 10% 12% 10%;"><i style="color: red;" class="fas fa-user-cog"></i><h3>Modifica <span style="color: red;">stato: </h3>
				<div><label for="stato1">attivo<input type="radio" name="user_status" value="attivo" <?php if (!empty($user_row) && $user_row['status'] == 'attivo') echo 'checked';?>></label></div>
				<div><label for="stato1">sospeso<input type="radio" name="user_status" value="sospeso" <?php if (!empty($user_row) && $user_row['status'] == 'sospeso') echo 'checked';?>></label></div>
				<div><label for="stato1">cancellato<input type="radio" name="user_status" value="cancellato" <?php if (!empty($user_row) && $user_row['status'] == 'cancellato') echo 'checked';?>></label></div>
				<input type="submit" name="modifica_user_status" value="Modifica" readonly></div>
		</form>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display: <?php echo $user_display; ?>" method="post">
			<div><i style="color: blueviolet;" class="far fa-envelope"></i><h3><span style="color: blueviolet;">email:</h3><input type="email" name="user_email" value="<?php if (!empty($user_row)) echo $user_row['email']; else echo '';?>" readonly></div>
		</form>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display: <?php echo $user_display; ?>" method="post">
			<div><i style="color: blueviolet;" class="far fa-envelope"></i><h3><span style="color: blueviolet;">email secondaria:</h3><input type="email" name="user_secondary_email" value="<?php if (!empty($user_row)) echo $user_row['secondary_email']; else echo '';?>" readonly></div>
		</form>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display: <?php echo $user_display; ?>" method="post">
			<div><i style="color: green;" class="fas fa-user"></i><h3><span style="color: green;">username:</span></h3><input type="text" name="user_username" value="<?php if (!empty($user_row)) echo $user_row['username']; else echo '';?>" readonly></div>
		</form>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display: <?php echo $user_display; ?>" method="post">
			<div><i style="color: blue;" class="fas fa-lock"></i><h3>Modifica <span style="color: blue;">password:</span></h3><input type="password" name="user_password" placeholder="Password" value="<?php echo $user_password; ?>"><input type="submit" name="modifica_user_password" value="Modifica"></div>
		</form>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display: <?php echo $user_display; ?>" method="post">
			<div><i style="color: deeppink;" class="fas fa-map-marked-alt"></i><h3><span style="color: deeppink;">indirizzo:</h3><input type="text" name="user_address" value="<?php if (!empty($user_row)) echo $user_row['address']; else echo '';?>" readonly></div>
		</form>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display: <?php echo $user_display; ?>" method="post">
			<div><i style="color: brown;" class="fas fa-map-marker-alt"></i><h3><span style="color: brown;">codice postale:</h3><input type="number" name="user_cap" value="<?php if (!empty($user_row)) echo $user_row['cap']; else echo '';?>" readonly></div>
		</form>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display: <?php echo $user_display; ?>" method="post">
			<div><i style="color: darkorange;" class="fas fa-birthday-cake"></i><h3><span style="color: darkorange;">data di nascita:</h3><input type="date" name="user_birthday" value="<?php if (!empty($user_row)) echo $user_row['birthday']; else echo '';?>" readonly></div>
		</form>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display: <?php echo $user_conf_pass; ?>" method="post">
			<div><i style="color: red;" class="fas fa-lock"></i><h3>Inserisci la tua <span style="color: red;">password attuale:</span></h3><input type="password" name="user_conf_password" placeholder="Password" value="<?php echo $user_password_attuale; ?>"><input type="submit" name="conferma_user_password" value="Conferma"></div>
		</form>
		<?php echo $user_alert; ?>
	</div>
	</div>
</body>
</html>