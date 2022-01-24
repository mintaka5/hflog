<?php
/**
 * Don't let authorized users into this page
 */
if (\Ode\Auth::getInstance()->isAuth()) {
    header('Location: ' . \Ode\Manager::getInstance()->friendlyAction('user'));
    exit();
}

switch (\Ode\Manager::getInstance()->getMode()) {
    case 'activate':
        $user_id = trim($_GET['id']);

        $odeUser = \Ode\DBO\User::getOneById($user_id);

        if ($odeUser != false) {
            if ($odeUser->is_deleted == 1) {
                $sth = \Ode\DBO::getInstance()->prepare("
							UPDATE " . \Ode\DBO\User::TABLE_NAME . "
							SET is_deleted = 0,
								modified = NOW()
							WHERE id = :id
						");
                $sth->bindParam(":id", $odeUser->id, PDO::PARAM_STR, 50);

                try {
                    $sth->execute();

                    /*require_once 'ForumBridge.php';
                    bbLogin($odeUser, $password);*/
                    \Ode\Auth::getInstance()->setSession($odeUser->id);

                    header("Location: " . \Ode\Manager::getInstance()->friendlyAction("index"));
                    exit();
                } catch (\PDOException $e) {
                    error_log($e->getMessage(), 0);
                } catch (\Exception $e) {
                    error_log($e->getMessage(), 0);
                }
            }
        }

        header("Location: " . \Ode\Manager::getInstance()->friendlyAction("index"));
        exit();
        break;
    case false:
    default:
        $form = new HTML_QuickForm2("registerForm");
        $form->setAttribute("action", \Ode\Manager::getInstance()->friendlyAction("register"));
        $form->setAttribute('role', 'form');

        $usernameTxt = $form->addText('usernameTxt')->setLabel('Username')->setAttribute('class', 'form-control');
        $usernameTxt->setAttribute('maxlength', 15);
        $usernameTxt->addRule('required', 'Required');
        $usernameTxt->addRule('regex', 'Only provide alphanumeric characters, hyphens, or underscores', "/^[A-Za-z\d_-]+$/");
        $usernameTxt->addRule('minlength', 'Shoule be at least 4 characters long.', 4);
        $usernameTxt->addRule('callback', 'Username is already in use', array('Validation', 'usernameExists'));

        $emailTxt = $form->addText("emailTxt")->setLabel("Email")->setAttribute('class', 'form-control');
        $emailTxt->addRule("required", "Required");
        $emailTxt->addRule("regex", "Not a valid email", QUICKFORM2_REGEX_EMAIL);
        $emailTxt->addRule("callback", "Email is in use", array("Validation", "emailExists"));

        $passTxt = $form->addPassword("passTxt")->setLabel("Password")->setAttribute('class', 'form-control');
        $passTxt->setAttribute("maxlength", 25);
        $passTxt->addRule("required", "Required");
        $passTxt->addRule("minlength", "Should be at least 8 characters long.", 8);
        $passTxt->addRule('regex', 'Provide letters, numbers, hyphens, or underscores', "/^[\w\d\-\_]+$/i");

        $confPassTxt = $form->addPassword("confPassTxt")->setLabel("Confirm password")->setAttribute('class', 'form-control');
        $confPassTxt->setAttribute("maxlength", 25);
        $passTxt->addRule("compare", "passwords do not match", $confPassTxt);

        $fnameTxt = $form->addText("fnameTxt")->setLabel("First name")->setAttribute('class', 'form-control');
        $fnameTxt->setAttribute("maxlength", 25);
        $fnameTxt->addRule("required", "Required");
        $fnameTxt->addRule("minlength", "Must be at least 2 characters long.", 2);
        $fnameTxt->addRule('regex', 'Only provide alphanumeric characters, hyphens, or underscores', "/^[A-Za-z\d_-]+$/");

        $lnameTxt = $form->addText("lnameTxt")->setLabel("Last name")->setAttribute('class', 'form-control');
        $lnameTxt->setAttribute("maxlength", 25);
        $lnameTxt->addRule("required", "Required");
        $lnameTxt->addRule("minlength", "Must be at least 2 characters long.", 2);
        $lnameTxt->addRule('regex', 'Only provide alphanumeric characters, hyphens, or underscores', "/^[A-Za-z\d_-]+$/");

        $latTxt = $form->addText('latHdn')->setLabel('Your Latitude')->setAttribute('id', 'latHdn')->setAttribute('readonly', 'readonly')->setAttribute('class', 'form-control');
        $lngTxt = $form->addText("lngHdn")->setLabel('Your Longitude')->setAttribute("id", "lngHdn")->setAttribute('readonly', 'readonly')->setAttribute('class', 'form-control');
        $latTxt->addRule('required', 'Provide a location');
        $latTxt->addRule('regex', 'Not a valid latitude.', "/\-?[0-9]{1,2}\.[0-9]{4}/");
        $lngTxt->addRule('required', 'Provide a location');
        $lngTxt->addRule('regex', 'Not a valid longitude.', "/\-?[0-9]{1,3}\.[0-9]{4}/");

        $agreeCheck = $form->addCheckbox('agree')->setContent('By checking this box, you agree to our site policies and terms, as well as give site owners permission to add you to the mailing list.');
        $agreeCheck->addRule('required', 'Agreements must be made.');

        $submitBtn = $form->addButton("submitBtn")->setContent("Register")->setAttribute('type', 'submit')->setAttribute('class', 'btn btn-default');

        if ($form->validate()) {
            $sth = \Ode\DBO::getInstance()->prepare("
							INSERT INTO users (id, username, email, password, firstname, lastname, lat, lng, is_deleted, created, modified)
							VALUES (UUID(), :username, :email, MD5(:passwd), :fname, :lname, :lat, :lng, 1, NOW(), NOW())
							");
            $sth->bindValue(":username", trim($_POST['usernameTxt']), PDO::PARAM_STR);
            $sth->bindValue(":email", trim($_POST['emailTxt']), PDO::PARAM_STR);
            $sth->bindValue(":passwd", trim($_POST['passTxt']), PDO::PARAM_STR);
            $sth->bindValue(":fname", trim($_POST['fnameTxt']), PDO::PARAM_STR);
            $sth->bindValue(":lname", trim($_POST['lnameTxt']), PDO::PARAM_STR);
            $sth->bindValue(":lat", $_POST['latHdn'], PDO::PARAM_STR);
            $sth->bindValue(":lng", $_POST['lngHdn'], PDO::PARAM_STR);

            try {
                $sth->execute();

                $mail = new \Ode\SMTP();
                $mail->addAddress($_POST['emailTxt'], $_POST['fnameTxt'] . ' ' . $_POST['lnameTxt']);
                $mail->Sender = SMTP_EMAIL;
                $mail->setFrom(SMTP_EMAIL, SMTP_NAME);
                $mail->Subject = 'HF Logbook registration confirmation';
                $mail->isHTML(true);

                $newuser = \Ode\DBO::getInstance()->query("
								SELECT a.*
								FROM users AS a
								WHERE a.email = " . \Ode\DBO::getInstance()->quote(trim($_POST['emailTxt']), PDO::PARAM_STR) . "
								LIMIT 0,1
								")->fetchObject(\Ode\DBO\User::MODEL_NAME);

                \Ode\View::getInstance()->assign("user", $newuser);
                \Ode\View::getInstance()->assign("url", 'http://' . APP_DOMAIN . \Ode\Manager::getInstance()->friendlyAction("register", "activate", null, array("id", $newuser->id)));
                $mail->Body = \Ode\View::getInstance()->fetch("mail/register_conf.tpl.php");

                if (!$mail->send()) {
                    error_log($mail->ErrorInfo, 0);
                }

                $mailMe = new \Ode\SMTP();
                $mailMe->addAddress(SMTP_EMAIL, SMTP_NAME);
                $mailMe->Sender = $newuser->email;
                $mailMe->setFrom($newuser->email, $newuser->fullname());
                $mailMe->Subject = 'HF Logbook New User Registration Request';
                $mailMe->isHTML(false);
                $mailMe->Body = 'A new user with the email, ' . trim($_POST['emailTxt']) . ', user ID, [' . $newuser->id . '], has registered with the site.';

                if (!$mailMe->send()) {
                    error_log($mailMe->ErrorInfo, 0);
                }

                header("Location: " . \Ode\Manager::getInstance()->friendlyAction("register", "success"));
                exit();
            } catch (\Exception $e) {
                //Ode_Log::getInstance()->log($e->getTraceAsString(), E_ERROR);
                error_log($e->getTraceAsString(), 0);
            }
        }

        \Ode\View::getInstance()->assign("form", $form->render(\Ode\View::getInstance()->getFormRenderer()));
        break;
}