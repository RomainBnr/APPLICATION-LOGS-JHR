<?php
require __DIR__.'/include/config.inc.php';
require __DIR__.'/include/fct.inc.php';
require __DIR__.'/include/auth.middleware.inc.php';
require __DIR__.'/models/LogsModel.php';

$model = new LogsModel($pdo);
$filters = [
    'q'      => trim(getParam('q','')),
    'level'  => trim(getParam('level','')),
    'host'   => trim(getParam('host','')),
    'source' => trim(getParam('source','')),
    'from'   => trim(getParam('from','')),
    'to'     => trim(getParam('to','')),
];
$data = $model->search($filters, 1, 100000);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=logs_export.csv');

$out = fopen('php://output', 'w');
fputcsv($out, ['id','timestamp','level','application','hostname','message']);
foreach ($data['rows'] as $r) {
    fputcsv($out, [$r['id'],$r['timestamp'],$r['level'],$r['application'],$r['hostname'],$r['message']]);
}
fclose($out);
