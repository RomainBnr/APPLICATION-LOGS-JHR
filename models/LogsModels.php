<?php
class LogsModel {
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    public function search(array $filters, int $page, int $perPage): array {
        $where = [];
        $params = [];

        if (!empty($filters['q'])) {
            $where[] = "(message LIKE :q)";
            $params[':q'] = "%{$filters['q']}%";
        }
        if (!empty($filters['level'])) {
            $where[] = "level = :level";
            $params[':level'] = $filters['level'];
        }
        if (!empty($filters['host'])) {                 // UI "host" -> colonne hostname
            $where[] = "hostname = :host";
            $params[':host'] = $filters['host'];
        }
        if (!empty($filters['source'])) {               // UI "source" -> colonne application
            $where[] = "application = :source";
            $params[':source'] = $filters['source'];
        }
        if (!empty($filters['from'])) {
            $where[] = "timestamp >= :from";
            $params[':from'] = $filters['from'];
        }
        if (!empty($filters['to'])) {
            $where[] = "timestamp <= :to";
            $params[':to'] = $filters['to'];
        }

        $sql = "SELECT SQL_CALC_FOUND_ROWS id, timestamp, level, application, hostname, message
                FROM log";
        if ($where) $sql .= " WHERE ".implode(" AND ", $where);
        $sql .= " ORDER BY timestamp DESC LIMIT :off,:lim";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $k=>$v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':off', ($page-1)*$perPage, PDO::PARAM_INT);
        $stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        $total = (int)$this->pdo->query("SELECT FOUND_ROWS() AS t")->fetch()['t'];
        return ['rows'=>$rows, 'total'=>$total];
    }
}
