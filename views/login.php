<?php
// views/login.php

// DEBUG (optionnel) : décommente si besoin
// ini_set('display_errors','1'); error_reporting(E_ALL);

require_once __DIR__ . '/../include/config.inc.php';
require_once __DIR__ . '/../include/fct.inc.php';

startSecureSession();

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    try {
        $stmt = $pdo->prepare("SELECT id, username, password, mdp FROM user WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        $error = "Erreur BDD : " . $e->getMessage();
        $user  = null;
    }

    $ok = false;
    if ($user) {
        $dbHash = $user['password'] ?? null;   // peut être NULL, clair, ou hash bcrypt/argon2
        $dbMdp  = $user['mdp'] ?? null;        // clair (héritage)

        // si 'password' ressemble à un hash bcrypt/argon2 → vérifier avec password_verify
        if (!empty($dbHash) && (preg_match('/^\$2y\$\d{2}\$/', $dbHash) || str_starts_with($dbHash, '$argon2'))) {
            $ok = password_verify($password, $dbHash);
        }
        // sinon si 'password' non vide (clair) → comparer en clair
        elseif (!empty($dbHash)) {
            $ok = hash_equals($dbHash, $password);
        }
        // sinon fallback sur 'mdp' (clair)
        elseif (!empty($dbMdp)) {
            $ok = hash_equals($dbMdp, $password);
        }
    }

    if ($ok) {
        $_SESSION['user_id']  = (int)$user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: ../index.php");
        exit;
    } else {
        $error = $error ?: "Identifiants invalides";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../style/style.css">
</head>
<body>
<div class="login-wrap">
  <div class="login-card">
    <h1>Connexion</h1>
    <p class="muted">Identifie-toi pour accéder au dashboard.</p>

    <?php if (!empty($error)): ?>
      <div class="err"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" action="">
      <label for="username">Utilisateur</label>
      <input type="text" id="username" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>

      <label for="password">Mot de passe</label>
      <input type="password" id="password" name="password" required>

      <button type="submit">Se connecter</button>
    </form>

    <div class="foot">Admin par défaut (si présent): <b>admin / admin123</b>.</div>
  </div>
</div>
</body>
</html>
