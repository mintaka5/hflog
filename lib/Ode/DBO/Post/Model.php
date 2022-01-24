<?php
namespace Ode\DBO\Post;

class Model {
	public $ID;
	public $post_author;
	public $post_date;
	public $post_date_gmt;
	public $post_content;
	public $post_title;
	public $post_excerpt;
	public $post_status;
	public $comment_status;
	public $ping_status;
	public $post_password;
	public $post_name;
	public $to_ping;
	public $pinged;
	public $post_modified;
	public $post_modified_gmt;
	public $post_post_content_filtered;
	public $post_parent;
	public $guid;
	public $menu_order;
	public $post_type;
	public $post_mime_type;
	public $comment_count;
	
	public function tags() {
		return Ode_DBO_Term::getAllByPost($this->ID);
	}
	
	public function tagList($links = true) {
		$tags = $this->tags();
		$tagAry = array();
		foreach($tags as $tag) {
			$str = ($links == true) ? '<a href="#" rel="category tag" title="' . $tag->post()->post_title . '">'.$tag->name.'</a>' : $tag->name;
			
			$tagAry[] =  $str;
		}
		
		return implode(", ", $tagAry);
	}
	
	public function content() {
		$str = preg_replace("/[\n\r]+/", "<br /><br />", $this->post_content);
		
		return $str;
	}
	
	public function comments() {
		return Ode_DBO_Comment::getAllByPost($this->ID);
	}
}