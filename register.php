<?php
include 'config/database.php'; // Connessione al database

// Controlla se il form è stato inviato
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $ruolo = $_POST['ruolo']; // Cliente o Tecnico

    // Hash della password per sicurezza
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    try {
        // Controllo se l'email è già registrata
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $error = "❌ L'email è già in uso!";
        } else {
            // Inserimento nel database
            $stmt = $pdo->prepare("INSERT INTO users (nome, email, password, ruolo) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nome, $email, $password_hash, $ruolo]);
            $success = "✅ Registrazione completata! Ora puoi <a href='login.php'>effettuare il login</a>.";
        }
    } catch (PDOException $e) {
        $error = "⚠ Errore nella registrazione: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrazione - Ticketing System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="d-flex justify-content-center align-items-center vh-100" style="background-color: #f8f9fa;">

    <div class="card shadow p-4" style="width: 400px;">
        <h2 class="text-center mb-4">📝 Registrazione</h2>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger text-center"><?= $error ?></div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <div class="alert alert-success text-center"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-bold">Nome</label>
                <input type="text" name="nome" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Ruolo</label>
                <select name="ruolo" class="form-select" required>
                    <option value="cliente">🎫 Cliente</option>
                    <option value="tecnico">🛠 Tecnico</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success w-100">📝 Registrati</button>
        </form>

        <p class="text-center mt-3">Hai già un account? <a href="login.php">Accedi</a></p>
    </div>

</body>
</html>