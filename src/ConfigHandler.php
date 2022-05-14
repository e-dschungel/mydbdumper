<?php

namespace eDschungel;

/**
Class to do configuration handling
*/
class ConfigHandler
{
    private $configDir = "config";
    private $configFileExtension = ".conf.php";
    private $config = [];

    /**
    Returns array of all config files

    @return returns array of all configfiles or empty array if none are found
     */
    public function getConfigFileNames()
    {
        $directory = __DIR__ . '/../' . $this->configDir;
        chdir($directory);
        $fileNames = glob('*' .  $this->configFileExtension);
        if (count($fileNames) > 0) {
            return $fileNames;
        } else {
            return [];
        }
    }

    /**
    Returns array of database names

    @return returns array of all database names or empty array if none are found
     */
    public function getDbNames()
    {
        return str_replace($this->configFileExtension, "", $this->getConfigFileNames());
    }

    /**
    Returns nr of databases

    @return number of config files
     */
    public function getNrDBs()
    {
        return count($this->getDBNames());
    }

    /**
    Load config for given database

    @param $dbName name of database  for which config is loaded

    @return void
     */
    public function loadConfig($dbName)
    {
        include_once "../" . $this->configDir . "/" . $dbName . $this->configFileExtension;
        $this->config = $config;
    }

    /**
    Get username for current database

    @return username
     */
    public function getUsername()
    {
        return $this->config["username"];
    }

    /**
    Get password for current database

    @return password
     */
    public function getPassword()
    {
        return $this->config["password"];
    }

    /**
    Get directory name where backup is stored for given database

    @param $dbname name of database

    @return directory name where backup is stored
     */
    public function getBackupDirName($dbName)
    {
        return $this->config["backup_dir"] . '/' . $dbName;
    }




    public function getEmailBackend() {
        return $this->config["emailBackend"];
    }

    public function getSMTPHost() {
        return $this->config["SMTPHost"];
    }

    public function getSMTPAuth() {
        return $this->config["SMTPAuth"];
    }

    public function getSMTPUsername() {
        return $this->config["SMTPUsername"];
    }

    public function getSMTPPassword() {
        return $this->config["SMTPPassword"];
    }

    public function getSMTPSecurity() {
        return $this->config["SMTPSecurity"];
    }

    public function getEmailFrom() {
        return $this->config["emailFrom"];
    }

    public function getEmailTo() {
        return $this->config["emailTo"];
    }

}
