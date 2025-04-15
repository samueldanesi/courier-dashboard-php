<?php
session_start();
include 'config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['ruolo'] != 'cliente') {
    header("Location: login.php");
    exit;
}

$cliente_id = $_SESSION['user_id'];

// Recupera i ticket del cliente
$stmt = $pdo->prepare("SELECT * FROM tickets WHERE cliente_id = ? ORDER BY created_at DESC");
$stmt->execute([$cliente_id]);
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Genio Logistica - Dashboard Cliente</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .navbar { background-color: #004085; }
        .navbar-brand { font-weight: bold; color: white !important; }
        .table thead { background-color: #004085; color: white; }
        .btn-primary { background-color: #004085; border-color: #004085; }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">🚛 Genio Logistica - Cliente</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link btn btn-light text-dark px-3" href="logout.php">🚪 Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenuto della Pagina -->
    <div class="container mt-5">
        <h2 class="text-center text-primary">📦 Benvenuto, <?= $_SESSION['nome']; ?>!</h2>
        <p class="text-center text-muted">Qui puoi controllare lo stato delle tue spedizioni.</p>

        <div class="d-flex justify-content-between mb-3">
            <a href="crea_ticket.php" class="btn btn-primary">➕ Apri un Nuovo Ticket</a>
        </div>

        <h3 class="mt-4">📋 Le Tue Spedizioni</h3>
        <table class="table table-striped table-hover shadow mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>📦 Codice Spedizione</th>
                    <th>Stato</th>
                    <th>Priorità</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tickets as $ticket): ?>
                <tr>
                    <td><?= $ticket['id'] ?></td>
                    <td><?= htmlspecialchars($ticket['titolo']) ?></td> <!-- Il titolo ora è il codice spedizione -->
                    <td>
                        <span class="badge bg-<?= $ticket['stato'] == 'Aperto' ? 'warning' : ($ticket['stato'] == 'In Lavorazione' ? 'primary' : 'success') ?>">
                            <?= $ticket['stato'] ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-<?= $ticket['priorita'] == 'Alta' ? 'danger' : ($ticket['priorita'] == 'Media' ? 'warning' : 'success') ?>">
                            <?= $ticket['priorita'] ?>
                        </span>
                    </td>
                    <td><?= date("d/m/Y H:i", strtotime($ticket['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (empty($tickets)): ?>
            <p class="text-center mt-4 text-muted">📭 Nessuna spedizione in corso.</p>
        <?php endif; ?>
    </div>

</body>
</html>