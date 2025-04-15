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

// Recupera i dettagli del ticket
$stmt = $pdo->prepare("SELECT tickets.*, users.nome AS cliente_nome 
                       FROM tickets JOIN users ON tickets.cliente_id = users.id 
                       WHERE tickets.id = ?");
$stmt->execute([$ticket_id]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    die("❌ Ticket non trovato.");
}

// Gestione cambio stato
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nuovo_stato = $_POST['stato'];

    try {
        $stmt = $pdo->prepare("UPDATE tickets SET stato = ? WHERE id = ?");
        $stmt->execute([$nuovo_stato, $ticket_id]);
        $success = "✅ Stato del ticket aggiornato con successo!";
        $ticket['stato'] = $nuovo_stato; // Aggiorna la variabile per la visualizzazione
    } catch (PDOException $e) {
        die("⚠ Errore nell'aggiornamento dello stato: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Ticket</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body style="background-color: #f8f9fa;">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">🛠 Ticketing System - Logistica</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link btn btn-secondary text-white px-3" href="tecnico_dashboard.php">⬅ Torna alla Dashboard</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenuto della Pagina -->
    <div class="container mt-5">
        <div class="card shadow p-4">
            <h2 class="text-center">📦 Gestione Ticket #<?= $ticket['id'] ?></h2>

            <!-- Messaggio di successo -->
            <?php if (isset($success)): ?>
                <div class="alert alert-success text-center"><?= $success ?></div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-6">
                    <p><strong>👤 Cliente:</strong> <?= htmlspecialchars($ticket['cliente_nome']) ?></p>
                    <p><strong>📦 Numero di Tracking:</strong> 
  <?= !empty($ticket['tracking_number']) ? htmlspecialchars($ticket['tracking_number']) : "<span class='text-danger'>Non disponibile</span>"; ?>
</p>                    <p><strong>📌 Titolo:</strong> <?= htmlspecialchars($ticket['titolo']) ?></p>
                    <p><strong>📖 Descrizione:</strong> <?= htmlspecialchars($ticket['descrizione']) ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>🔥 Priorità:</strong> 
                        <span class="badge bg-<?= $ticket['priorita'] == 'Alta' ? 'danger' : ($ticket['priorita'] == 'Media' ? 'warning' : 'success') ?>">
                            <?= $ticket['priorita'] ?>
                        </span>
                    </p>
                    <p><strong>⏳ Stato Attuale:</strong> 
                        <span class="badge bg-<?= $ticket['stato'] == 'Aperto' ? 'warning' : ($ticket['stato'] == 'In Lavorazione' ? 'primary' : 'success') ?>">
                            <?= $ticket['stato'] ?>
                        </span>
                    </p>
                </div>
            </div>

            <!-- Form per aggiornare lo stato -->
            <form method="POST" class="mt-3">
                <label class="form-label fw-bold">🔄 Cambia Stato</label>
                <select name="stato" class="form-select">
                    <option value="Aperto" <?= $ticket['stato'] == 'Aperto' ? 'selected' : '' ?>>🟡 Aperto</option>
                    <option value="In Lavorazione" <?= $ticket['stato'] == 'In Lavorazione' ? 'selected' : '' ?>>🔵 In Lavorazione</option>
                    <option value="Risolto" <?= $ticket['stato'] == 'Risolto' ? 'selected' : '' ?>>🟢 Risolto</option>
                </select>
                <button type="submit" class="btn btn-success mt-3 w-100">✅ Aggiorna Stato</button>
            </form>
        </div>
    </div>

</body>
</html>