<?php

require_once "src/BackupFileHandler.php";
require_once "src/ConfigHandler.php";
require_once "src/Dumper.php";

$configHandler = new eDschungel\ConfigHandler();
$dbNames = $configHandler->getDbNames();

foreach ($dbNames as $dbName) {
    $configHandler->loadConfig($dbName);
    $backupFileHandler = new eDschungel\BackupFileHandler($configHandler, $dbName);
    $dumper = new eDschungel\Dumper($configHandler, $dbName);
    $backupFileName = $backupFileHandler->createCurrentBackupFileName();
    $dumper->dump();
    if ($dumper->wasSuccessful($backupFileName)) {
        print "ok";
    }
}
