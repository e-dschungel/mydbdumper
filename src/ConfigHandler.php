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
        chdir($this->configDir);
        $directory = __DIR__ . '/../' . $this->configDir;
        $fileNames = array_diff(scandir($directory), array('..', '.'));
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
    Returns nr of config files

    @return number of config files
     */
    public function getNrConfigs()
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
}
