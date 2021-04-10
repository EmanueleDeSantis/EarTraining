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

require_once('../bridge2/connection.php');
echo '<link rel="shortcut icon" type="image/svg" href="../MySite/icon.svg">'; /* per visualizzare la fav icon durante l'uso delle form */
session_start();

$_SESSION['current_page'] = $_SERVER['PHP_SELF'];

if (!isset($_SESSION['accesso_permesso'])) {
	header('Location: effettua_login.html');
	exit();
}
elseif (($_SESSION['accesso_permesso']) == "sospeso") {
	header('Location: utente_sospeso.html');
	exit();
}
elseif (($_SESSION['accesso_permesso']) == "cancellato") {
	header('Location: utente_cancellato.html');
	exit();
}
if (($_SESSION['accesso_admin']) == "1") {
	header('Location: stop_admin.html');
	exit();
}

if (isset($_POST['logout'])) {
	unset($_SESSION);
	session_destroy();
	header("location: ../index.php");
	exit();
}

$display_login = 'list-item';
$display_form = 'none';

$xmlString = "";
foreach (file('XML/risultati_competitiva.xml') as $node) $xmlString .= trim($node);
$doc = new DOMDocument();
$doc->loadXML($xmlString);
$crea_utente = 1;
$crea_stagione = 1;
$XML_lista_utenti = $doc->documentElement;
$XML_utenti = $XML_lista_utenti->childNodes;
for ($i = 0; $i < $XML_utenti->length; $i++) {
	$XML_utente = $XML_utenti->item($i);
	$XML_utente_id = $XML_utente->getAttribute('id');
	if ($XML_utente_id == $_SESSION['id_user']) {
		$crea_utente = 0;
		$XML_stagioni = $XML_utente->childNodes;
		for ($j = 0; $j < $XML_stagioni->length; $j++) {
			$XML_stagione = $XML_stagioni->item($j);
			$XML_stagione_mese = $XML_stagione->getAttribute('mese');
			if ($XML_stagione_mese == date('Y-m'))
				$crea_stagione = 0;
		}
		if ($crea_stagione) {//crea stagione
			$XML_stagione = $doc->createElement('stagione');
			$XML_stagione_mese = $doc->createAttribute('mese');
			$XML_stagione_mese->value = date('Y-m');
			$XML_stagione->appendChild($XML_stagione_mese);
			$XML_utente->appendChild($XML_stagione);
			$XML_stagioni = $XML_utente->childNodes;
			$doc->formatOutput = true;
			$doc->save('XML/risultati_competitiva.xml');
		}
	}
}
if ($crea_utente) {//crea utente e stagione
	$XML_utente = $doc->createElement('utente');
	$XML_utente_id = $doc->createAttribute('id');
	$XML_utente_id->value = $_SESSION['id_user'];
	$XML_utente->appendChild($XML_utente_id);
	$XML_utente_username = $doc->createAttribute('username');
	$XML_utente_username->value = $_SESSION['usernome'];
	$XML_utente->appendChild($XML_utente_username);
	$XML_lista_utenti->appendChild($XML_utente);
	$XML_stagione = $doc->createElement('stagione');
	$XML_stagione_mese = $doc->createAttribute('mese');
	$XML_stagione_mese->value = date('Y-m');
	$XML_stagione->appendChild($XML_stagione_mese);
	$XML_utente->appendChild($XML_stagione);
	$XML_stagioni = $XML_utente->childNodes;
	$doc->formatOutput = true;
	$doc->save('XML/risultati_competitiva.xml');
}

if (!isset($_SESSION['rand_nota_comp'])) $_SESSION['rand_nota_comp'] = array();
if (!isset($_SESSION['note_prod_comp'])) $_SESSION['note_prod_comp'] = array();
if (!isset($_SESSION['suoni_prod_comp'])) $_SESSION['suoni_prod_comp'] = array();

function play($note) {
	global $mysqli_connection;
    for ($x = 0; $x < $note; $x++) {
    	$rand_id_nota[] = rand(1,42);
    	$sql = "SELECT * FROM note WHERE id = $rand_id_nota[$x];";
   		$risq = mysqli_query($mysqli_connection, $sql);    	
    	$row = mysqli_fetch_array($risq);    	
    	$note_prodotte[] = $row['nota'];    	
    	$suoni_prodotti[] = $row['suono'];
		echo '<audio autoplay="autoplay"><source src="'.$row['suono'].'" type="audio/mpeg"></audio>';
   	}
   	$_SESSION['rand_nota_comp'] = $rand_id_nota;   	
   	$_SESSION['note_prod_comp'] = $note_prodotte;   	
   	$_SESSION['suoni_prod_comp'] = $suoni_prodotti;
}

function play_again() {
	global $mysqli_connection;
	$num_rand_id_nota = count($_SESSION['rand_nota_comp']);
	$suoni_prodotti[] = $_SESSION['suoni_prod_comp'];
	for ($x = 0; $x < $num_rand_id_nota; $x++) {
		echo '<audio autoplay="autoplay"><source src="'.$_SESSION['suoni_prod_comp'][$x].'" type="audio/mpeg"></audio>';
   	}
}

if (!isset($_SESSION['refresh_comp'])) $_SESSION['refresh_comp'] = 0;
if (!isset($_SESSION['value_comp'])) $_SESSION['value_comp'] = 1;
if (!isset($_SESSION['display_quali_note_comp'])) $_SESSION['display_quali_note_comp'] = 'none';
if (!isset($_SESSION['display_replay_comp'])) $_SESSION['display_replay_comp'] = 'none';
$alert = "";

if (isset($_POST['quante_note'])) {
	$_SESSION['display_replay_comp'] = 'none';
	if (empty($_POST['scelta'])) {
		$alert = "<p style=\"color: red;\">Scegli il numero di note da riprodurre!</p>";
		$_SESSION['display_quali_note_comp'] = 'none';
	}
	else {
		play($_POST['scelta']);
		$_SESSION['display_quali_note_comp'] = 'block'; 
		$_SESSION['value_comp'] = $_POST['scelta'];
	}
	$_SESSION['refresh_comp'] = 0;
}

if (isset($_POST['quali_note']) && $_SESSION['refresh_comp'] == 0) {
	if (empty($_POST['note_scelte'])) $alert = "<p style=\"color: red;\">Seleziona le note da indovinare!</p>";
	else {
		$crea_livello = 1;
		$arr_livelli_id = array();
		$XML_livelli = $XML_stagione->getElementsByTagName('livello');
		for ($k = 0; $k < $XML_livelli->length; $k++) {
			$XML_livello = $XML_livelli->item($k);
			$XML_livello_id = $XML_livello->getAttribute('id');
			$arr_livelli_id[] = $XML_livello_id;
			if ($XML_livello_id == $_SESSION['value_comp'])
				$crea_livello = 0;						
		}
		if ($crea_livello) {//crea livello
			$XML_livello = $doc->createElement('livello');
			$XML_livello_id = $doc->createAttribute('id');
			$XML_livello_id->value = $_SESSION['value_comp'];
			$XML_livello->appendChild($XML_livello_id);
			$XML_livello_id = $XML_livello->getAttribute('id');
			if (!empty($arr_livelli_id)) asort($arr_livelli_id); //ordina l'array di id in senso ascendente
			if (empty($arr_livelli_id) || $XML_livello_id > max($arr_livelli_id))
					$XML_stagione->appendChild($XML_livello);
			elseif ($XML_livello_id < min($arr_livelli_id)) 
					$XML_stagione->insertBefore($XML_livello, $XML_stagione->firstChild);
			else {
				foreach ($arr_livelli_id as $livello_id) { //inserisce i livelli in ordine crescente
					if ($XML_livello_id < $livello_id) {
						$max_arr_livelli_id[] = $livello_id; //l'array contiene tutti gli id maggiori di quello da inserire (in senso ascendente)
						$XML_livello_next_id = min($max_arr_livelli_id); //il livello successivo a quello da inserire è quello avente id minore tra i maggiori
						for ($k = 0; $k < $XML_livelli->length; $k++) {
							$XML_livello_search = $XML_livelli->item($k);
							$XML_livello_search_id = $XML_livello_search->getAttribute('id');
							if ($XML_livello_next_id == $XML_livello_search_id)
								$XML_stagione->insertBefore($XML_livello, $XML_livello_search);
						}
					}
				}
			}
			$XML_successi = $doc->createElement('successi', '0');
			$XML_livello->appendChild($XML_successi);
			$XML_tentativi = $doc->createElement('tentativi', '0');
			$XML_livello->appendChild($XML_tentativi);
			$doc->formatOutput = true;
			$doc->save('XML/risultati_competitiva.xml');
		}

		$_SESSION['display_quali_note_comp'] = 'none';
		$_SESSION['display_replay_comp'] = 'block';
		$note_prodotte = array_unique($_SESSION['note_prod_comp']);
		if ($note_prodotte === array_intersect($note_prodotte, $_POST['note_scelte']) && $_POST['note_scelte'] === array_intersect($_POST['note_scelte'], $note_prodotte)) {
			$alert = "<p style=\"color: blue;\">Complimenti!</p>";
			for ($k = 0; $k < $XML_stagione->childNodes->length; $k++) {
				$XML_livello = $XML_stagione->childNodes->item($k);
				$XML_livello_id = $XML_livello->getAttribute('id');
				if ($XML_livello_id == $_SESSION['value_comp']) {
					$XML_livello->firstChild->nodeValue += 1;
					$doc->formatOutput = true;
					$doc->save('XML/risultati_competitiva.xml');
				}
			}
			
		}
		else $alert = "<p style=\"color: red;\">Peccato, la risposta era <span style=\"color: blue;\">".implode(" ", $_SESSION['note_prod_comp'])."</span></p>";
		$_SESSION['refresh_comp'] += 1;
		for ($k = 0; $k < $XML_stagione->childNodes->length; $k++) {
			$XML_livello = $XML_stagione->childNodes->item($k);
			$XML_livello_id = $XML_livello->getAttribute('id');
			if ($XML_livello_id == $_SESSION['value_comp']) {
				$XML_livello->lastChild->nodeValue += 1;
				$doc->formatOutput = true;
				$doc->save('XML/risultati_competitiva.xml');
			}
		}
	}
}

if (isset($_POST['play_again'])) {
	play_again();
}

if(!isset($_SESSION['data_pers'])) {
	$_SESSION['data_pers'] = date_create(date('Y-m'));
	$_SESSION['data_pers'] = date_format($_SESSION['data_pers'], 'Y-m');
}

if (isset($_POST['inserisci_data_pers'])) {
	if (isset($_POST['data_inserita_pers'])) {
		$_SESSION['data_pers'] = date_create($_POST['data_inserita_pers']);
		$_SESSION['data_pers'] = date_format($_SESSION['data_pers'], 'Y-m');
	}
}

if (isset($_POST['mese_prima_pers'])) {
	$_SESSION['data_pers'] = date_create(date($_SESSION['data_pers']));
	date_modify($_SESSION['data_pers'], '-1 month');
	$_SESSION['data_pers'] = date_format($_SESSION['data_pers'], 'Y-m');
}

if (isset($_POST['mese_dopo_pers'])) {
	$_SESSION['data_pers'] = date_create($_SESSION['data_pers']);
	date_modify($_SESSION['data_pers'], '+1 month');
	$_SESSION['data_pers'] = date_format($_SESSION['data_pers'], 'Y-m');
}

$display_table = 'none';
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

<?php echo '<?xml version="1.0" encoding="UTF-8"'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head><title>Bridge</title>
<meta name="viewport" content="viewport">
<!-- la dichiarazione della fav icon è ala riga 4 -->
<link rel="stylesheet" type="text/css" href="ear_training.css">
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
		<span title="Area personale" class="nome"><a href="area_personale.php"><?php echo $_SESSION['usernome']; ?></a></span>
		<sub><input title="logout" type="submit" name="logout" class="fas fa-sign-out-alt" value="&#xf2f5;"></sub>
	</form>
	</div>
	<div class="container">
		<div class="leftnav">
		<div class="menu-sx">
			<div class="link"><a href="../MySite/teoriamusicale.html">Teoria musicale</a></div>
			<div class="hr-sx" id="hr1"></div>
			<div class="link"><a href="../MySite/temperamento.html">Temperamento</a></div>
			<div class="hr-sx" id="hr2"></div>
			<div class="link"><a href="../MySite/armonia.html">Armonia</a></div>
			<div class="hr-sx" id="hr3"></div>
			<div class="link"><a id="state" title="Allenamento" href="ear_training_allenamento.php">Ear Training</a></div>
			<div class="hr-sx" id="hr4"></div>
		</div>
		</div>
		<div class="rightnav">
		<div class="cont">
		<div class="rcont-dx">
		<h2 class="risultati">Risultati personali</h2>
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
			for ($j = 0; $j < $XML_stagioni->length; $j++) {
				$XML_stagione = $XML_stagioni->item($j);
				$XML_stagione_mese = $XML_stagione->getAttribute('mese');
				if ($XML_stagione_mese == $_SESSION['data_pers']) {
					$XML_livelli = $XML_stagione->getElementsByTagName('livello');
					for ($k = 0; $k < $XML_livelli->length; $k++) {
						$XML_livello = $XML_livelli->item($k);
						$XML_livello_id = $XML_livello->getAttribute('id');
						$XML_successi = $XML_livello->firstChild;
						$XML_successi_value = $XML_successi->textContent;
						$XML_tentativi = $XML_livello->lastChild;
						$XML_tentativi_value = $XML_tentativi->textContent;
						$XML_perc = ($XML_successi_value / $XML_tentativi_value) * 100;
						echo "<div class=\"div-tr\"><div class=\"div-td\">".$XML_livello_id."</div><div class=\"div-td\">".$XML_successi_value."</div><div class=\"div-td\">".$XML_tentativi_value."</div><div class=\"div-td\">".number_format($XML_perc,2,",",".")."%</div></div>";
					}
				}
			}
			?>
			</div>
		</div>
		<div class="ris-form">
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
				<input type="submit" name="mese_prima_pers" class="fas fa-caret-left" value="&#xf0d9;">
			</form>
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
				<input type="month" name="data_inserita_pers" value="<?php echo $_SESSION['data_pers']; ?>">
				<input type="submit" name="inserisci_data_pers" value="Inserisci">
			</form>
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
				<input type="submit" name="mese_dopo_pers" class="fas fa-caret-right" value="&#xf0da;">
			</form>
		</div>
		</div>
		<?php 
		if (!isset($_SESSION['show'])) $_SESSION['show'] = 0;

		if (isset($_POST['username_scelto'])) {
			$query_esiste_utente = "SELECT * FROM $tab_utenti WHERE username = '{$_POST['username_scelto']}';";
			$result_esiste_utente = mysqli_query($mysqli_connection, $query_esiste_utente);
			$fetch_esiste_utente = mysqli_fetch_array($result_esiste_utente);
			if (empty($fetch_esiste_utente)) $alert = "<p style=\"color: red;\">L'utente cercato non esiste!</p>";
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

		function XML_username_scelto() {
			global $XML_lista_utenti;
			global $XML_utenti;
			for ($i = 0; $i < $XML_utenti->length; $i++) {
				$XML_utente = $XML_utenti->item($i);
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
			global $XML_lista_utenti;
			global $XML_utenti;
			$array_classifica = array();
			$array_of_array_classifica = array();
			for ($i = 0; $i < $XML_utenti->length; $i++) {
				$XML_utente = $XML_utenti->item($i);
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
			global $XML_lista_utenti;
			global $XML_utenti;
			$array_classifica = array();
			$array_of_array_classifica = array();
			for ($i = 0; $i < $XML_utenti->length; $i++) {
				$XML_utente = $XML_utenti->item($i);
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
		<div class="rcont-sx">
			<h2 class="risultati">Risultati globali</h2>	
			<h3 style="color: red; margin-top: 1rem; margin-bottom: 1rem;">Cerca per:</h3>
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
				<span>Username:&nbsp;</span><input type="text" name="username_scelto">
				<input type="submit" name="quale_utente" value="Cerca">
			</form>
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
				<span style="color: #ff00ff;">Livello:&nbsp;</span><input type="number" min="1" max="42" name="livello_scelto" value="<?php echo $_SESSION['value_comp']; ?>">
				<input type="submit" name="quale_livello" value="Cerca">
			</form>
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
				<span style="color: red;">Classifica completa:&nbsp;</span><input type="submit" name="classifica" value="Mostra">
			</form>
		</div>
		</div>
		</div>
		<div class="main">
		<div class="titolo"><a style="font-size: 1.7rem;" title="Allenamento" href="ear_training_allenamento.php">&#x1f4aa;</a><h1>Modalit&agrave; competitiva</h1><a style="font-size: 2.5rem;" title="Competitiva" href="ear_training_competitiva.php">&#x1f451;</a></div>
		<form action="#riproduci" id="riproduci" method="post">
			<span>Scegli quante note vuoi riprodurre contemporaneamente: </span>
			<input type="number" name="scelta" min="1" max="42" value="<?php echo $_SESSION['value_comp']; ?>">
			<input type="submit" name="quante_note" value="Riproduci">
		</form>   
		<form style="display: <?php echo $_SESSION['display_quali_note_comp'] ?>;" action="#conferma" id="conferma" method="post">
			<span>Do</span><input type="checkbox" name="note_scelte[]" value="Do">
			<span>Do&#x266f;</span><input type="checkbox" name="note_scelte[]" value="Do#">
			<span>Re</span><input type="checkbox" name="note_scelte[]" value="Re">
			<span>Re&#x266F;</span><input type="checkbox" name="note_scelte[]" value="Re#">
			<span>Mi</span><input type="checkbox" name="note_scelte[]" value="Mi">
			<span>Fa</span><input type="checkbox" name="note_scelte[]" value="Fa">
			<span>Fa&#x266F;</span><input type="checkbox" name="note_scelte[]" value="Fa#">
			<span>Sol</span><input type="checkbox" name="note_scelte[]" value="Sol">
			<span>Sol&#x266F;</span><input type="checkbox" name="note_scelte[]" value="Sol#">
			<span>La</span><input type="checkbox" name="note_scelte[]" value="La">
			<span>La&#x266F;</span><input type="checkbox" name="note_scelte[]" value="La#">
			<span>Si</span><input type="checkbox" name="note_scelte[]" value="Si">
			<input type="submit" name="quali_note" value="Conferma">
		</form>
		<form style="display: <?php echo $_SESSION['display_replay_comp'] ?>;" action="#play_again" id="play_again" method="post">
			<span>Se vuoi riascoltare le ultime note proposte clicca qui</span>
			<input type="submit" name="play_again" value="Play again">
		</form>
		<?php echo $alert; ?>
		<div class="ris-form" style="display: <?php echo $display_data_glob; ?>;">
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
					<div class="div-th" style="border-right: 0.1rem groove #1E90FF; border-bottom: 0.1rem groove #1E90FF;">Username</div>
					<div class="div-th" style="color: #ff00ff; border-right: 0.1rem groove #1E90FF; border-bottom: 0.1rem groove #1E90FF;">Livello</div>
					<div class="div-th" style="color: blue; border-right: 0.1rem groove #1E90FF; border-bottom: 0.1rem groove #1E90FF;">Successi</div>
					<div class="div-th" style="color: red; border-right: 0.1rem groove #1E90FF; border-bottom: 0.1rem groove #1E90FF;">Tentativi</div>
					<div class="div-th" style="color: green; border-bottom: 0.1rem groove #1E90FF;">%Successi</div>
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
		</div> <!-- div-tabella -->
		</div> <!-- main -->
	</div>
</body>
</html>