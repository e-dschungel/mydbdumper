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

    @param $dbName name of database

    @return directory name where backup is stored
     */
    public function getBackupDirName($dbName)
    {
        return $this->config["backup_dir"] . '/' . $dbName;
    }

    /**
    Get chosen email backend

    @return email backend
     */
    public function getEmailBackend()
    {
        return $this->config["emailBackend"];
    }

    /**
    Get SMTP Host

    @return smtp host
     */
    public function getSMTPHost()
    {
        return $this->config["SMTPHost"];
    }

    /**
    Get chosen SMTP auth method

    @return SMTP auth method
     */
    public function getSMTPAuth()
    {
        return $this->config["SMTPAuth"];
    }

    /**
    Get chosen SMTP username

    @return SMTP username
     */
    public function getSMTPUsername()
    {
        return $this->config["SMTPUsername"];
    }

    /**
    Get chosen SMTP password

    @return SMTP password
     */
    public function getSMTPPassword()
    {
        return $this->config["SMTPPassword"];
    }

    /**
    Get chosen SMTP security

    @return SMTP security
     */
    public function getSMTPSecurity()
    {
        return $this->config["SMTPSecurity"];
    }

    /**
    Get email address from which backup mail is send

    @return email address
     */
    public function getEmailFrom()
    {
        return $this->config["emailFrom"];
    }

    /**
    Get email address to which backup mail is send

    @return email address
     */
    public function getEmailTo()
    {
        return $this->config["emailTo"];
    }
}
