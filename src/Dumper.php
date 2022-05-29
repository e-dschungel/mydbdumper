<?php

namespace eDschungel;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
Class to dump MySQL database and check if dump was successful
 */
class Dumper
{
    protected $config;
    protected $dbName;
    protected $dateFormatter;

    /**
    Constructor

    @param $config configuration
    @param $dbName name of database
     */
    public function __construct($config, $dbName)
    {
        $this->config = $config;
        $this->dbName = $dbName;
        $this->dateFormatter = \datefmt_create(
            null,
            \IntlDateFormatter::MEDIUM,
            \IntlDateFormatter::MEDIUM,
            null,
            \IntlDateFormatter::GREGORIAN
        );
    }

    /**
    Dump database to file

    @param $filename filename to which database is dumped

    @return true on success
     */
    public function dump($filename)
    {
        $backupDirName = $this->config->getBackupDirName($this->dbName);
        if (!file_exists($backupDirName)) {
            mkdir($backupDirName, 0755, true);
        }
        $outputfilename = $this->config->getBackupDirName($this->dbName) . "/" . $filename;
        $tempfilename = str_replace(".gz", "", $outputfilename);
        $dumpCommand = [];
        $dumpCommand[] = "mysqldump";
        $dumpCommand[] = "--user=" . $this->config->getUsername();
        if (strlen($this->config->getMysqldumpOptions() > 0)) {
            $dumpCommand[] = $this->config->getMysqldumpOptions();
        }
        $dumpCommand[] = "--result-file=" . $tempfilename;
        $dumpCommand[] = $this->dbName;
        //don't password by argument but as enviromental parameter for security
        //see https://stackoverflow.com/a/34670902
        $dumpProcess = new Process($dumpCommand, null, ["MYSQL_PWD" => $this->config->getPassword()], null, null);
        $starttime = time();
        print("Started dump at "
        . datefmt_format($this->dateFormatter, $starttime) . "\n");
        print("Dump command: " . implode(" ", $dumpCommand) . "\n");
        $dumpProcess->run();
        if ($dumpProcess->isSuccessful() && $this->wasSuccessful($tempfilename)) {
            $gzipCommand = ["gzip",  "-f", $tempfilename];
            $gzipProcess = new Process($gzipCommand, null, null, null);
            $gzipProcess->run();
            if ($gzipProcess->isSuccessful()) {
                $stoptime = time();
                $duration = $stoptime - $starttime;
                print("Dump completed successfully at " .
                datefmt_format($this->dateFormatter, $stoptime) . ", duration ${duration}s\n");
                return true;
            } else {
                print("Compressing failed, error message:\n" . $gzipProcess->getErrorOutput());
                return false;
            }
        } else {
            print("Dump of database $this->dbName failed, error message:\n");
            print($dumpProcess->getErrorOutput());
        }
        return false;
    }

    /**
    Check if dump was successful

    @param $filename of dump to check

    @return true if success
     */
    public function wasSuccessful($filename)
    {
        $lastline = $this->tailCustom($filename);
        $successfulOutput = "-- Dump completed ";
        return(strpos($lastline, $successfulOutput) === 0);
    }

    /**
     * Slightly modified version of http://www.geekality.net/2011/05/28/php-tail-tackling-large-files/
     *
     * @param $filepath path to file
     * @param $lines number of lines to return
     * @param $adaptive use adaptive buffer size
     *
     * @author  Torleif Berger, Lorenzo Stanco
     * @link    http://stackoverflow.com/a/15025877/995958
     * @license http://creativecommons.org/licenses/by/3.0/
     * @return last $lines number of lines of given file
     */
    private function tailCustom($filepath, $lines = 1, $adaptive = true)
    {
        // Open file
        $f = @fopen($filepath, "rb");

        if ($f === false) {
            return false;
        }

        // Sets buffer size, according to the number of lines to retrieve.
        // This gives a performance boost when reading a few lines from the file.
        if (!$adaptive) {
            $buffer = 4096;
        } else {
            $buffer = ($lines < 2 ? 64 : ($lines < 10 ? 512 : 4096));
        }

        // Jump to last character
        fseek($f, -1, SEEK_END);

        // Read it and adjust line number if necessary
        // (Otherwise the result would be wrong if file doesn't end with a blank line)
        if (fread($f, 1) != "\n") {
            $lines -= 1;
        }

        // Start reading
        $output = '';
        $chunk = '';

        // While we would like more
        while (ftell($f) > 0 && $lines >= 0) {
            // Figure out how far back we should jump
            $seek = min(ftell($f), $buffer);

            // Do the jump (backwards, relative to where we are)
            fseek($f, -$seek, SEEK_CUR);

            // Read a chunk and prepend it to our output
            $output = ($chunk = fread($f, $seek)) . $output;

            // Jump back to where we started reading
            fseek($f, -mb_strlen($chunk, '8bit'), SEEK_CUR);

            // Decrease our line counter
            $lines -= substr_count($chunk, "\n");
        }

        // While we have too many lines
        // (Because of buffer size we might have read too many)
        while ($lines++ < 0) {
            // Find first newline and remove all text before that
            $output = substr($output, strpos($output, "\n") + 1);
        }

        // Close file and return
        fclose($f);
        return trim($output);
    }
}
