<?php
// works for windows authentication no password or use needed 
define('DB_SERVER', 'localhost');
define('DB_NAME',   'IvorPaineHospital');

function getConn(): mixed {
    static $conn = null;
    if ($conn !== null) return $conn;

    $conn = sqlsrv_connect(DB_SERVER, [
        "Database"               => DB_NAME,
        "TrustServerCertificate" => true,
        "Encrypt"                => false,
        "CharacterSet"           => "UTF-8",
        "ReturnDatesAsStrings"   => true,
    ]);

    if ($conn === false) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'error'  => 'SQL Server connection failed.',
            'detail' => sqlsrv_errors(),
        ]);
        exit;
    }

    return $conn;
}


function dbQuery(string $sql, array $params = []): array {
    $conn = getConn();
    $stmt = $params
        ? sqlsrv_query($conn, $sql, $params)
        : sqlsrv_query($conn, $sql);

    if ($stmt === false) {
        jsonError('Query failed: ' . formatSqlsrvError(), $sql);
    }

    $rows = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $rows[] = $row;
    }
    sqlsrv_free_stmt($stmt);
    return $rows;
}


function dbExec(string $sql, array $params = []): int {
    $conn = getConn();
    $stmt = $params
        ? sqlsrv_query($conn, $sql, $params)
        : sqlsrv_query($conn, $sql);

    if ($stmt === false) {
        jsonError('Execute failed: ' . formatSqlsrvError(), $sql);
    }

    $affected = sqlsrv_rows_affected($stmt);
    sqlsrv_free_stmt($stmt);
    return $affected;
}


function dbScalar(string $sql, array $params = []): mixed {
    $rows = dbQuery($sql, $params);
    if (empty($rows)) return null;
    return reset($rows[0]);
}

function formatSqlsrvError(): string {
    $errs = sqlsrv_errors();
    if (!$errs) return 'Unknown error';
    return implode(' | ', array_map(fn($e) => "[{$e['code']}] {$e['message']}", $errs));
}

function jsonError(string $msg, string $sql = ''): never {
    http_response_code(500);
    header('Content-Type: application/json');
    $out = ['error' => $msg];
    if ($sql) $out['sql'] = $sql;
    echo json_encode($out);
    exit;
}

function jsonResponse(mixed $data, int $code = 200): never {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
?>
