<?php
require_once __DIR__ . '/../models/LogsModel.php';

class LogsController {
    private LogsModel $model;
    public function __construct(PDO $pdo) { $this->model = new LogsModel($pdo); }

    public function dashboard(): void {
        $filters = [
            'q'      => trim(getParam('q','')),
            'level'  => trim(getParam('level','')),
            'host'   => trim(getParam('host','')),
            'source' => trim(getParam('source','')),
            'from'   => trim(getParam('from','')),
            'to'     => trim(getParam('to','')),
        ];
        $page    = max(1, (int)getParam('page', 1));
        $perPage = 50;

        $data = $this->model->search($filters, $page, $perPage);
        $rows  = $data['rows'];
        $total = $data['total'];
        $pages = max(1, (int)ceil($total / $perPage));

        require __DIR__ . '/../views/dashboard.php';
    }
}
