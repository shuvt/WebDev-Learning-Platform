<?php
// includes/firebird_db.php

function getFirebirdConnection() {
    static $firebird_db = null;
    
    if ($firebird_db === null) {
        try {
            // Проверяем константы
            if (!defined('FIREBIRD_DB_PATH') || !defined('FIREBIRD_USER') || !defined('FIREBIRD_PASS')) {
                throw new Exception('Константы Firebird не определены в config.php');
            }
            
            $database = FIREBIRD_DB_PATH;
            $username = FIREBIRD_USER;
            $password = FIREBIRD_PASS;
            
            // Проверяем что файл базы существует
            if (!file_exists($database)) {
                throw new Exception("Файл базы данных не найден: " . $database);
            }
            
            // Подключаемся через localhost как в вашем рабочем примере
            $dsn = "firebird:dbname=localhost:{$database}";
            $firebird_db = new PDO($dsn, $username, $password);
            $firebird_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
        } catch (PDOException $e) {
            throw new Exception("Ошибка подключения к Firebird: " . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception("Ошибка: " . $e->getMessage());
        }
    }
    
    return $firebird_db;
}

// Функция для выполнения SQL запросов в Firebird
function executeFirebirdQuery($sql) {
    $db = getFirebirdConnection();
    try {
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        throw new Exception($e->getMessage());
    }
}

// Функция для получения списка таблиц
function getFirebirdTables() {
    $db = getFirebirdConnection();
    try {
        $tables = $db->query("
            SELECT RDB\$RELATION_NAME 
            FROM RDB\$RELATIONS 
            WHERE RDB\$SYSTEM_FLAG = 0 
            ORDER BY RDB\$RELATION_NAME
        ")->fetchAll();
        
        $table_names = [];
        foreach ($tables as $table) {
            $table_names[] = trim($table['RDB$RELATION_NAME']);
        }
        
        return $table_names;
    } catch (Exception $e) {
        throw new Exception($e->getMessage());
    }
}

// Функция для проверки подключения
function testFirebirdConnection() {
    try {
        $db = getFirebirdConnection();
        $result = $db->query("SELECT FIRST 1 * FROM RDB\$DATABASE");
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>