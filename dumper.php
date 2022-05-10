<?php

require_once "src/BackupFileHandler.php";
require_once "src/ConfigHandler.php";
require_once "src/Dumper.php";

$configHandler = new eDschungel\ConfigHandler();
$dbNames = $configHandler->getDbNames();

$noSuccessfulDumps = 0;
foreach ($dbNames as $dbName) {
    $configHandler->loadConfig($dbName);
    $backupFileHandler = new eDschungel\BackupFileHandler($configHandler, $dbName);
    $dumper = new eDschungel\Dumper($configHandler, $dbName);
    $backupFileName = $backupFileHandler->createCurrentBackupFileName();
    if ($dumper->dump($backupFileName)) {
        $noSuccessfulDumps++;
    }
}
if ($noSuccessfulDumps === $configHandler->getNrDBs()){
        print "All databases dumped successfully.\n";
    }
    else{
        print "Only $noSuccessfulDumps databases out of $configHandler->getNrDBs() were dumped successfully.";
    }
