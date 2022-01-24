<? if($this->manager->isMode()): ?>
<h2>Contact Us!</h2>
<div><?= $this->form; ?></div>
<? endif; ?>

<? if($this->manager->isMode("sent")): ?>
<div>
        <p>
                Thanks, for contacting me. Please, await a response, and I'll be sure to address 
                any of your concerns.
        </p>
        <p>Best regards,<br />Chris Walsh, KJ6BBS</p>
</div>
<? endif; ?>

<? if($this->manager->isMode("paypal")): ?>
<div>
        <? if($this->manager->isTask("thanks")): ?>
        <div>
                Thanks for your generous contribution. HF Logbook really does appreciate it.
                Your proceeds will go towards maintaining a balance for the monthly hosting services.
                If you have any questions regarding HF Logbook's financing or revenue, please, contact me <a href="<?= $this->manager->friendlyAction("contact"); ?>">here</a>
        </div>
        <? endif; ?>
</div>
<? endif; ?>
