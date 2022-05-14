<?php

namespace eDschungel;

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

/**
Class to send dump via mail
 */
class Mailer
{
    protected $config;

    /**
    Constructor

    @param $config configuration
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
    Send mail with dump backup

    @param $dbName name of database to send
    @param $backupFilename name of dumpfile to send

    @return true if successful
    */
    public function sendMail($dbName, $backupFilename)
    {
        $mail = new PHPMailer(true);
        try {
            //Server settings
            switch (strtolower($this->config->getEmailBackend())) {
                case "mail":
                        $mail->isMail();
                    break;
                case "smtp":
                    $mail->isSMTP();
                    $mail->Host     = $this->config->getSMTPHost();
                    $mail->SMTPAuth = $this->config->getSMTPAuth();
                    $mail->Username = $this->config->getSMTPUsername();
                    $mail->Password = $this->config->getSMTPPassword();
                    switch (strtolower($$this->config->getSMTPSecurity())) {
                        case "starttls":
                            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                            break;
                        case "smtps":
                            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                            break;
                        default:
                            echo("Invalid config entry for SMTPSecurity {$this->config->getSMTPSecurity()}\n");
                    }
                    $mail->Port = $this->config->getSMTPPort();
                    break;
                default:
                        echo("Invalid config entry for emailBackend {$this->config->getEmailBackend()}\n");
            }

            //Recipients
            $mail->setFrom($this->config->getEmailFrom());
            $mail->addAddress($this->config->getEmailTo());

            // Content
            $mail->isHTML(false);
            $mail->Subject = "Backup of database " . $dbName;
            $mail->Body    = "Find attached a backup of your database " . $dbName;
            $mail->AddAttachment(
                $this->config->getBackupDirName($dbName) . "/" . $backupFilename,
                $name = pathinfo($backupFilename, PATHINFO_BASENAME),
                $encoding = 'base64',
                $type = 'application/octet-stream'
            );
            $mail->CharSet = 'utf-8';

            $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}\n";
            return false;
        }
        return true;
    }
}
