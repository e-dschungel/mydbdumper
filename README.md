# MyDBDumper
This is a script to dump MySQL or MariaDB databases and mail it to a given address.
A configurable number of backups is kept locally.
It uses `mysqldump` for dumping and `gzip` for compressing so both are required as well as access to the `proc_open` PHP function.
It uses [PHPMailer](https://github.com/PHPMailer/PHPMailer) for email sending and [Process](https://github.com/symfony/process) for executing command line programs.

## Requirements
* PHP >= 8.1
* a MySQL or MariaDB database
* enabled proc_open function
* mysqldump executable
* gzip executable

## Installation
### From Git
* Clone this repo `git clone https://github.com/e-dschungel/mydbdumper`
* Install dependencies using composer `composer install --no-dev`
* Rename `config/config.dist.php` to `config/config.conf.php` and edit it according to your needs
* Create DBNAME.conf.php, see Configuration

### From ZIP file
* Download `myDBDumper.zip` (NOT `Source Code (zip)` or `Source Code (tar.gz)`)  from https://github.com/e-dschungel/mydbdumper/releases/latest
* Extract and upload it to your webserver
* Rename `config/config.dist.php` to `config/config.conf.php` and edit it according to your needs, see below
* Create DBNAME.conf.php, see Configuration

## Configuration
The configuration is loaded from (at least) two files: the general file `config.conf.php` and a specific file `DBNAME.conf.php` for a database with the name DBNAME.
The specific file `DBNAME.conf.php` will be loaded after `config.conf.php` and will overwrite settings given in `config.conf.php`.

|variable|description|
|---|---|
|$config['backupDir']| Directory to which to backups are stored|
|$config['emailTo']| email adress of the recipient, multiple recipients can be given separated by comma, e.g. $config['emailTo'] = "user1@example.com, user2@example.com";|
|$config['emailFrom']| email adress shown as sender of the dump|
|$config['maxNrBackups']| Number of backups to keep locally, if more backups exist, they are deleted|
|$config['SMTPHost']| SMTP hostname|
|$config['SMTPAuth']| use SMTP authentication? true or false|
|$config['SMTPUsername']| SMTP username|
|$config['SMTPPassword']| SMTP password|
|$config['SMTPSecurity']| type of SMTP security setting, can be "starttls" or "smtps"|
|$config['SMTPPort']| SMTP port|
|$config['username']| MySQL/MariaDB username|
|$config['password']|| MySQL/MariaDB password|

## Changelog
### Version 0.1
* first public release

### Version 0.1.1
* update PHPMailer to 6.6.5
* update Process to 5.4.11

### Version 0.1.2
* improved handling if backup dir does not exist
* update dev dependencies

### Version 0.1.3
* update PHPMailer to 6.7.1
* update Process to 5.4.19
* update dev dependencies
* correctly document minimal PHP version (due to process dependency)

### Version 0.1.4
* update PHPMailer to 6.8.0
* update Process to 5.4.21
* update dev dependencies

### Version 0.1.5
* update PHPMailer to 6.8.1
* update Process to 5.4.28
* update dev dependencies

### Version 0.1.6
* update PHPMailer to 6.9.1
* update dev dependencies

### Version 0.2.0
* update Process to 6.4.8
* with that minimal PHP version is increased to 8.1 
* update dev dependencies
