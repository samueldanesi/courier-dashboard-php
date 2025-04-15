<?php
session_start();
include 'config/database.php';

// Controllo accesso tecnico
if (!isset($_SESSION['user_id']) || $_SESSION['ruolo'] != 'tecnico') {
    header("Location: login.php");
    exit;
}

// Eliminazione ticket
if (isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM tickets WHERE id = ? AND stato = 'Risolto'");
    $stmt->execute([$delete_id]);
    header("Location: tecnico_dashboard.php");
    exit;
}

// Recupera tutti i ticket aperti
$stmt = $pdo->prepare("SELECT tickets.*, users.nome AS cliente_nome FROM tickets 
                       JOIN users ON tickets.cliente_id = users.id 
                       ORDER BY tickets.created_at DESC");
$stmt->execute();
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Tecnico - Genio Logistica</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body style="background-color: #f8f9fa;">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">🚚 Genio Logistica - Tecnico</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link btn btn-danger text-white px-3" href="logout.php">🚪 Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenuto della Dashboard -->
    <div class="container mt-5">
        <h2 class="text-center">Benvenuto, <?= $_SESSION['nome']; ?>! 🛠</h2>
        <p class="text-center">Qui puoi gestire i ticket dei clienti.</p>

        <h3 class="mt-4">📋 Ticket da Gestire</h3>
        <table class="table table-striped table-hover shadow mt-4">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Tracking</th>
                    <th>Titolo</th>
                    <th>Stato</th>
                    <th>Priorità</th>
                    <th>Data</th>
                    <th>Azione</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tickets as $ticket): ?>
                <tr>
                    <td><?= $ticket['id'] ?></td>
                    <td><?= htmlspecialchars($ticket['cliente_nome']) ?></td>
                    <?= htmlspecialchars($ticket['tracking_number'] ?? 'N/A') ?>
                    <td><?= htmlspecialchars($ticket['titolo']) ?></td>
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
                    <td>
                        <a href="gestisci_ticket.php?id=<?= $ticket['id'] ?>" class="btn btn-sm btn-outline-primary">🔍 Gestisci</a>
                        <?php if ($ticket['stato'] == 'Risolto'): ?>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="delete_id" value="<?= $ticket['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Sei sicuro di voler eliminare questo ticket?')">🗑 Elimina</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Nessun ticket disponibile -->
        <?php if (empty($tickets)): ?>
            <p class="text-center mt-4 text-muted">📭 Nessun ticket da gestire al momento.</p>
        <?php endif; ?>
    </div>

</body>
</html>
