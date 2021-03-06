<?php

namespace eDschungel;

    use PHPMailer\PHPMailer\PHPMailer;

/**
Class to do configuration handling
 */
class ConfigHandler
{
    private $configDir = "config";
    private $configFileExtension = ".conf.php";
    const DEFAULT_CONFIG_FILENAME = "config.conf.php";
    private $config = [];

    /**
    Returns array of all config files (except default config file)

    @return returns array of all configfiles or empty array if none are found
     */
    public function getConfigFileNames()
    {
        $directory = __DIR__ . '/../' . $this->configDir;
        chdir($directory);
        $fileNames = glob('*' .  $this->configFileExtension);
        $fileNames = \array_diff($fileNames, [self::DEFAULT_CONFIG_FILENAME]);
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
        $this->config = [];
        if (file_exists(__DIR__ . "/../" . $this->configDir . "/" . self::DEFAULT_CONFIG_FILENAME)) {
            include __DIR__ . "/../" . $this->configDir . "/" . self::DEFAULT_CONFIG_FILENAME;
            $this->config = $config;
        }
        include __DIR__ . "/../" . $this->configDir . "/" . $dbName . $this->configFileExtension;
        $this->config = array_merge($this->config, $config);
    }

    /**
    * Helper function that checks if multiple keys are in array
    *
    * @param $array array to check
    * @param $keys keys to check
    *
    * @return bool true if all keys exist in array
    */
    private function allArrayKeysExist($array, $keys)
    {
        foreach ($keys as $k) {
            if (!isset($array[$k])) {
                return false;
            }
        }
        return true;
    }

    /**
    * Helper function to check if given key exists. Return value for given key if it exists, $default if not.
    *
    * @param $key key of config to check
    * @param $default value to return if $key doies not exist
    *
    * @return Return value for given $key if it exists, $default if not.
    */
    private function getConfig($key, $default)
    {
        if (array_key_exists($key, $this->config)) {
            return $this->config[$key];
        } else {
            return $default;
        }
    }

    /**
    * Function to check config, print warnings, and return a config with required default values
    *
    * @return void
    */
    public function checkConfig()
    {
        $smtp_config_requirements = array(
            "SMTPHost",
            "SMTPAuth",
            "SMTPUsername",
            "SMTPPassword",
            "SMTPSecurity",
            "SMTPPort",
        );

        if (!array_key_exists("username", $this->config)) {
            echo("Username not given\n");
        }

        if (!array_key_exists("password", $this->config)) {
            echo("Password not given\n");
        }

        if (!array_key_exists("backupDir", $this->config)) {
            echo("Backup directory not given, setting default value \"../backup\"\n");
            $this->config['backupDir'] = "../backup";
        }

        if (!array_key_exists("emailBackend", $this->config)) {
            echo("emailBackend not given, setting default value mail!\n");
            $this->config['emailBackend'] = "mail";
        }

        if (strtolower($this->config['emailBackend']) == "smtp") {
            if (!$this->allArrayKeysExist($this->config, $smtp_config_requirements)) {
                echo("Not all required SMTP variables were given!\n");
            }
        }
    }

    /**
    Get username for current database

    @return username
     */
    public function getUsername()
    {
        return $this->getConfig("username", "");
    }

    /**
    Get password for current database

    @return password
     */
    public function getPassword()
    {
        return $this->getConfig("password", "");
    }

    /**
    Get directory name where backup is stored for given database

    @param $dbName name of database

    @return directory name where backup is stored
     */
    public function getBackupDirName($dbName)
    {
        $backupDir = $this->getConfig("backupDir", "");
        if (strlen($backupDir) == 0) {
            return "";
        } else {
            return $backupDir  . '/' . $dbName;
        }
    }

    /**
    Get chosen email backend

    @return email backend, "mail" if not given
     */
    public function getEmailBackend()
    {
        return $this->getConfig("emailBackend", "mail");
    }

    /**
    Get SMTP Host

    @return smtp host
     */
    public function getSMTPHost()
    {
        return $this->getConfig("SMTPHost", "");
    }

    /**
    Get chosen SMTP auth method

    @return SMTP auth method
     */
    public function getSMTPAuth()
    {
        return $this->getConfig("SMTPAuth", "");
    }

    /**
    Get chosen SMTP username

    @return SMTP username
     */
    public function getSMTPUsername()
    {
        return $this->getConfig("SMTPUsername", "");
    }

    /**
    Get chosen SMTP password

    @return SMTP password
     */
    public function getSMTPPassword()
    {
        return $this->getConfig("SMTPPassword", "");
    }

    /**
    Get chosen SMTP security

    @return SMTP security constants PHPMailer::ENCRYPTION_STARTTLS or PHPMailer::ENCRYPTION_SMTPS or empty string
     */
    public function getSMTPSecurity()
    {
        $SMTPSecurity = strtolower($this->getConfig("SMTPSecurity", ""));
        switch ($SMTPSecurity) {
            case "starttls":
                return PHPMailer::ENCRYPTION_STARTTLS;
                break;
            case "smtps":
                return PHPMailer::ENCRYPTION_SMTPS;
                break;
            default:
                return "";
        }
    }

    /**
    Get email address from which backup mail is send

    @return email address
     */
    public function getEmailFrom()
    {
        return $this->getConfig("emailFrom", "");
    }

    /**
    Get email address to which backup mail is send

    @return email address
     */
    public function getEmailTo()
    {
        return $this->getConfig("emailTo", "");
    }

    /**
    Get maximum number of backups.

    @return number of backups
     */
    public function getMaxNrBackups()
    {
        return $this->getConfig("maxNrBackups", PHP_INT_MAX);
    }

    /**
    Get options for mysqldump

    @return mysqldump options
     */
    public function getMysqldumpOptions()
    {
        return $this->getConfig("mysqldumpOptions", "");
    }
}
