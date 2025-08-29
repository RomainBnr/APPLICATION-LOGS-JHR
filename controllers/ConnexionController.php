<?php
class ConnexionController {
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    public function showLogin(?string $error = null): void {
        require __DIR__ . '/../views/login.php';
    }

    public function doLogin(): void {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $this->showLogin("Identifiants requis.");
            return;
        }

        $stmt = $this->pdo->prepare("SELECT id, username, password_hash FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            startSecureSession();
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            redirect('index.php');
        } else {
            $this->showLogin("Identifiants invalides.");
        }
    }
}
