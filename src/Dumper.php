<?php

namespace eDschungel;

/**
Class to dump MySQL database and check if dump was successful
*/
class Dumper
{
    protected $config;
    protected $dbName;

    /**
    Constructor

    @param $config configuration
    @param $dbName name of database
    */
    public function __construct($config, $dbName)
    {
        $this->config = $config;
        $this->dbName = $dbName;
    }

    /**
    Dump database to file

    @return void
    */
    public function dump()
    {
        $arguments = [];
        $arguments[] = "--user=" . $this->config->getUsername();
        $arguments[] = "--password=" . $this->config->getPassword();
        $arguments[] = $this->dbName;
        $cmdline = "mysqldump" . " " . implode(" ", $arguments);
        print($cmdline);
        exec($cmdline);
    }

    /**
    Check if dump was successful

    @param $filename of dump to check

    @return true if success
    */
    public function wasSuccessful($filename)
    {
        $fp = fopen($filename, "r");
        return false;
    }
}
