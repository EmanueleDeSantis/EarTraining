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

if (isset($_POST['logout'])) {
	unset($_SESSION);
	session_destroy();
	header("location: ../index.php");
	exit();
}

$display_login = 'list-item';
$display_form = 'none';

if(!isset($_SESSION['data'])) {
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

$xmlString = "";
foreach (file('XML/risultati_allenamento.xml') as $node) $xmlString .= trim($node);
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
			$doc->save('XML/risultati_allenamento.xml');
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
	$doc->save('XML/risultati_allenamento.xml');
}

if (!isset($_SESSION['rand_nota'])) $_SESSION['rand_nota'] = array();
if (!isset($_SESSION['note_prod'])) $_SESSION['note_prod'] = array();
if (!isset($_SESSION['suoni_prod'])) $_SESSION['suoni_prod'] = array();

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
   	$_SESSION['rand_nota'] = $rand_id_nota;   	
   	$_SESSION['note_prod'] = $note_prodotte;   	
   	$_SESSION['suoni_prod'] = $suoni_prodotti;	
}

function play_again() {
	global $mysqli_connection;
	$num_rand_id_nota = count($_SESSION['rand_nota']);
	$suoni_prodotti[] = $_SESSION['suoni_prod'];
	for ($x = 0; $x < $num_rand_id_nota; $x++) {
		echo '<audio autoplay="autoplay"><source src="'.$_SESSION['suoni_prod'][$x].'" type="audio/mpeg"></audio>';
   	}
}

function from_note_to_number($nota) {
	if ($nota == "Do") return $nota = 1;
	elseif ($nota == "Do#") return $nota = 2;
	elseif ($nota == "Re") return $nota = 3;
	elseif ($nota == "Re#") return $nota = 4;
	elseif ($nota == "Mi") return $nota = 5;
	elseif ($nota == "Fa") return $nota = 6;
	elseif ($nota == "Fa#") return $nota = 7;
	elseif ($nota == "Sol") return $nota = 8;
	elseif ($nota == "Sol#") return $nota = 9;
	elseif ($nota == "La") return $nota = 10;
	elseif ($nota == "La#") return $nota = 11;
	elseif ($nota == "Si") return $nota = 12;
}

function from_number_to_note($nota) {
	if ($nota == 1) return $nota = "Do";
	elseif ($nota == 2) return $nota = "Do#";
	elseif ($nota == 3) return $nota = "Re";
	elseif ($nota == 4) return $nota = "Re#";
	elseif ($nota == 5) return $nota = "Mi";
	elseif ($nota == 6) return $nota = "Fa";
	elseif ($nota == 7) return $nota = "Fa#";
	elseif ($nota == 8) return $nota = "Sol";
	elseif ($nota == 9) return $nota = "Sol#";
	elseif ($nota == 10) return $nota = "La";
	elseif ($nota == 11) return $nota = "La#";
	elseif ($nota == 12) return $nota = "Si";
}

if (!isset($_SESSION['refresh'])) $_SESSION['refresh'] = 0;
if (!isset($_SESSION['value'])) $_SESSION['value'] = 1;
if (!isset($_SESSION['display_quali_note'])) $_SESSION['display_quali_note'] = 'none';
if (!isset($_SESSION['display_replay'])) $_SESSION['display_replay'] = 'none';
$alert = "";

if (isset($_POST['quante_note'])) {
	if (empty($_POST['scelta'])) {
		$alert = "<p style=\"color: red;\">Scegli il numero di note da riprodurre!</p>";
		$_SESSION['display_quali_note'] = 'none';
		$_SESSION['display_replay'] = 'none';
	}
	else {
		play($_POST['scelta']);
		$_SESSION['display_replay'] = 'block';
		$_SESSION['display_quali_note'] = 'block'; 
		$_SESSION['value'] = $_POST['scelta'];
	}
	$_SESSION['refresh'] = 0;
}

if (isset($_POST['aiuto'])) {
	$query = "SELECT * FROM note WHERE id = 27;"; /* La */
   	$resq = mysqli_query($mysqli_connection, $query);    	
    $fetch = mysqli_fetch_array($resq);
    echo '<audio autoplay="autoplay"><source src="'.$fetch['suono'].'" type="audio/mpeg"></audio>';
}

if (isset($_POST['quali_note']) && $_SESSION['refresh'] == 0) {
	if (empty($_POST['note_scelte'])) $alert = "<p style=\"color: red;\">Seleziona le note da indovinare!</p>";
	else {
		$crea_livello = 1;
		$arr_livelli_id = array();
		$XML_livelli = $XML_stagione->getElementsByTagName('livello');
		for ($k = 0; $k < $XML_livelli->length; $k++) {
			$XML_livello = $XML_livelli->item($k);
			$XML_livello_id = $XML_livello->getAttribute('id');
			$arr_livelli_id[] = $XML_livello_id;
			if ($XML_livello_id == $_SESSION['value'])
				$crea_livello = 0;
		}
		if ($crea_livello) {//crea livello
			$XML_livello = $doc->createElement('livello');
			$XML_livello_id = $doc->createAttribute('id');
			$XML_livello_id->value = $_SESSION['value'];
			$XML_livello->appendChild($XML_livello_id);
			$XML_livello_id = $XML_livello->getAttribute('id');
			if (!empty($arr_livelli_id)) asort($arr_livelli_id); //ordina l'array di id in senso ascendente
			if (empty($arr_livelli_id) || $XML_livello_id > max($arr_livelli_id)) { //vengono prima inseriti i livelli e poi le note
				if ($XML_livelli->length == 0) 
					$XML_stagione->insertBefore($XML_livello, $XML_stagione->firstChild);
				else 
					$XML_stagione->insertBefore($XML_livello, $XML_livelli->item(($XML_livelli->length) - 1)->nextSibling);
			}
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
			$doc->save('XML/risultati_allenamento.xml');
		}

		$_SESSION['display_quali_note'] = 'none';
		$note_prodotte = array_unique($_SESSION['note_prod']);
		foreach ($note_prodotte as $nota_prodotta) {
			$crea_nota = 1;
			$arr_note_nome = array();
			$arr_note_numero = array();
			$XML_note = $XML_stagione->getElementsByTagName('nota');
			for ($k = 0; $k < $XML_note->length; $k++) {
				$XML_nota = $XML_note->item($k);
				$XML_nota_nome = $XML_nota->getAttribute('nome');
				$arr_note_nome[] = $XML_nota_nome;
				$aux = $XML_nota_nome;
				$arr_note_numero[] = from_note_to_number($aux); //converte tutte le note nei loro numeri corrispondenti
				if ($XML_nota_nome == $nota_prodotta) {
					$crea_nota = 0;
					$XML_nota->lastChild->nodeValue += 1;
					if (in_array($nota_prodotta, $_POST['note_scelte'])) 
						$XML_nota->firstChild->nodeValue += 1;
				}
			}
			if ($crea_nota) {
				$XML_nota = $doc->createElement('nota');
				$XML_nota_nome = $doc->createAttribute('nome');
				$XML_nota_nome->value = $nota_prodotta;
				$XML_nota->appendChild($XML_nota_nome);
				$XML_nota_nome = $XML_nota->getAttribute('nome');
				$aux = $XML_nota_nome;
				$XML_nota_numero = from_note_to_number($aux);
				if (!empty($arr_note_numero)) asort($arr_note_numero); //ordina l'array di note in senso ascendente
				if (empty($arr_note_numero) || $XML_nota_numero > max($arr_note_numero))
					$XML_stagione->appendChild($XML_nota);
				elseif ($XML_nota_numero < min($arr_note_numero)) { //vengono prima inseriti i livelli e poi le note
					if ($XML_note->length == 0)
						$XML_stagione->appendChild($XML_nota);
					else 
						$XML_stagione->insertBefore($XML_nota, $XML_note->item(0));
				}
				else {
					foreach ($arr_note_numero as $nota_numero) {
						if ($XML_nota_numero < $nota_numero) {
							$max_arr_note_numero[] = $nota_numero; //l'array contiene tutte le note maggiori di quella da inserire (in senso ascendente)
							$XML_nota_next_number = min($max_arr_note_numero); //la nota successiva a quella da inserire è quella minore tra le maggiori
							$aux = $XML_nota_next_number;
							$XML_nota_next_nome = from_number_to_note($aux); //converte il numero della nota alla nota corrispondente
							for ($k = 0; $k < $XML_note->length; $k++) {
								$XML_nota_search = $XML_note->item($k);
								$XML_nota_search_nome = $XML_nota_search->getAttribute('nome');
								if ($XML_nota_next_nome == $XML_nota_search_nome) 
									$XML_stagione->insertBefore($XML_nota, $XML_nota_search);
							}
						}
					}
				}
				if (in_array($nota_prodotta, $_POST['note_scelte'])) 
					$XML_successi = $doc->createElement('successi', '1');
				else $XML_successi = $doc->createElement('successi', '0');
				$XML_nota->appendChild($XML_successi);
				$XML_tentativi = $doc->createElement('tentativi', '1');
				$XML_nota->appendChild($XML_tentativi);
				$doc->formatOutput = true;
				$doc->save('XML/risultati_allenamento.xml');
			}
		}

		if ($note_prodotte === array_intersect($note_prodotte, $_POST['note_scelte']) && $_POST['note_scelte'] === array_intersect($_POST['note_scelte'], $note_prodotte)) {
			$alert = "<p style=\"color: blue;\">Complimenti!!!</p>";
			for ($k = 0; $k < $XML_livelli = $XML_stagione->getElementsByTagName('livello')->length; $k++) {
				$XML_livello = $XML_livelli = $XML_stagione->getElementsByTagName('livello')->item($k);
				$XML_livello_id = $XML_livello->getAttribute('id');
				if ($XML_livello_id == $_SESSION['value']) {
					$XML_livello->firstChild->nodeValue += 1;
					$doc->formatOutput = true;
					$doc->save('XML/risultati_allenamento.xml');
				}
			}		
		}
		else $alert = "<p style=\"color: red;\">Peccato, la risposta era <span style=\"color: blue;\">".implode(" ", $_SESSION['note_prod'])."</span></p>";
		$_SESSION['refresh'] += 1;
		for ($k = 0; $k < $XML_livelli = $XML_stagione->getElementsByTagName('livello')->length; $k++) {
			$XML_livello = $XML_livelli = $XML_stagione->getElementsByTagName('livello')->item($k);
			$XML_livello_id = $XML_livello->getAttribute('id');
			if ($XML_livello_id == $_SESSION['value']) {
				$XML_livello->lastChild->nodeValue += 1;
				$doc->formatOutput = true;
				$doc->save('XML/risultati_allenamento.xml');
			}
		}
	}
}

if (isset($_POST['play_again'])) {
	play_again();
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
			<div class="link"><a id="state" title="Competitiva" href="ear_training_competitiva.php">Ear Training</a></div>
			<div class="hr-sx" id="hr4"></div>
		</div>
		</div>
		<div class="rightnav">
		<div class="cont">
		<div class="rcont-dx">
		<h2 class="risultati">Risultati allenamento</h2>
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
						echo "<div class=\"div-tr\"><div class=\"div-td\">".$XML_livello_id."</div><div class=\"div-td\">".$XML_successi_value."</div><div class=\"div-td\">".$XML_tentativi_value."</div><div class=\"div-td\">".number_format($XML_perc,2,",",".")."%</div></div>";
					}
				}
			}
			?>
			</div>
		</div>
		</div>
		<div class="rcont-sx">
		<h2 class="risultati">Statistiche note</h2>
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
						echo "<div class=\"div-tr\"><div class=\"div-td\">".$XML_nota_nome."</div><div class=\"div-td\">".$XML_successi_value."</div><div class=\"div-td\">".$XML_tentativi_value."</div><div class=\"div-td\">".number_format($XML_perc,2,",",".")."%</div></div>";
					}
				}
			}
			?>
			</div>
		</div>
		</div>
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
		</div>
		<div class="main">
		<div class="titolo"><a style="font-size: 2.5rem;" title="Allenamento" href="ear_training_allenamento.php">&#x1f4aa;</a><h1>Allena il tuo orecchio!</h1><a style="font-size: 1.7rem;" title="Competitiva" href="ear_training_competitiva.php">&#x1f451;</a></div>
		<form action="#riproduci" id="riproduci" method="post">
			<span>Scegli quante note vuoi riprodurre contemporaneamente: </span>
			<input type="number" name="scelta" min="1" max="42" value="<?php echo $_SESSION['value']; ?>">
			<input type="submit" name="quante_note" value="Riproduci">
		</form>   
		<form action="#la" id="a" method="post">
			<span>Se ti senti in difficolt&agrave; clicca il tasto "La" per ascoltarne il suono!</span>
			<input type="submit" name="aiuto" value="La">
		</form>	
		<form style="display: <?php echo $_SESSION['display_quali_note'] ?>;" action="#conferma" id="conferma" method="post">
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
		<form style="display: <?php echo $_SESSION['display_replay'] ?>;" action="#play_again" id="play_again" method="post">
			<span>Se vuoi riascoltare le ultime note proposte clicca qui</span>
			<input type="submit" name="play_again" value="Play again">
		</form>
		<?php echo $alert; ?>
		</div> <!-- main -->
	</div>
</body>
</html>