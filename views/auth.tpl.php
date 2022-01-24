<?php if ($this->manager->isMode()): ?>
    <div>
        <?php if ($this->manager->isTask()): ?>
            <div class="row">
                <div class="col-lg-8 col-md-9 col-sm-12 col-xs-12">
                    <?php if (!empty($this->error)): ?>
                        <div class="notification notification-error"><?php echo $this->error; ?></div>
                    <?php endif; ?>
                    <div><?php echo $this->form; ?></div>
                </div>
                <div class="col-lg-4 col-md-3 col-sm-12 col-xs-12">
                    <div class="list-group">
                        <a href="<?php echo $this->manager->friendlyAction('auth', 'forgot'); ?>"
                           class="list-group-item">Forgot username/password?</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if ($this->manager->isMode('forgot')): ?>
    <?php if ($this->manager->isTask()): ?>
    <h2>Forgot account credentials</h2>
    <div>
        <?php echo $this->form; ?>
    </div>
    <?php endif; ?>

    <?php if($this->manager->isTask('reset')): ?>
    <h2>Reset password</h2>
    <div>
        <?php echo $this->form; ?>
    </div>
    <?php endif; ?>

    <?php if($this->manager->isTask('success')): ?>
    <div>You password was successfully updated. Please, <a href="<?php echo $this->manager->friendlyAction('auth'); ?>">log in</a>.</div>
    <?php endif; ?>

    <?php if($this->manager->isTask('sent')): ?>
        <div>An email was sent to the address you provided, with instructions on how to access your credentials.</div>
    <?php endif; ?>
<?php endif; ?>
