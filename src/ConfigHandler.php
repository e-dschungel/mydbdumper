<?php

namespace eDschungel;

class ConfigHandler
{
    private $configDir = "config";

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

    public function getDbNames()
    {
        return str_replace(".conf.php", "", $this->getConfigFileNames());
    }


    public function getNrConfigs()
    {
        count($this->getDBNames());
    }



    public function loadConfig($dbName)
    {
        include_once $this->configDir . $dbName;
    }
}
