<?php
require_once __DIR__.'/include/config.inc.php';
require_once __DIR__.'/include/fct.inc.php';

startSecureSession();
if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

// Filtres
$q = trim($_GET['q'] ?? '');
$level = trim($_GET['level'] ?? '');
$host = trim($_GET['host'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 50;

$where = [];
$params = [];

if ($q !== '') {
    $where[] = "message LIKE :q";
    $params[':q'] = "%$q%";
}
if ($level !== '') {
    $where[] = "level = :level";
    $params[':level'] = $level;
}
if ($host !== '') {
    $where[] = "host = :host";
    $params[':host'] = $host;
}

$sql = "SELECT * FROM logs";
if ($where) $sql .= " WHERE " . implode(" AND ", $where);
$sql .= " ORDER BY created_at DESC LIMIT :offset,:limit";

$stmt = $pdo->prepare($sql);
foreach ($params as $k => $v) $stmt->bindValue($k, $v);
$stmt->bindValue(':offset', ($page-1)*$perPage, PDO::PARAM_INT);
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->execute();
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Logs</title>
</head>
<body>
    <h2>Dashboard Logs</h2>
    <form method="get">
        <input type="text" name="q" placeholder="Recherche message" value="<?=htmlspecialchars($q)?>">
        <input type="text" name="host" placeholder="Host" value="<?=htmlspecialchars($host)?>">
        <select name="level">
            <option value="">--Niveau--</option>
            <option value="info">INFO</option>
            <option value="warning">WARNING</option>
            <option value="error">ERROR</option>
            <option value="critical">CRITICAL</option>
        </select>
        <button type="submit">Filtrer</button>
    </form>
    <table border="1">
        <tr><th>Date</th><th>Niveau</th><th>Host</th><th>Message</th></tr>
        <?php foreach ($logs as $log): ?>
            <tr>
                <td><?=$log['created_at']?></td>
                <td><?=$log['level']?></td>
                <td><?=$log['host']?></td>
                <td><?=$log['message']?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
