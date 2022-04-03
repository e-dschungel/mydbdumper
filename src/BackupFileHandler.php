<?php

namespace eDschungel;

class BackupFileHandler
{
    protected $config;
    protected $dbNames;

    /**
    Constructor for the tests.

    @param $config configurations
     */
    public function __construct($config, $dbName)
    {
        $this->config = $config;
        $this->dbName = $dbName;
    }

    public function getBackupFileNames()
    {
        $directory = __DIR__ . '/' . getDirectoryName();
        print($directory);
        chdir($directory);
        $fileNames = array_diff(scandir($directory), array('..', '.'));
        if (count($fileNames) > 0) {
            usort($fileNames, function ($a, $b) {
                    return filemtime($b) - filemtime($a);
            });
            return $fileNames;
        } else {
            return [];
        }
    }

    public function getNrBackupFiles()
    {
        return count(getBackupFileNames());
    }

    public function getNewestBackupFileName()
    {
        return getBackupFileNames()[getNrBackupFiles() - 1];
    }

    public function getOldestBackupFileName()
    {
        return getBackupFileNames()[0];
    }

    public function getDirectoryName()
    {
        return config[backup_dir] . '/' . $this->$dbName;
    }

    public function createCurrentBackupFileName()
    {
        return $this->dbName . strftime("%d%m%y", time()) . ".gz";
    }
}
