<?php

require_once "src/BackupFileHandler.php";
require_once "src/ConfigHandler.php";
require_once "src/Dumper.php";

$configHandler = new ConfigHandler();
$dbNames = $configHandler->getDbNames();

foreach ($dbNames as $dbName) {
    $backupFileHandler = new BackupFileHandler($config, $dbName);
    $dumper = new Dumper($config, $dbName);
    $backupFileName = $backupFileHandler->createCurrentBackupFileName();
    $dumper->dump();
    if ($dumper->wasSuccessful($backupFileName)) {
        print "ok";
    }
}
