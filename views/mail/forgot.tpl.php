<div>
    <p>Dear <?php echo $this->user->fullname(); ?>,</p>
    <p>Your credentials for HF Logbook are as follows:</p>
    <p>
        Username: <?php echo $this->user->username; ?>
    </p>
    <p>
        We unfortunately cannot provide your current password, please, click <a href="<?php echo $this->link; ?>">here</a> to reset it!
    </p>
    <p>Best regards,</p>
    <p>Chris, KJ6BBS</p>
</div>