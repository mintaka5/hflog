<?php
@define("QUALSH_CONTACT_EMAIL", "kj6bbs@gmail.com");
@define("QUALSH_CONTACT_NAME", "Chris Walsh");

switch(\Ode\Manager::getInstance()->getMode()) {
	default:
		$form = new HTML_QuickForm2("contactForm");
		$form->setAttribute("action", \Ode\Manager::getInstance()->friendlyAction("contact"));
                $form->setAttribute('role', 'form');
		
		$nameTxt = $form->addText("fullname")->setLabel("Your name")->setAttribute('class', 'form-control');
		$nameTxt->addRule("required", "Required");
		$nameTxt->setAttribute("maxlength", 50);
		
                if(\Ode\Auth::getInstance()->isLoggedIn()) { // if user is registered and logged in use their registered email
                    $form->addHidden("email")->setValue(\Ode\Auth::getInstance()->getSession()->email);
                } else {
                    $emailTxt = $form->addText("email")->setLabel("Email address")->setAttribute('class', 'form-control');
                    $emailTxt->addRule("required", "Required");
                    $emailTxt->addRule("regex", "Not a valid email", QUICKFORM2_REGEX_EMAIL);
                }
		
		$subjTxt = $form->addText("subj")->setLabel("Subject")->setAttribute('class', 'form-control');
		$subjTxt->setAttribute("maxlength", 100);
		
		$msgArea = $form->addTextarea("msg")->setLabel("Message")->setAttribute('class', 'form-control');
		$msgArea->addRule("required", "Required");
		$msgArea->addRule("minlength", "Must be at least 50 characters", 50);
		$msgArea->addRule("maxlength", "Message is way too long.", 3600);
		$msgArea->setAttribute("rows", 10);
		$msgArea->setAttribute("cols", 75);
		
		$htmlChk = $form->addCheckbox("sendHtml")->setContent("Send HTML formatted email?");
		
        $submitBtn = $form->addButton("submitBtn")->setContent('Send')->setAttribute('type', 'submit')->setAttribute('class', 'btn btn-default');
		
		if($form->validate()) {
			$mail1 = new \Ode\SMTP();
			$mail1->addAddress(SMTP_EMAIL, SMTP_NAME);
			$mail1->setFrom(trim($_POST['email']), trim($_POST['fullname']));
            $mail1->Sender = trim($_POST['email']);
			$mail1->Subject = trim($_POST['subj']);
			$mail1->Body = trim($_POST['msg']);
			
			$mail1->send();
			
			$mail2 = new \Ode\SMTP();
			$mail2->addAddress(trim($_POST['email']), trim($_POST['fullname']));
			$mail2->setFrom(SMTP_EMAIL, SMTP_NAME);
            $mail2->Sender = SMTP_EMAIL;
			$mail2->Subject = 'Thanks for contacting HF Logbook';
			
			if(isset($_POST['sendHtml'])) {
				$mail2->isHtml(true);
				\Ode\View::getInstance()->assign("fullname", trim($_POST['fullname']));
				$mail2->Body = \Ode\View::getInstance()->fetch("mail/contact_conf.tpl.php");
			} else {
				$mail2->Body = "Dear " . trim($_POST['fullname']) . "\n\nThanks, for contacting me. I will try to respond to your question as soon as possible.\n\nBest regards,\n\nChris Walsh, KJ6BBS";
			}
			
			$mail2->send();
			
			header("Location: " . \Ode\Manager::getInstance()->friendlyAction("contact", "sent"));
			exit();
		}
		
		\Ode\View::getInstance()->assign("form", $form->render(\Ode\View::getInstance()->getFormRenderer()));
		break;
	case 'sent':
		
		break;
	case 'paypal':
		switch(\Ode\Manager::getInstance()->getTask()) {
			default: break;
			case 'thanks':
				
				break;
		}
		break;
}
?>
