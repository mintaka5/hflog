<?php

switch (\Ode\Manager::getInstance()->getMode()) {
    default:
    case false:
        
        
        // do not show login form for users already logged in
        if (\Ode\Auth::getInstance()->isLoggedIn()) {
            header('Location: ' . \Ode\Manager::getInstance()->friendlyAction("logs", "user"));
            exit();
        }

        $form = new \HTML_QuickForm2('loginForm');
        $form->setAttribute('action', \Ode\Manager::getInstance()->friendlyAction('auth'));
        $form->setAttribute('role', 'form');

        $form->addHidden('referer')->setValue((isset($_GET['referer'])) ? $_GET['referer'] : \Ode\Manager::getInstance()->friendlyAction('index'));

        $usernameTxt = $form->addText('username')->setLabel('Username')->setAttribute('class', 'form-control');
        $usernameTxt->addRule('required', 'Required');

        $passwordTxt = $form->addPassword('password')->setLabel('Password')->setAttribute('class', 'form-control');
        $passwordTxt->addRule('required', 'Required');

        $form->addButton('submitBtn')->setContent('Log In')->setAttribute('type', 'submit')->setAttribute('class', 'btn btn-default');

        if ($form->validate()) {
            $username = trim($_POST[$usernameTxt->getName()]);
            $password = trim($_POST[$passwordTxt->getName()]);
            $referer = $_POST['referer'];

            $sth = \Ode\DBO::getInstance()->prepare("
                    SELECT a.id
                    FROM " . \Ode\DBO\User::TABLE_NAME . " AS a
                    WHERE a.username = :username
                    AND a.password = MD5(:password)
                    LIMIT 0,1
                ");
            $sth->bindParam(":username", $username, PDO::PARAM_STR, 50);
            $sth->bindParam(":password", $password, PDO::PARAM_STR, 50);

            try {
                $sth->execute();

                $result = $sth->fetchColumn();

                if ($result != false) {
                    $loginTime = new \DateTime(null, new \DateTimeZone('UTC'));

                    // log login date
                    \Ode\DBO\User::getMeta()->add($result, \Ode\DBO\User\Metadata::META_LAST_LOGIN, $loginTime->format('Y-m-d H:i:s'), true);

                    // store place name from user coordinates if not already available
                    $locationExists = \Ode\DBO\User::getMeta()->get($result, \Ode\DBO\User\Metadata::META_LOCATION_NAME, true);
                    if (!$locationExists) {
                        $user = \Ode\DBO\User::getOneById($result);
                        $address = \Ode\Geo\Google\Geocode::fromLatLngAdminLevel($user->coords());
                        $addressExists = \Ode\DBO\User::getMeta()->get($user->id, \Ode\DBO\User\Metadata::META_LOCATION_NAME, true);
                        if (empty($addressExists)) {
                            \Ode\DBO\User::getMeta()->add($user->id, \Ode\DBO\User\Metadata::META_LOCATION_NAME, $address, true);
                        }
                    }

                    \Ode\Auth::getInstance()->setSession($result);

                    header("Location:" . $referer);
                    exit();
                } else {
                    \Ode\View::getInstance()->assign('error', 'Login failed. Please, try again.');
                }
            } catch (\Exception $e) {
                \Ode\View::getInstance()->assign('error', 'Could not process authentication at this time');

                error_log($e->getTraceAsString(), E_ERROR);
            }
        }

        \Ode\View::getInstance()->assign('form', $form->render(\Ode\View::getInstance()->getFormRenderer()));

        break;
    case 'logout':
        \Ode\Auth::getInstance()->killSession();

        $referer = $_REQUEST['referer'];

        header("Location: " . $referer);
        exit();
        break;
    case 'forgot':
        switch (\Ode\Manager::getInstance()->getTask()) {
            case 'reset':
                $userId = $_GET['id'];

                $form = new HTML_QuickForm2('resetPassword');
                $form->setAttribute('action', \Ode\Manager::getInstance()->friendlyAction('auth', 'forgot', 'reset'));
                $form->setAttribute('role', 'form');

                $passwdTxt = $form->addPassword('password')->setLabel('Password')->setAttribute('class', 'form-control')->setAttribute('maxlength', 25);
                $passwdTxt->addRule('required', 'Required');
                $passwdTxt->addRule('minlength', 'Should be at least 8 characters long', 8);
                $passwdTxt->addRule('regex', 'Provide letters, numbers, hyphens, or underscores', "/^[\w\d\-\_]+$/i");

                $confTxt = $form->addPassword('confPassword')->setLabel('Confirm password')->setAttribute('class', 'form-control')->setAttribute('maxlength', 25);
                $passwdTxt->addRule('compare', 'Passwords do not match', $confTxt);

                $submitBtn = $form->addButton('submitBtn')->setContent('Update')->setAttribute('type', 'submit')->setAttribute('class', 'btn btn-default');

                if ($form->validate()) {
                    $sth = \Ode\DBO::getInstance()->prepare("
                        UPDATE users
                        SET
                          password = MD5(:password)
                        WHERE id = :id
                    ");
                    $sth->bindValue(':password', $_POST['password'], PDO::PARAM_STR, 50);
                    $sth->bindValue(':id', $userId, PDO::PARAM_STR, 50);

                    try {
                        $sth->execute();

                        header('Location: ' . \Ode\Manager::getInstance()->friendlyAction('auth', 'forgot', 'success'));
                        exit();
                    } catch (\Exception $e) {
                        error_log($e->getTraceAsString(), 0);
                    }
                }

                \Ode\View::getInstance()->assign('form', $form->render(\Ode\View::getInstance()->getFormRenderer()));
                break;
            default:
            case false:
                $form = new \HTML_QuickForm2('forgotPassword');
                $form->setAttribute('action', \Ode\Manager::getInstance()->friendlyAction('auth', 'forgot'));
                $form->setAttribute('role', 'form');

                $emailTxt = $form->addText('emailAddress')->setLabel('Email')->setAttribute('class', 'form-control');
                $emailTxt->addRule('required', 'Required');
                $emailTxt->addRule('regex', 'Not a valid email address.', QUICKFORM2_REGEX_EMAIL);

                $submitBtn = $form->addButton('submitBtn')->setContent('Send')->setAttribute('type', 'submit')->setAttribute('class', 'btn btn-default');

                if ($form->validate()) {
                    $emailExists = \Ode\DBO\User::emailExists($_POST['emailAddress']);

                    if ($emailExists === true) {
                        $user = \Ode\DBO\User::getOneByEmail($_POST['emailAddress']);

                        $mail = new \Ode\SMTP();
                        $mail->addAddress(trim($_POST['emailAddress']), $user->fullname());
                        $mail->Sender = SMTP_EMAIL;
                        $mail->setFrom(SMTP_EMAIL, SMTP_NAME);
                        $mail->Subject = 'HF Logbook account recovery';
                        $mail->isHTML(true);

                        \Ode\View::getInstance()->assign('user', $user);
                        \Ode\View::getInstance()->assign('link', APP_SITE_URL . \Ode\Manager::getInstance()->friendlyAction('auth', 'forgot', 'reset', array('id', $user->id)));
                        $mail->Body = \Ode\View::getInstance()->fetch('mail/forgot.tpl.php');

                        if (!$mail->send()) {
                            error_log($mail->ErrorInfo, 0);
                        }

                        header('Location: ' . \Ode\Manager::getInstance()->friendlyAction('auth', 'forgot', 'sent'));
                        exit();
                    }
                }

                \Ode\View::getInstance()->assign('form', $form->render(\Ode\View::getInstance()->getFormRenderer()));
                break;
        }
        break;
}
