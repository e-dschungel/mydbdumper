<?php

namespace eDschungel;

/**
Class to handle backupfiles
 */
class BackupFileHandler
{
    protected $config;
    protected $dbName;

    /**
    Constructor

    @param $config configurations
    @param $dbName name of the database
     */
    public function __construct($config, $dbName)
    {
        $this->config = $config;
        $this->dbName = $dbName;
    }

    /**
    Returns array of all backupfiles for given database

    @return returns array of all backupfiles for given database or empty array if none are found
     */
    public function getBackupFileNames()
    {
        $directory = __DIR__ . '/' . $this->config->getBackupDirName($this->dbName);
        print($directory);
        chdir($directory);
        $fileNames = array_diff(scandir($directory), array('..', '.'));
        if (count($fileNames) > 0) {
            usort(
                $fileNames,
                function ($a, $b) {
                    return filemtime($b) - filemtime($a);
                }
            );
            return $fileNames;
        } else {
            return [];
        }
    }

    /**
    Get number of backup files

    @return number of backup files
     */
    public function getNrBackupFiles()
    {
        return count($this->getBackupFileNames());
    }

    /**
    Get file name of newest backup

    @return returns newest backup file name or empty if not found
     */
    public function getNewestBackupFileName()
    {
        return $this->getBackupFileNames()[$this->getNrBackupFiles() - 1];
    }

    /**
    Get file name of oldest backup

    @return returns oldest backup file name or empty if not found
     */
    public function getOldestBackupFileName()
    {
        return $this->getBackupFileNames()[0];
    }

    /**
    Create file name for backup based on timestamp

    @return directory name
     */
    public function createCurrentBackupFileName()
    {
        return $this->dbName . strftime("%d%m%y", time()) . ".gz";
    }
}
