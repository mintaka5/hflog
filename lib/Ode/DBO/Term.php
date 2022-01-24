<?php
namespace Ode\DBO;

class Term {
	public static function getAllByPost($post_id) {
		return \Ode\DBO::getInstance()->query("
			SELECT
				a.name AS name,
				c.object_id
			FROM db125612_blog.wp_term_taxonomy AS b
			LEFT JOIN db125612_blog.wp_terms AS a ON (a.term_id = b.term_id)
			LEFT JOIN db125612_blog.wp_term_relationships AS c ON (c.term_taxonomy_id = b.term_taxonomy_id)
			LEFT JOIN db125612_blog.wp_posts AS d ON (d.ID = c.object_id)
			WHERE b.taxonomy = 'category'
			AND c.object_id IS NOT NULL
			AND d.ID = " . \Ode\DBO::getInstance()->quote($post_id, PDO::PARAM_INT) . "
			ORDER BY a.name
			ASC
		")->fetchAll(PDO::FETCH_CLASS, "Ode_DBO_Term_Model");
	}
}