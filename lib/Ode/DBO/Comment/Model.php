<?php
namespace Ode\DBO\Comment;

class Model {
	public $comment_ID;
	public $comment_post_ID;
	public $comment_author;
	public $comment_author_email;
	public $comment_author_url;
	public $comment_author_IP;
	public $comment_date;
	public $comment_date_gmt;
	public $comment_content;
	public $comment_karma;
	public $comment_approved;
	public $comment_agent;
	public $comment_type;
	public $comment_parent;
	public $user_id;
	
	public function content() {
		return iconv("UTF-8", "ISO-8859-1", $this->comment_content);
	}
}