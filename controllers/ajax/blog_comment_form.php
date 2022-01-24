<?php
require_once './init.php';

switch(\Ode\Manager::getInstance()->getMode()) {
	default:
		$form = new HTML_QuickForm2("commentForm");
		//$form->setAttribute("id", "commentForm");
		$form->setAttribute("action", "javascript:void(0);");
		
		$postId = $form->addHidden("pid")->setAttribute("id", "pid")->setValue($_POST['pid']);
		
		$nameTxt = $form->addText("nameTxt")->setLabel("Name");
		$nameTxt->addRule("required", "Required");
		$nameTxt->addRule("minlength", "Must be at least 3 characters", 3);
		$nameTxt->addRule("maxlength", "Must be under 50 characters", 50);
		
		$emailTxt = $form->addText("emailTxt")->setLabel("Email");
		$emailTxt->addRule("required", "Required");
		$emailTxt->addRule("regex", "Not a valid email address", QUICKFORM2_REGEX_EMAIL);
		
		$msgTxt = $form->addTextarea("msgTxt")->setLabel("Comment");
		$msgTxt->setAttribute("rows", 10);
		$msgTxt->setAttribute("cols", 70);
		$msgTxt->setAttribute("id", "msgTxt");
		$msgTxt->addRule("required", "Required");
		$msgTxt->addRule("minlength", "Must be at least 10 characters", 10);
		$msgTxt->addRule("maxlength", "Too long", 1600);
		
		//$form->addSubmit("submitBtn")->setAttribute("value", "Send");
		$form->addButton("submitBtn")->setAttribute("id", "cfSubmit")->setContent("Send");
		
		if($form->validate()) {
			//Util::debug($_POST);
			
			$sth = \Ode\DBO::getInstance()->prepare("
				INSERT INTO db125612_blog.wp_comments (
					comment_post_ID, comment_author, comment_author_email, comment_author_IP,
					comment_date, comment_date_gmt, comment_content, comment_approved 
				)
				VALUES (
					:post_ID, :author, :author_email, :author_IP,
					NOW(), UTC_TIMESTAMP(), :content, 0
				)
			");
			$sth->bindValue(":post_ID", $_POST['pid'], PDO::PARAM_INT);
			$sth->bindValue(":author", trim($_POST['nameTxt']), PDO::PARAM_STR);
			$sth->bindValue(":author_email", trim($_POST['emailTxt']), PDO::PARAM_STR);
			$sth->bindValue(":author_IP", $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
			$sth->bindValue(":content", trim($_POST['msgTxt']), PDO::PARAM_STR);
			
			try {
				$sth->execute();
			} catch(PDOException $e) {
				error_log($e->getTraceAsString(), 0);
			}
			
			echo "Comment has been submitted for review. Thanks for submitting!";
			exit();
		}
		
		$renderer = \HTML_QuickForm2_Renderer::factory("default");
		\Ode\View::getInstance()->assign("form", $form->render($renderer));
		
		header("Content-Type: text/html");
		echo \Ode\View::getInstance()->fetch("ajax/blog_comment_form.tpl.php");
		exit();
		break;
}
?>