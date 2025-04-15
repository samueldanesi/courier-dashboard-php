<?php
session_start();
include 'config/database.php';

// Verifica se l'utente è loggato come cliente
if (!isset($_SESSION['user_id']) || $_SESSION['ruolo'] != 'cliente') {
    header("Location: login.php");
    exit;
}

// Controlla se il form è stato inviato
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tracking_number = trim($_POST['tracking_number']); // Recupera il codice spedizione
    $descrizione = trim($_POST['descrizione']);
    $priorita = $_POST['priorita'];
    $cliente_id = $_SESSION['user_id']; // ID del cliente loggato

    try {
        // Inserisci il ticket nel database con il codice spedizione
        $stmt = $pdo->prepare("INSERT INTO tickets (cliente_id, titolo, descrizione, priorita, tracking_number) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$cliente_id, $tracking_number, $descrizione, $priorita, $tracking_number]);
        $success = "✅ Ticket creato con successo!";
    } catch (PDOException $e) {
        $error = "⚠ Errore nella creazione del ticket: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crea Ticket</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body style="background-color: #f8f9fa;">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">🎫 Ticketing System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link btn btn-secondary text-white px-3" href="cliente_dashboard.php">⬅ Torna alla Dashboard</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenuto della Pagina -->
    <div class="container mt-5">
        <div class="card shadow p-4" style="max-width: 500px; margin: auto;">
            <h2 class="text-center">📦 Apri un Nuovo Ticket</h2>

            <!-- Messaggi di successo o errore -->
            <?php if (isset($success)): ?>
                <div class="alert alert-success text-center"><?= $success ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger text-center"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold">Inserisci Codice Spedizione</label>
                    <input type="text" name="tracking_number" class="form-control" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Descrizione</label>
                    <textarea name="descrizione" class="form-control" rows="4" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Priorità</label>
                    <select name="priorita" class="form-select" required>
                        <option value="Bassa">🟢 Bassa</option>
                        <option value="Media">🟡 Media</option>
                        <option value="Alta">🔴 Alta</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100">📨 Invia Ticket</button>
            </form>
        </div>
    </div>

</body>
</html>