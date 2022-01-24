<div>
    <h2>Please, <a href="<?php echo $this->manager->friendlyAction('register'); ?>">register</a> or <a href="<?php echo $this->manager->friendlyAction('auth'); ?>">login</a> to see the logs.</h2>

    <p>Registration is free!</p>

    <h2>All registered users</h2>

    <h3>Update: May 16, 2016</h3>

    <p>
        All registered users have limited access to the logs. Registered
        users get up to 500 requests per hour to any log related information.
        Pro users get unlimited access.
    </p>

    <p>
        All registered users can now edit their own logs.
    </p>

    <h2>Pro Account</h2>
    <p>
        Pro account users have access to a lot more information than registered
        users or guests. Current registrants who have donated $5.00 or more to
        the site, will become pro users, and will have unlimited
        access to log features. This pro account will also grant access to a
        list of stations that could potentially be broadcasting within the
        current hour on the current day, or any day. Thank you, for your
        contribution.</p>
    <p>Users who donate $5.00 USD, will be given full access (pro account) to the log book for the duration of the current version of
        this site and its current functionality.</p>

    <h3>Features:</h3>
    <ul>
        <li>Full log descriptions.</li>
        <li>
            Geographical location information of transmission or station
            sites.
        </li>
        <li>Station or transmitter details.</li>
        <li>Audio samples of select log entries.</li>
        <li>Find out what could potentially be on within the hour.</li>
        <li>Search by frequency or free-text.</li>
    </ul>

    <h2>How to Get a Pro Account</h2>
    <?php if ($this->auth->isRegistered()): ?>
        <p>
            Select the <em>Donate</em> item from the menu, and then click the <em>Donate</em> button in the pop up item
            that appears.
            If you are already registered, simply use your email, <?php echo $this->auth->getSession()->email; ?>
            in the payment notes of the PayPal transaction. <em>If you do not do this, the site manager has no way to
                activate your Pro status</em>.
        </p>
    <?php else: ?>
        <p>First, you need to <a href="<?php echo $this->manager->friendlyAction('register'); ?>">register</a> with the
            logbook.
            Once, your account is registered, use the email you provided in the registration in the payment notes of the
            PayPal transaction. <u>If you do not do this, the site manager has no way to activate your Pro status</u>.
        </p>
    <?php endif; ?>
    <h2>Questions</h2>
    <p>If for any reason there is a problem setting up your Pro status, please, <a
            href="<?php echo $this->manager->friendlyAction('contact'); ?>">contact the site administrator</a>.</p>
</div>
