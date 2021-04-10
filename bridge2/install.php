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
echo '<link rel="shortcut icon" type="image/svg" href="../MySite/icon.svg">'; /* per visualizzare la fav icon durante l'uso delle form */

// Database
$database = "CREATE DATABASE if not exists $db_name;";
if ($check = mysqli_query($mysqli_connection, $database)) {
	echo "Database creato<br />";
}
else echo "Database non creato<br />";

// Tabella utenti
$utenti = "CREATE TABLE if not exists $tab_utenti(id int UNIQUE NOT NULL auto_increment, PRIMARY KEY(id), ";
$utenti .= "email varchar(80) UNIQUE NOT NULL, ";
$utenti .= "secondary_email varchar(80) NOT NULL, ";
$utenti .= "username varchar(9) UNIQUE NOT NULL, ";
$utenti .= "password varchar(255) NOT NULL, ";
$utenti .= "address varchar(80) NOT NULL, ";
$utenti .= "cap char(5) NOT NULL, ";
$utenti .= "birthday date NOT NULL, ";
$utenti .= "status varchar(15) DEFAULT 'attivo' NOT NULL, ";
$utenti .= "admin bit DEFAULT 0 NOT NULL) ";
$utenti .= "DEFAULT CHARACTER SET latin1 COLLATE latin1_general_cs;";

if ($check = mysqli_query($mysqli_connection, $utenti)) {
	echo "Tabella utenti creata<br />";
}
else echo "Tabella utenti non creata<br />";

// Inserisci i dati dell'admin
$utenti = "INSERT INTO $tab_utenti(id, email, secondary_email, username, password, address, cap, birthday, status, admin) VALUES('1', '', '', '', '".password_hash('', PASSWORD_DEFAULT)."', '', '', '', '', 1), ";
$utenti .= "('2', 'emanuele@email.it', 'emanuele@emailsec.it', 'emanuele', '".password_hash('emanuele', PASSWORD_DEFAULT)."', 'Via XXIV Maggio 7', '04100', '1998-03-24', 'attivo', 0), ";
$utenti .= "('3', 'cacioppo@email.it', 'cacioppo@emailsec.it', 'cacioppo', '".password_hash('cacioppo', PASSWORD_DEFAULT)."', 'Via XXIV Maggio 7', '04100', '1998-05-13', 'attivo', 0), ";
$utenti .= "('4', 'christian@email.it', 'christian@emailsec.it', 'christian', '".password_hash('christian', PASSWORD_DEFAULT)."', 'Via XXIV Maggio 7', '04100', '1998-08-06', 'attivo', 0), ";
$utenti .= "('5', 'jake@email.it', 'jake@emailsec.it', 'jake', '".password_hash('jake', PASSWORD_DEFAULT)."', 'Via XXIV Maggio 7', '04100', '1998-02-21', 'attivo', 0), ";
$utenti .= "('6', 'matteo@email.it', 'matteo@emailsec.it', 'matteo', '".password_hash('matteo', PASSWORD_DEFAULT)."', 'Via XXIV Maggio 7', '04100', '1998-10-27', 'attivo', 0), ";
$utenti .= "('7', 'riccardo@email.it', 'riccardo@emailsec.it', 'riccardo', '".password_hash('riccardo', PASSWORD_DEFAULT)."', 'Via XXIV Maggio 7', '04100', '1998-04-04', 'cancellato', 0), ";
$utenti .= "('8', 'gianluca@email.it', 'gianluca@emailsec.it', 'gianluca', '".password_hash('gianluca', PASSWORD_DEFAULT)."', 'Via XXIV Maggio 7', '04100', '1998-06-28', 'sospeso', 0), ";
$utenti .= "('9', 'patrizio@email.it', 'patrizio@emailsec.it', 'patrizio', '".password_hash('patrizio', PASSWORD_DEFAULT)."', 'Via XXIV Maggio 7', '04100', '1998-11-07', 'attivo', 0), ";
$utenti .= "('10', 'alessio@email.it', 'alessio@emailsec.it', 'alessio', '".password_hash('alessio', PASSWORD_DEFAULT)."', 'Via XXIV Maggio 7', '04100', '1998-01-10', 'cancellato', 0), ";
$utenti .= "('11', 'giulia@email.it', 'giulia@emailsec.it', 'giulia', '".password_hash('giulia', PASSWORD_DEFAULT)."', 'Via XXIV Maggio 7', '04100', '1998-09-30', 'sospeso', 0), ";
$utenti .= "('12', 'michela@email.it', 'michela@emailsec.it', 'michela', '".password_hash('michela', PASSWORD_DEFAULT)."', 'Via XXIV Maggio 7', '04100', '1998-02-17', 'sospeso', 0);";

if ($check = mysqli_query($mysqli_connection, $utenti)) {
	echo "Dati utenti inseriti<br />";
}
else echo "Dati utenti non inseriti<br />";

// Tabella livelli
$livelli = "CREATE TABLE if not exists $tab_livelli(id int UNIQUE NOT NULL, PRIMARY KEY(id));";
if ($check = mysqli_query($mysqli_connection, $livelli)) {
	echo "Tabella livelli creata<br />";
}
else echo "Tabella livelli non creata<br />";

$livelli = "INSERT INTO $tab_livelli VALUES(1), ";
$livelli .= "(2), ";
$livelli .= "(3), ";
$livelli .= "(4), ";
$livelli .= "(5), ";
$livelli .= "(6), ";
$livelli .= "(7), ";
$livelli .= "(8), ";
$livelli .= "(9), ";
$livelli .= "(10), ";
$livelli .= "(11), ";
$livelli .= "(12), ";
$livelli .= "(13), ";
$livelli .= "(14), ";
$livelli .= "(15), ";
$livelli .= "(16), ";
$livelli .= "(17), ";
$livelli .= "(18), ";
$livelli .= "(19), ";
$livelli .= "(20), ";
$livelli .= "(21), ";
$livelli .= "(22), ";
$livelli .= "(23), ";
$livelli .= "(24), ";
$livelli .= "(25), ";
$livelli .= "(26), ";
$livelli .= "(27), ";
$livelli .= "(28), ";
$livelli .= "(29), ";
$livelli .= "(30), ";
$livelli .= "(31), ";
$livelli .= "(32), ";
$livelli .= "(33), ";
$livelli .= "(34), ";
$livelli .= "(35), ";
$livelli .= "(36), ";
$livelli .= "(37), ";
$livelli .= "(38), ";
$livelli .= "(39), ";
$livelli .= "(40), ";
$livelli .= "(41), ";
$livelli .= "(42);";

if ($check = mysqli_query($mysqli_connection, $livelli)) {
	echo "Dati livelli inseriti<br />";
}
else echo "Dati livelli non inseriti<br />";

// Tabella note
$note = "CREATE TABLE if not exists $tab_note(id int UNIQUE NOT NULL, PRIMARY KEY(id), ";
$note .= "nota varchar(10) NOT NULL, ";
$note .= "nota_completa varchar(10) UNIQUE NOT NULL, ";
$note .= "suono varchar(20) UNIQUE NOT NULL);";

if ($check = mysqli_query($mysqli_connection, $note)) {
	echo "Tabella note creata<br />";
}
else echo "Tabella note non creata<br />";

$note = "INSERT INTO $tab_note(id, nota, nota_completa, suono) VALUES('1', 'Sol', 'Sol1', 'Suoni/Sol1.mp3'), ";
$note .= "('2', 'Sol#', 'Sol#1', 'Suoni/Soldie1.mp3'), ";
$note .= "('3', 'La', 'La1', 'Suoni/La1.mp3'), ";
$note .= "('4', 'La#', 'La#1', 'Suoni/Ladie1.mp3'), ";
$note .= "('5', 'Si', 'Si1', 'Suoni/Si1.mp3'), ";

$note .= "('6', 'Do', 'Do2', 'Suoni/Do2.mp3'), ";
$note .= "('7', 'Do#', 'Do#2', 'Suoni/Dodie2.mp3'), ";
$note .= "('8', 'Re', 'Re2', 'Suoni/Re2.mp3'), ";
$note .= "('9', 'Re#', 'Re#2', 'Suoni/Redie2.mp3'), ";
$note .= "('10', 'Mi', 'Mi2', 'Suoni/Mi2.mp3'), ";
$note .= "('11', 'Fa', 'Fa2', 'Suoni/Fa2.mp3'), ";
$note .= "('12', 'Fa#', 'Fa#2', 'Suoni/Fadie2.mp3'), ";
$note .= "('13', 'Sol', 'Sol2', 'Suoni/Sol2.mp3'), ";
$note .= "('14', 'Sol#', 'Sol#2', 'Suoni/Soldie2.mp3'), ";
$note .= "('15', 'La', 'La2', 'Suoni/La2.mp3'), ";
$note .= "('16', 'La#', 'La#2', 'Suoni/Ladie2.mp3'), ";
$note .= "('17', 'Si', 'Si2', 'Suoni/Si2.mp3'), ";

$note .= "('18', 'Do', 'Do3', 'Suoni/Do3.mp3'), ";
$note .= "('19', 'Do#', 'Do#3', 'Suoni/Dodie3.mp3'), ";
$note .= "('20', 'Re', 'Re3', 'Suoni/Re3.mp3'), ";
$note .= "('21', 'Re#', 'Re#3', 'Suoni/Redie3.mp3'), ";
$note .= "('22', 'Mi', 'Mi3', 'Suoni/Mi3.mp3'), ";
$note .= "('23', 'Fa', 'Fa3', 'Suoni/Fa3.mp3'), ";
$note .= "('24', 'Fa#', 'Fa#3', 'Suoni/Fadie3.mp3'), ";
$note .= "('25', 'Sol', 'Sol3', 'Suoni/Sol3.mp3'), ";
$note .= "('26', 'Sol#', 'Sol#3', 'Suoni/Soldie3.mp3'), ";
$note .= "('27', 'La', 'La3', 'Suoni/La3.mp3'), ";
$note .= "('28', 'La#', 'La#3', 'Suoni/Ladie3.mp3'), ";
$note .= "('29', 'Si', 'Si3', 'Suoni/Si3.mp3'), ";

$note .= "('30', 'Do', 'Do4', 'Suoni/Do4.mp3'), ";
$note .= "('31', 'Do#', 'Do#4', 'Suoni/Dodie4.mp3'), ";
$note .= "('32', 'Re', 'Re4', 'Suoni/Re4.mp3'), ";
$note .= "('33', 'Re#', 'Re#4', 'Suoni/Redie4.mp3'), ";
$note .= "('34', 'Mi', 'Mi4', 'Suoni/Mi4.mp3'), ";
$note .= "('35', 'Fa', 'Fa4', 'Suoni/Fa4.mp3'), ";
$note .= "('36', 'Fa#', 'Fa#4', 'Suoni/Fadie4.mp3'), ";
$note .= "('37', 'Sol', 'Sol4', 'Suoni/Sol4.mp3'), ";
$note .= "('38', 'Sol#', 'Sol#4', 'Suoni/Soldie4.mp3'), ";
$note .= "('39', 'La', 'La4', 'Suoni/La4.mp3'), ";
$note .= "('40', 'La#', 'La#4', 'Suoni/Ladie4.mp3'), ";
$note .= "('41', 'Si', 'Si4', 'Suoni/Si4.mp3'), ";
$note .= "('42', 'Do', 'Do5', 'Suoni/Do5.mp3');";

if ($check = mysqli_query($mysqli_connection, $note)) {
	echo "Dati note inseriti<br />";
}
else echo "Dati note non inseriti<br />";

$xmlString = "";
foreach (file("../bridge3/XML_popolati/risultati_allenamento.xml") as $node) $xmlString .= trim($node);
$doc = new DOMDocument();
$doc->loadXML($xmlString);

$doc->formatOutput = true;
if ($doc->save("../bridge3/XML/risultati_allenamento.xml")) echo "Punteggi utenti modalit&agrave; allenamento inseriti<br />"; 
else echo "Punteggi utenti modalit&agrave; allenamento non inseriti<br />";

$xmlString_comp = "";
foreach (file("../bridge3/XML_popolati/risultati_competitiva.xml") as $node) $xmlString_comp .= trim($node);
$doc_comp = new DOMDocument();
$doc_comp->loadXML($xmlString_comp);

$doc_comp->formatOutput = true;
if ($doc_comp->save("../bridge3/XML/risultati_competitiva.xml")) echo "Punteggi utenti modalit&agrave; competitiva inseriti";
else echo "Punteggi utenti modalit&agrave; allenamento non inseriti";

?>