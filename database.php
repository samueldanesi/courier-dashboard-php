<?php
// Configurazione database
$host = "localhost";
$dbname = "ticketing_system";
$username = "root"; // Cambia se hai impostato una password
$password = ""; // Se hai una password MySQL, mettila qui

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Errore di connessione al database: " . $e->getMessage());
}
?>