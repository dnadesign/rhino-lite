<?php

global $databaseConfig;

if (!isset($databaseConfig)) {
    $type = $_REQUEST['db']['type'] = defined('SS_DATABASE_CLASS') ? SS_DATABASE_CLASS : 'MySQLDatabase';
    $databaseConfig = [
        'type' => $type
    ];
}

require_once __DIR__ . '/../framework/tests/bootstrap.php';
