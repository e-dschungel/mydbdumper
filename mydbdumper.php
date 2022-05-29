<?php

header('Content-Type: text/plain');

require_once "src/BackupFileHandler.php";
require_once "src/ConfigHandler.php";
require_once "src/Dumper.php";
require_once "src/Mailer.php";
require_once dirname(__FILE__) . '/vendor/autoload.php';

$configHandler = new eDschungel\ConfigHandler();
$dbNames = $configHandler->getDbNames();

$noSuccessfulDumps = 0;
$noSuccessfulMailings = 0;

print "myDBDumper started\n\n";

foreach ($dbNames as $dbName) {
    $configHandler->loadConfig($dbName);
    $configHandler->checkConfig();
    $backupFileHandler = new eDschungel\BackupFileHandler($configHandler, $dbName);
    $dumper = new eDschungel\Dumper($configHandler, $dbName);
    $mailer = new eDschungel\Mailer($configHandler);
    $backupFileName = $backupFileHandler->createCurrentBackupFileName();
    if ($dumper->dump($backupFileName)) {
        $noSuccessfulDumps++;
        if ($mailer->sendMail($dbName, $backupFileName)) {
            $noSuccessfulMailings++;
        }
        $backupFileHandler->rotateBackups();
    }
}
print "\n";
if ($noSuccessfulDumps === $configHandler->getNrDBs() && $noSuccessfulMailings === $configHandler->getNrDBs()) {
    print "All databases were dumped and mailed successfully.\n";
} elseif ($noSuccessfulDumps === $configHandler->getNrDBs()) {
    print "All dumps were successful, but only $noSuccessfulMailings mailings out of ";
    print $configHandler->getNrDBs();
    print " were successful.\n";
} else {
    print "Only $noSuccessfulDumps dumps out of $configHandler->getNrDBs() were successful.\n";
}
