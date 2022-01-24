<nav class="section box">
	<h3 class="arrowhead">Search blog<mark></mark></h3>
	<div style="padding:10px;">
		<form method="get" action="<?= $this->manager->friendlyAction("blog", "search"); ?>">
			<input type="text" name="qry" id="qry" />
			<input type="submit" value="Find" />
		</form>
	</div>
</nav>