<?php
session_start();
include 'config/database.php'; // Connessione al database

// Controlla se il form è stato inviato
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    try {
        // Controllo se l'email esiste nel database
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Login riuscito: Salva i dati dell'utente nella sessione
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nome'] = htmlspecialchars($user['nome']);
            $_SESSION['ruolo'] = $user['ruolo'];

            // Reindirizzamento alla dashboard in base al ruolo
            if ($user['ruolo'] == 'cliente') {
                header("Location: cliente_dashboard.php");
            } else {
                header("Location: tecnico_dashboard.php");
            }
            exit;
        } else {
            $error = "❌ Email o password errati!";
        }
    } catch (PDOException $e) {
        $error = "⚠ Errore nel login: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Ticketing System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="d-flex justify-content-center align-items-center vh-100" style="background-color: #f8f9fa;">

    <div class="card shadow p-4" style="width: 350px;">
        <h2 class="text-center mb-4">🔐 Login</h2>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger text-center"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-bold">Email</label>
                <input type="email" name="email" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">🔑 Accedi</button>
        </form>

        <p class="text-center mt-3">Non hai un account? <a href="register.php">Registrati</a></p>
    </div>

</body>
</html>