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
        if (!empty($filters['host'])) {
            $where[] = "host = :host";
            $params[':host'] = $filters['host'];
        }
        if (!empty($filters['source'])) {
            $where[] = "source = :source";
            $params[':source'] = $filters['source'];
        }
        if (!empty($filters['from'])) {
            $where[] = "created_at >= :from";
            $params[':from'] = $filters['from'];
        }
        if (!empty($filters['to'])) {
            $where[] = "created_at <= :to";
            $params[':to'] = $filters['to'];
        }

        $sql = "SELECT SQL_CALC_FOUND_ROWS id, level, source, host, message, created_at
                FROM logs";
        if ($where) $sql .= " WHERE ".implode(" AND ", $where);
        $sql .= " ORDER BY created_at DESC LIMIT :off,:lim";

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
