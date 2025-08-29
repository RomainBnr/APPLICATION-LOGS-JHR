<?php
function startSecureSession(): void {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_set_cookie_params(['lifetime'=>0,'httponly'=>true,'samesite'=>'Lax']);
        session_start();
    }
}
function isLoggedIn(): bool { return !empty($_SESSION['user_id']); }
function redirect(string $path): void { header("Location: $path"); exit; }
function getParam(string $key, $default=null) { return $_REQUEST[$key] ?? $default; }
function h(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
