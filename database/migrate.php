<?php

declare(strict_types=1);

// Load .env from project root
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

$host    = $_ENV['DB_HOST']    ?? '127.0.0.1';
$port    = $_ENV['DB_PORT']    ?? '3306';
$name    = $_ENV['DB_NAME']    ?? 'blog';
$user    = $_ENV['DB_USER']    ?? 'root';
$pass    = $_ENV['DB_PASS']    ?? '';
$charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';

$pdo = new PDO(
    "mysql:host=$host;port=$port;dbname=$name;charset=$charset",
    $user,
    $pass,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
);

// Ensure tracking table exists
$pdo->exec('
    CREATE TABLE IF NOT EXISTS migrations (
        id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        filename   VARCHAR(255) NOT NULL UNIQUE,
        applied_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
');

// Fetch already applied migrations
$applied = $pdo->query('SELECT filename FROM migrations')
               ->fetchAll(PDO::FETCH_COLUMN);
$applied = array_flip($applied);

// Collect and sort .sql files (skip migrate.php itself)
$files = glob(__DIR__ . '/migrations/*.sql');
sort($files);

if (empty($files)) {
    echo "No migration files found.\n";
    exit(0);
}

$ran = 0;

foreach ($files as $filepath) {
    $filename = basename($filepath);

    if (isset($applied[$filename])) {
        echo "  skip  $filename\n";
        continue;
    }

    $sql = file_get_contents($filepath);

    try {
        // DDL (CREATE/ALTER) causes implicit commit in MySQL — no transaction wrapper
        foreach (array_filter(array_map('trim', explode(';', $sql))) as $statement) {
            $pdo->exec($statement);
        }

        $stmt = $pdo->prepare('INSERT INTO migrations (filename) VALUES (:filename)');
        $stmt->execute([':filename' => $filename]);

        echo "  apply $filename\n";
        $ran++;
    } catch (\Throwable $e) {
        echo "  ERROR in $filename: {$e->getMessage()}\n";
        exit(1);
    }
}

echo $ran > 0 ? "\nDone. Applied $ran migration(s).\n" : "\nAll migrations already applied.\n";
