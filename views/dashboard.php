<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <title>Dashboard Logs</title>
  <style>
    body { font-family: system-ui, sans-serif; margin: 20px; }
    .topbar { display:flex; gap:12px; align-items:center; }
    input, select, button { padding:8px; }
    table { width:100%; border-collapse: collapse; margin-top: 16px; }
    th, td { border: 1px solid #eee; padding: 8px; text-align:left; }
    th { background:#fafafa; }
    .pagination { margin-top: 12px; display:flex; gap:8px; align-items:center; }
    .badge { padding:2px 6px; border-radius:6px; background:#eee; }
  </style>
</head>
<body>
  <div class="topbar">
    <form method="get" action="index.php">
      <input type="text" name="q" placeholder="Recherche message" value="<?=h($filters['q'])?>">
      <input type="text" name="host" placeholder="Hostname" value="<?=h($filters['host'])?>">
      <input type="text" name="source" placeholder="Application" value="<?=h($filters['source'])?>">
      <select name="level">
        <option value="">Niveau</option>
        <?php foreach (['INFO','WARNING','ERROR','CRITICAL'] as $lvl): ?>
          <option value="<?=$lvl?>" <?= strtoupper($filters['level']??'')===$lvl?'selected':''?>><?=$lvl?></option>
        <?php endforeach; ?>
      </select>
      <input type="datetime-local" name="from" value="<?=h($filters['from'])?>" />
      <input type="datetime-local" name="to"   value="<?=h($filters['to'])?>" />
      <button type="submit">Filtrer</button>
      <a href="export.php?<?=http_build_query(array_filter($filters))?>"><button type="button">Export CSV</button></a>
    </form>
    <div style="margin-left:auto">
      <span class="badge">Total : <?= (int)$total ?></span>
      <a href="logout.php"><button type="button">Déconnexion (<?=h($_SESSION['username'] ?? '')?>)</button></a>
    </div>
  </div>

  <table>
    <thead>
      <tr>
        <th>Timestamp</th><th>Niveau</th><th>Application</th><th>Hostname</th><th>Message</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td><?=h($r['timestamp'])?></td>
          <td><?=h(strtoupper($r['level']))?></td>
          <td><?=h($r['application'] ?? '')?></td>
          <td><?=h($r['hostname'] ?? '')?></td>
          <td><?=h($r['message'])?></td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$rows): ?>
        <tr><td colspan="5">Aucun résultat.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <div class="pagination">
    <?php if ($page>1): ?>
      <a href="?<?=http_build_query(array_merge($filters,['page'=>$page-1]))?>">&laquo; Préc.</a>
    <?php endif; ?>
    <span>Page <?=$page?> / <?=$pages?></span>
    <?php if ($page<$pages): ?>
      <a href="?<?=http_build_query(array_merge($filters,['page'=>$page+1]))?>">Suiv. &raquo;</a>
    <?php endif; ?>
  </div>
</body>
</html>
