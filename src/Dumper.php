<?php

namespace eDschungel;

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
        $this->dateFormatter = \datefmt_create(null, \IntlDateFormatter::MEDIUM, \IntlDateFormatter::MEDIUM,
        null, \IntlDateFormatter::GREGORIAN);
    }

    /**
    Dump database to file

    @return true on success
    */
    public function dump($filename)
    {
        $backupDirName = $this->config->getBackupDirName($this->dbName);
        if (!file_exists($backupDirName)){
            mkdir($backupDirName, 0755, true);
        }
        $outputfilename = $this->config->getBackupDirName($this->dbName) . "/" . $filename;
        $tempfilename = str_replace(".gz", "", $outputfilename);
        $arguments = [];
        $arguments[] = "--user=" . $this->config->getUsername();
        $arguments[] = "--password=" . $this->config->getPassword();
        $arguments[] = "--result-file=" . $tempfilename;
        $arguments[] = $this->dbName;
        $cmdline = "mysqldump" . " " . implode(" ", $arguments);
        $starttime = time();
        print("Start dump of database " . $this->dbName . " at " . datefmt_format($this->dateFormatter, $starttime). "\n");
        print("Dump command $cmdline \n");
        $mysqldumpoutput = "";
        $returncode = exec($cmdline, $mysqldumpoutput);
        if (strlen($returncode) === 0 && $this->wasSuccessful($tempfilename)){
            $gzipcmdline = "gzip" . " -f " . $tempfilename;
            $gzipreturncode = exec($gzipcmdline);
            if (strlen($gzipreturncode) === 0){
                $stoptime = time();
                $duration = $stoptime - $starttime;
                print("Dump of database $this->dbName successfully completed at " .  datefmt_format($this->dateFormatter, $stoptime) . " duration ${duration}s\n");
                return true;
            }
        }
        print("Dump of database $this->dbName failed\n");
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
	 * @author Torleif Berger, Lorenzo Stanco
	 * @link http://stackoverflow.com/a/15025877/995958
	 * @license http://creativecommons.org/licenses/by/3.0/
	 */
	private function tailCustom($filepath, $lines = 1, $adaptive = true) {

		// Open file
		$f = @fopen($filepath, "rb");
		if ($f === false) return false;

		// Sets buffer size, according to the number of lines to retrieve.
		// This gives a performance boost when reading a few lines from the file.
		if (!$adaptive) $buffer = 4096;
		else $buffer = ($lines < 2 ? 64 : ($lines < 10 ? 512 : 4096));

		// Jump to last character
		fseek($f, -1, SEEK_END);

		// Read it and adjust line number if necessary
		// (Otherwise the result would be wrong if file doesn't end with a blank line)
		if (fread($f, 1) != "\n") $lines -= 1;

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
