<?php
session_start();
include 'config/database.php';

// Controllo accesso tecnico
if (!isset($_SESSION['user_id']) || $_SESSION['ruolo'] != 'tecnico') {
    header("Location: login.php");
    exit;
}

// Controlla se è stato passato un ID valido
if (!isset($_GET['id'])) {
    header("Location: tecnico_dashboard.php");
    exit;
}

$ticket_id = $_GET['id'];

// Recupera il ticket
$stmt = $pdo->prepare("SELECT * FROM tickets WHERE id = ?");
$stmt->execute([$ticket_id]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

// Controlla se il ticket esiste e se è risolto
if (!$ticket) {
    die("❌ Ticket non trovato.");
}
if ($ticket['stato'] != 'Risolto') {
    die("⚠ Errore: Solo i ticket risolti possono essere eliminati.");
}

// Elimina il ticket
try {
    $stmt = $pdo->prepare("DELETE FROM tickets WHERE id = ?");
    $stmt->execute([$ticket_id]);
    header("Location: tecnico_dashboard.php?success=Ticket eliminato con successo");
    exit;
} catch (PDOException $e) {
    die("⚠ Errore nell'eliminazione del ticket: " . $e->getMessage());
}
?>