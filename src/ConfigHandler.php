<?php

namespace eDschungel;

/**
Class to do configuration handling
*/
class ConfigHandler
{
    private $configDir = "config";

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
        return str_replace(".conf.php", "", $this->getConfigFileNames());
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
        include_once $this->configDir . $dbName;
    }
}
