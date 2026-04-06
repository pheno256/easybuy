<?php
/**
 * EasyBuy Uganda - Database Migration Runner
 * Run migrations in order
 */

// Database configuration
$host = 'localhost';
$dbname = 'easybuy_db';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
    $pdo->exec("USE `$dbname`");
    
    echo "Connected to database successfully.\n\n";
    
    // Migration table to track which migrations have been run
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS migrations (
            id INT PRIMARY KEY AUTO_INCREMENT,
            migration VARCHAR(255) NOT NULL,
            batch INT NOT NULL,
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_migration (migration)
        )
    ");
    
    // Get already run migrations
    $stmt = $pdo->query("SELECT migration FROM migrations");
    $completed = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // List of migrations in order
    $migrations = [
        'v1.0.0.sql',
        'v1.1.0.sql',
        'v1.2.0.sql',
        'v1.3.0.sql'
    ];
    
    $batch = date('YmdHis');
    $count = 0;
    
    foreach ($migrations as $migration) {
        if (!in_array($migration, $completed)) {
            echo "Running migration: $migration ... ";
            
            $sql = file_get_contents(__DIR__ . '/' . $migration);
            
            // Split SQL by semicolon (basic approach)
            $queries = array_filter(array_map('trim', explode(';', $sql)));
            
            try {
                $pdo->beginTransaction();
                
                foreach ($queries as $query) {
                    if (!empty($query)) {
                        $pdo->exec($query);
                    }
                }
                
                // Record migration
                $stmt = $pdo->prepare("INSERT INTO migrations (migration, batch) VALUES (?, ?)");
                $stmt->execute([$migration, $batch]);
                
                $pdo->commit();
                echo "SUCCESS\n";
                $count++;
            } catch (PDOException $e) {
                $pdo->rollBack();
                echo "FAILED\n";
                echo "Error: " . $e->getMessage() . "\n";
                exit(1);
            }
        } else {
            echo "Skipping $migration (already run)\n";
        }
    }
    
    echo "\n============================================\n";
    echo "Migration completed!\n";
    echo "Ran $count new migration(s)\n";
    echo "============================================\n";
    
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage() . "\n");
}