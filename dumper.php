<?php

require_once "src/BackupFileHandler.php";
require_once "src/ConfigHandler.php";
require_once "src/Dumper.php";

$configHandler = new eDschungel\ConfigHandler();
$dbNames = $configHandler->getDbNames();

foreach ($dbNames as $dbName) {
    $backupFileHandler = new eDschungel\BackupFileHandler($config, $dbName);
    $dumper = new eDschungel\Dumper($config, $dbName);
    $backupFileName = $backupFileHandler->createCurrentBackupFileName();
    $dumper->dump();
    if ($dumper->wasSuccessful($backupFileName)) {
        print "ok";
    }
}
