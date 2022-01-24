<?php
namespace Ode;

class SMTP extends \PHPMailer {
    public function __construct() {
        parent::__construct();

        $this->Host = SMTP_HOST;
        $this->Port = SMTP_PORT;
        $this->SMTPAuth = true;
        $this->SMTPSecure = 'ssl';
        $this->isSMTP();
        $this->Username = SMTP_USER;
        $this->Password = SMTP_PASSWORD;
    }
}