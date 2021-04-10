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

require_once('bridge2/connection.php');
session_start();

$_SESSION['current_page'] = $_SERVER['PHP_SELF'];

$display_login = 'list-item';
$display_form = 'none';
$display_nome = 'none';
$display_lettore = 'inline';

if (isset($_SESSION['accesso_permesso'])) {
	$display_login = 'none'; 
	$display_form = 'block'; 
	$display_nome = 'inline';
	$display_lettore = 'none';
	if (isset($_POST['logout'])) {
		$display_login = 'none'; 
		$display_form = 'block'; 
		$display_nome = 'inline';
		$display_lettore = 'none';
		unset($_SESSION);
		session_destroy();
		header('location: '.$_SERVER['PHP_SELF'].'');
		exit();
	}
}

?>

<?php echo '<?xml version="1.0" encoding="UTF-8"'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head><title>Bridge</title>
<meta name="viewport" content="viewport">
<link rel="shortcut icon" type="image/svg" href="MySite/icon.svg">
<link rel="stylesheet" type="text/css" href="bridge2/index.css">
<link rel="stylesheet" type="text/css" href="https://use.fontawesome.com/releases/v5.13.0/css/all.css"> 
</head>
<body>
	<div class="topnav">
	<ul class="menu">
		<li class="icons" id="stato"><a href="<?php echo $_SERVER['PHP_SELF']; ?>">&#x1f3e1;</a></li>
		<li><a href="MySite/aboutme.html">About Me</a></li>
		<li class="icons"><a href="#"><i class="fab fa-facebook-square"></i></a></li>
		<li class="icons"><a href="#"><i class="fab fa-instagram"></i></a></li>
		<li><a href="mailto:musicskills.altervista.org@gmail.com">Mail me</a></li>
		<li style="display: <?php echo $display_login ?>"><a href="bridge2/login.php">Login</a></li>
	</ul>
	<form class="logout" style="display: <?php echo $display_form ?>" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<span title ="Area personale" class="nome"><a href="bridge3/area_personale.php"><?php echo $_SESSION['usernome']; ?></a></span>
		<sub><input title="logout" type="submit" name="logout" class="fas fa-sign-out-alt" value="&#xf2f5;"></sub>
	</form>
	</div>
	<div class="container">
		<div class="leftnav">
		<div class="menu-sx">
			<div class="link"><a href="MySite/teoriamusicale.html">Teoria musicale</a></div>
			<div class="hr-sx" id="hr1"></div>
			<div class="link"><a href="MySite/temperamento.html">Temperamento</a></div>
			<div class="hr-sx" id="hr2"></div>
			<div class="link"><a href="MySite/armonia.html">Armonia</a></div>
			<div class="hr-sx" id="hr3"></div>
			<div class="link"><a href="bridge3/ear_training_allenamento.php">Ear Training</a></div>
			<div class="hr-sx" id="hr4"></div>
		</div>
		</div>
		<div class="main">
		<h1 style="font-size: 2.7rem;">Caro <span style="display: <?php echo $display_nome ?>; color: green;"><a href="bridge3/area_personale.php" style="text-decoration: none;"><?php echo $_SESSION['usernome']; ?></a></span><span style="display: <?php echo $display_lettore ?>;">lettore</span>, benvenuto nel mio sito web!</h1>
		<p>Qui troverai dell'ottimo materiale per approcciare il mondo della musica da zero in modo talvolta intuitivo, talvolta formale,
		al fine di rendere chiara la lettura e rigorosa la trattazione. Nel men&ugrave; sinistro sono presenti i vari argomenti trattati,
		mentre sulla destra c'&egrave; la sezione dedicata ai testi da me consigliati, di cui la maggior parte viene adoperata anche nei conservatori.
		Senza dilungarmi oltre, ti auguro buona lettura!
		</p>
		<h1>Introduzione alla musica</h1>
		<p>Volendo dare una definizione, per musica si intende l'arte di combinare, in verticale e in orizzontale, suoni nel tempo.
		&Egrave; evidente che da questo punto di vista qualunque forma di suono pu&ograve; essere intesa come "musica", e in effetti lo &egrave;, ma solamente da un punto
		di vista generalizzato, di fatto per come la si intende normalmente, &egrave; una <strong>combinazione organizzata di suoni</strong>, definita
		secondo le varie regole della <a href="MySite/teoriamusicale.html">teoria musicale</a>. A questo punto ci si potrebbe domandare come mai ci sia bisogno 
		di seguire delle regole dal momento che la musica &egrave; prima di tutto una forma d'arte. Beh, la mia risposta &egrave; provate ad usare un programma di composizione 
	   (io personalmente mi trovo bene con <a href="https://musescore.org/it/download">Musescore</a>), inserite note a caso e ascoltate il risultato;
		probabilmente l'esito non sar&agrave; tra quelli attesi, inoltre se non avete nozioni sul <a href="MySite/temperamento.html">temperamento</a>,
		avrete dato per scontato che le note siano tutte e sole quelle fornite dal programma, ma soprattuto che abbiano quel determinato 
		suono, quando in realt&agrave; la tematica &egrave; piuttosto complessa: si pensi che l'attuale temperamento (detto temperamento equabile)
		si &egrave; iniziato a diffondere soltanto dalla seconda met&agrave; del XIX secolo con l'introduzione del diapason (1831).  
		Tutto questo dovrebbe iniziare a far riflettere sulle necessit&agrave; di definire un sistema musicale che definisca in maniera formale i vari concetti di base quali le note, gli intervalli tra di esse, le scale, gli accordi, e cos&igrave; via. Tutti questi aspetti sono trattati nei vari link sulla sinistra, fatene buon uso!	
		</p>
		</div>
		<div class="rightnav">
		<h3>Testi consigliati:</h3>
		<ul class="menu-dx">
			<li><h4>Teoria Musicale:</h4>
				<ul>
				<li><a href="https://www.amazon.it/Lezioni-teoria-musicale-Ist-magistrali/dp/889735324X">Poltronieri, Lezioni di Teoria Musicale</a></li>
				</ul>	
			</li>
			<li><h4>Solfeggio (livello base):</h4> 
				<ul>
					<li><a href="https://www.amazon.it/Esercizi-progressivi-solfeggi-cantati-secondaria/dp/8897353088">Poltronieri, Esercizi progressivi di solfeggi parlati e cantati vol.1</a></li> 
					<li><a href="https://www.amazon.it/Esercizi-progressivi-solfeggi-parlati-cantati/dp/8897353045">Poltronieri, Esercizi progressivi di solfeggi parlati e cantati vol.2</a></li>	  
				 </ul>	 
			</li>
			<li><h4>Solfeggio (livello avanzato):</h4> 
				<ul>	 
					<li><a href="https://www.amazon.it/Solfeggi-manoscritti-cantati-vocali-Dettati-melodici/dp/8863884641">Pedron, Solfeggi Manoscritti vol.1</a></li> 
					<li><a href="https://www.amazon.it/Solfeggi-manoscritti-cantati-vocali-Dettati-melodici/dp/886388465X">Pedron, Solfeggi Manoscritti vol.2</a></li> 
				 </ul>	 
			</li>
			<li><h4>Armonia:</h4>
				<ul>
					<li><a href="https://www.amazon.it/Armonia-Walter-Piston/dp/8870630498">Walter Piston, Armonia</a></li>
				</ul>
			</li>
			<li><h4>Esercizi di Armonia:</h4>
				<ul>
					<li><a href="https://www.amazon.it/Elementi-fondamentali-armonia-Gennaro-Napoli/dp/B00JC15VMK">Napoli J., Bassi per lo studio dell'armonia complementare</a></li>
				</ul>
			</li>
			<li><h4>Contrappunto e composizione:</h4>
				<ul>
					<li><a href="https://www.amazon.it/Contrappunto-composizione-Felix-Salzer/dp/8870631524">Salzer F., Schachter C., Contrappunto e Composizione</a></li>
				</ul>
			</li>
			<li><h4>Analisi Musicale:</h4>
				<ul>
					<li><a href="https://www.amazon.it/Analisi-musicale-Ian-Bent/dp/8870630730">Bent I., Drabkin W., Analisi Musicale</a></li>
				</ul>
			</li>
			<li><h4>Orchestrazione:</h4>
				<ul>
					<li><a href="https://www.amazon.it/Lo-studio-dellorchestrazione-Samuel-Adler/dp/8870637026">Lo Studio dell'Orchestrazione</a></li>
				</ul>
			</li>
			<li><h4>Storia della Musica:</h4>
				<ul class="ultimo-ul">
					<li><a href="https://www.amazon.it/Lineamenti-storia-della-musica-occidentale/dp/8876656413">lineamenti di storia della musica occidentale vol.1</a></li>
					<li><a href="https://www.amazon.it/Lineamenti-storia-della-musica-occidentale/dp/8876656359">lineamenti di storia della musica occidentale vol.2</a></li>
				</ul>
			</li>
		</ul>
		</div>
	</div>
</body>
</html>