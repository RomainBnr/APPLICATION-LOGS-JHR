<?php
class ConnexionController {
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    public function showLogin(?string $error = null): void {
        require __DIR__ . '/../views/login.php';
    }

    public function doLogin(): void {
        startSecureSession();

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $this->showLogin("Identifiants requis.");
            return;
        }

        // table = user (schéma existant)
        $stmt = $this->pdo->prepare("SELECT id, username, password, mdp FROM user WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $ok = false;
        if ($user) {
            if (!empty($user['password'])) {
                // colonne 'password' = hash bcrypt
                $ok = password_verify($password, $user['password']);
            } elseif (!empty($user['mdp'])) {
                // colonne 'mdp' = mot de passe en clair (legacy)
                $ok = hash_equals($user['mdp'], $password);
            }
        }

        if ($ok) {
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $username;
            redirect('index.php');
        } else {
            $this->showLogin("Identifiants invalides.");
        }
    }
}
