<?php

namespace eDschungel;

class Dumper
{
    public function __construct($config, $dbName)
    {
        $this->config = $config;
        $this->dbName = $dbName;
    }

    public function dump()
    {
        $cmdline = "mysqldump";
        exec($cmdline);
    }

    public function wasSuccessful($filename)
    {
        $fp = fopen($filename, "r");
    }
}
