<!DOCTYPE html>
<html lang="en">
<head>
    <title>HF Logbook</title>

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="Cache-Control" content="max-age=3600, must-revalidate">
    <meta http-equiv="pragma" content="max-age=3600, must-revalidate">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
    <meta content="A current log of stations heard globally listed by frequency, whether it be HF, shortwave (SWL), utility, military or whatever else pops up on the bands. This is a shortwave radio logbook." name="description">

    <?php if ($this->manager->isPage('logs') && $this->manager->isMode('log') && $this->manager->isTask('view')): ?>
        <?php if ($this->log->hasLocation()): ?>
            <meta name="geo.position"
                  content="<?php echo $this->log->location()->location()->lat . ";" . $this->log->location()->location()->lng; ?>">
            <meta name="geo.placename" content="<?php echo $this->log->location()->location()->site; ?>">
        <?php endif; ?>
    <?php endif; ?>

    <?php echo $this->fetch('head/css.tpl.php'); ?>

    <?php echo $this->fetch('head/js.tpl.php'); ?>

    <!-- begin google analytics code -->
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-77852211-1', 'auto');
        ga('send', 'pageview');

    </script>
    <!-- end google analytics code -->
</head>
<body>
<div id="logContainer" class="container">

    <div class="page-header">
        <h1>HF Logbook</h1>
    </div>

    <?php echo $this->fetch("hflogs/topmenu.tpl.php"); ?>

    <div class="mainContainer"><?php echo $this->contentforlayout; ?></div>

    <footer class="footer well well-sm">
        High Frequency (HF)/Shortwave Listening (SWL) log book is
        a web-based logging system to help shortwave listeners search for
        broadcasts of AM commercial radio, spy numbers stations, utility
        stations, like worldwide military or just about anything else
        coming over the high frequency (HF) air waves.
        <?php if (!$this->auth->isAuth()): ?>
            Please, <a href="<?php echo $this->manager->friendlyAction("register"); ?>"
                       title="Register now!">register</a>,
            so that you may enter new logs
            into the system.
        <? endif; ?>
        <ul class="nav nav-pills">
            <li>
                <a href="<?php echo $this->manager->friendlyAction('contact'); ?>">Contact Us</a>
            </li>
            <li>
                <a href="<?php echo $this->manager->friendlyAction('info', 'privacy-policy'); ?>">Privacy
                    Policy</a>
            </li>
        </ul>
    </footer>
</div>

</body>
</html>
