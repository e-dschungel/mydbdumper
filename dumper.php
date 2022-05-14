<?php

require_once "src/BackupFileHandler.php";
require_once "src/ConfigHandler.php";
require_once "src/Dumper.php";
require_once "src/Mailer.php";
require_once dirname(__FILE__) . '/vendor/autoload.php';

$configHandler = new eDschungel\ConfigHandler();
$dbNames = $configHandler->getDbNames();

$noSuccessfulDumps = 0;
foreach ($dbNames as $dbName) {
    $configHandler->loadConfig($dbName);
    $backupFileHandler = new eDschungel\BackupFileHandler($configHandler, $dbName);
    $dumper = new eDschungel\Dumper($configHandler, $dbName);
    $mailer = new eDschungel\Mailer($configHandler);
    $backupFileName = $backupFileHandler->createCurrentBackupFileName();
    if ($dumper->dump($backupFileName)) {
        $noSuccessfulDumps++;
        $mailer->sendMail($dbName, $backupFileName);
        $backupFileHandler->rotateBackups();
    }
}
if ($noSuccessfulDumps === $configHandler->getNrDBs()) {
        print "All databases dumped successfully.\n";
} else {
    print "Only $noSuccessfulDumps databases out of $configHandler->getNrDBs() were dumped successfully.";
}
