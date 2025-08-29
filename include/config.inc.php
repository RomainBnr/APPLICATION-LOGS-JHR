<?php
// Connexion MariaDB/MySQL
// IMPORTANT: on utilise la base déjà existante: projetphp
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'projetphp');
define('DB_USER', 'jorge');   // conforme à ce qu'on avait utilisé
define('DB_PASS', 'jhr');       // tu m'as dit: "le mot de passe doit être jhr"

try {
    $pdo = new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (Throwable $e) {
    http_response_code(500);
    die("Erreur BDD");
}
