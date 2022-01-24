<nav class="navbar navbar-default" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse"
                    data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">Home</a>
        </div>

        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="horizNav nav navbar-nav">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Logs <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="<?php echo $this->manager->friendlyAction('logs'); ?>">List all</a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="<?php echo $this->manager->friendlyAction("logs", "recent"); ?>">Recent</a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="<?php echo $this->manager->friendlyAction("logs", "whatson", "now", array("dow", 1)); ?>"
                               title="Within the hour on this day of the week.">What's on now?</a>
                        </li>
                        <li>
                            <a href="<?php echo $this->manager->friendlyAction("logs", "whatson", "now"); ?>"
                               title="Now, but any day of the week.">What's on any day?</a>
                        </li>
                        <?php if ($this->auth->isAuth()): ?>
                            <li class="divider"></li>
                            <li>
                                <a href="<?php echo $this->manager->friendlyAction('logs', 'add'); ?>">Submit log</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Info <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo $this->manager->friendlyAction('info', 'resources'); ?>">Resources</a>
                        </li>
                        <li><a href="<?php echo $this->manager->friendlyAction('info', 'links'); ?>"
                               title="Related links">Links</a></li>
                        <li class="divider"></li>
                        <li>
                            <a href="<?php echo $this->manager->friendlyAction('help'); ?>">Help</a>
                        </li>
                    </ul>
                </li>

                <?php if (!$this->auth->isAuth()): ?>

                    <?php if (!$this->manager->isMode("register")): ?>
                        <li>
                            <a href="<?php echo $this->manager->friendlyAction("register"); ?>">Register</a>
                        </li>
                    <?php endif; ?>

                <?php endif; ?>

                <?php if ($this->auth->isAdmin()): ?>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Admin <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="<?php echo $this->manager->action('admin_hflogs'); ?>">Logs</a>
                            </li>
                            <li>
                                <a href="<?php echo $this->manager->action('admin_stations'); ?>">Stations/Locations</a>
                            </li>
                            <li>
                                <a href="<?php echo $this->manager->action('admin_users'); ?>">Users</a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>

            <ul class="nav navbar-nav navbar-right">
                <?php if ($this->auth->isAuth()): ?>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle"
                           data-toggle="dropdown"><?php echo $this->auth->getSession()->username; ?> <b
                                class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="<?php echo $this->manager->friendlyAction("user"); ?>">My Account</a>
                            </li>
                            <li>
                                <a href="<?php echo $this->manager->friendlyAction("user", "logs"); ?>">Logs</a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="<?php echo $this->manager->friendlyAction("auth", "logout", null, array('referer', APP_SITE_URL . '')); ?>"
                                   title="Log out!">Logout</a>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li>
                        <a href="<?php echo $this->manager->friendlyAction('auth', null, null, array('referer', $this->manager->friendlyAction($this->manager->getPage()))); ?>">Login</a>
                    </li>
                <?php endif; ?>
            </ul>

            <form class="navbar-form navbar-right" role="search" action="<?php echo $this->manager->friendlyAction('logs', 'search'); ?>" method="get">
                <input type="hidden" name="token" value="<?php echo $this->searchtoken; ?>"/>
                <div class="form-group">
                    <input class="form-control" type="text" name="q" value=""/>
                </div>
                <button type="submit" class="btn btn-default">Search</button>
            </form>

            <form class="navbar-form navbar-right" action="https://www.paypal.com/cgi-bin/webscr" method="post">
                <input type="hidden" name="cmd" value="_xclick"/>
                <input type="hidden" name="business" value="cjwalsh@ymail.com"/>
                <input type="hidden" name="item_name" value="HF Logbook Donation"/>
                <input type="hidden" name="item_number" value="hflogbook-donation-<?php echo UUID::get(); ?>"/>
                <input type="hidden" name="no_shipping" value="1"/>
                <input type="hidden" name="no_note" value="1"/>
                <input type="hidden" name="currency_code" value="USD"/>
                <input type="hidden" name="lc" value="AU"/>
                <input type="hidden" name="bn" value="PP-BuyNowBF"/>
                <input type="hidden" name="return"
                       value="<?php echo substr($this->manager->getFullURL(), 0, -1) . $this->manager->friendlyAction('donation', 'thankyou'); ?>"/>

                <div class="form-group">
                    <label for="donationAmount">Amount (USD)</label>
                    <select class="form-control" name="amount" id="donationAmount">
                        <option value="1">$1.00</option>
                        <option value="5">$5.00</option>
                        <option value="10">$10.00</option>
                        <option value="25">$25.00</option>
                        <option value="50">$50.00</option>
                        <option value="100">$100.00</option>
                    </select>
                </div>

                <button class="btn btn-default" type="submit">Donate</button>
            </form>
        </div>
    </div> <!-- end container-fluid -->
</nav> <!-- end horizontal nav menu container -->