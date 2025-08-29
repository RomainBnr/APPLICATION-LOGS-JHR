<?php
require_once __DIR__ . '/include/config.inc.php';
require_once __DIR__ . '/include/fct.inc.php';

startSecureSession();
if (!isLoggedIn()) {
    header("Location: views/login.php");
    exit;
}

// --------------------
// Filtres GET
// --------------------
$q       = trim($_GET['q'] ?? '');
$level   = trim($_GET['level'] ?? '');
$host    = trim($_GET['host'] ?? ''); // correspond à la colonne 'hostname'
$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 50;

$where  = [];
$params = [];

if ($q !== '') {
    $where[] = "message LIKE :q";
    $params[':q'] = "%$q%";
}
if ($level !== '') {
    $where[] = "level = :level";
    $params[':level'] = strtoupper($level);
}
if ($host !== '') {
    $where[] = "hostname = :host";
    $params[':host'] = $host;
}

// --------------------
// Export CSV si demandé
// --------------------
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    $sqlExport = "SELECT timestamp, level, application, hostname, message FROM log";
    if ($where) {
        $sqlExport .= " WHERE " . implode(" AND ", $where);
    }
    $sqlExport .= " ORDER BY timestamp DESC"; // pas de LIMIT sur l’export

    $stmtExp = $pdo->prepare($sqlExport);
    foreach ($params as $k => $v) {
        $stmtExp->bindValue($k, $v);
    }
    $stmtExp->execute();

    // En-têtes HTTP pour un téléchargement CSV UTF-8
    $filename = "logs_" . date('Ymd_His') . ".csv";
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    // (Optionnel) BOM pour Excel
    echo "\xEF\xBB\xBF";

    $out = fopen('php://output', 'w');
    // En-têtes de colonnes
    fputcsv($out, ['timestamp','level','application','hostname','message']);
    // Lignes
    while ($row = $stmtExp->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($out, [
            $row['timestamp'],
            $row['level'],
            $row['application'],
            $row['hostname'],
            $row['message']
        ]);
    }
    fclose($out);
    exit;
}

// --------------------
// Requête paginée (affichage)
// --------------------
$sql = "SELECT id, timestamp, level, application, hostname, message FROM log";
if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY timestamp DESC LIMIT :offset, :limit";

$stmt = $pdo->prepare($sql);
foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
}
$stmt->bindValue(':offset', ($page - 1) * $perPage, PDO::PARAM_INT);
$stmt->bindValue(':limit',  $perPage, PDO::PARAM_INT);
$stmt->execute();
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Logs</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style/style.css">
</head>
<body class="dashboard-body">
    <div class="header">
        <h1>Dashboard Logs</h1>
        <a href="logout.php" class="logout">Déconnexion</a>
    </div>

    <div class="filter-form">
        <form method="get" style="display:contents">
            <div class="form-group">
                <label for="q">Recherche message</label>
                <input type="text" id="q" name="q" placeholder="Rechercher dans les messages..." value="<?= h($q) ?>">
            </div>
            <div class="form-group">
                <label for="host">Hostname</label>
                <input type="text" id="host" name="host" placeholder="Nom d'hôte..." value="<?= h($host) ?>">
            </div>
            <div class="form-group">
                <label for="level">Niveau</label>
                <select id="level" name="level">
                    <option value="">Tous les niveaux</option>
                    <?php foreach (['INFO','WARNING','ERROR','CRITICAL'] as $lvl): ?>
                        <option value="<?= $lvl ?>" <?= (strtoupper($level) === $lvl ? 'selected' : '') ?>><?= $lvl ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <button type="submit">Filtrer</button>
            </div>

            <!-- Bouton Export CSV (conserve les filtres) -->
            <div class="form-group">
                <button type="submit" name="export" value="csv">Exporter CSV</button>
            </div>
        </form>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>Niveau</th>
                    <th>Application</th>
                    <th>Hostname</th>
                    <th>Message</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td class="timestamp"><?= h($log['timestamp']) ?></td>
                        <td>
                            <span class="level level-<?= strtolower(h($log['level'])) ?>">
                                <?= h($log['level']) ?>
                            </span>
                        </td>
                        <td><?= h($log['application']) ?></td>
                        <td><?= h($log['hostname']) ?></td>
                        <td><?= h($log['message']) ?></td>
                    </tr>
                <?php endforeach; ?>

                <?php if (!$logs): ?>
                    <tr>
                        <td colspan="5" class="no-results">
                            Aucun log trouvé avec les critères sélectionnés.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
