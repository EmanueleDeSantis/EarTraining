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
// Dati relativi al database e alle tabelle da usare in uno script che includa questo file

// Esecuzione del tentativo di connessione
$host = 'localhost';
$username = ''; // Inserisci l'username per accedere al database
$password = ''; // Inserisci la password per accedere al database
$db_name = ''; // Inserisci il nome del database
$tab_utenti = 'utenti';
$tab_livelli = 'livelli';
$tab_note = 'note';
$mysqli_connection = new mysqli($host, $username, $password, $db_name);

// Controllo della connessione
if (mysqli_connect_errno($mysqli_connection)) {
	printf("Problema con la connessione %s\n", mysqli_connect_errno($mysqli_connection));
	exit();
}

?>	

	