<? if(!empty($this->groups)): ?>
<div>
	<table class="data" id="groupsData">
		<thead></thead>
		<tbody>
			<? foreach($this->groups as $num => $group): ?>
			<tr class="<?= ($num%2==0) ? "nobg" : "bg"; ?>">
				<td><?= $group->title; ?></td>
				<td>
				<a href="<?= $this->manager->action("frequencies", "admin", "select", array("freq_id", $this->freq_id), array("group_id", $group->id)); ?>" title="Select this group!">select &amp; approve</a>
				</td>
			</tr>
			<? endforeach; ?>
		</tbody>
	</table>
</div>
<? else: ?>
<div>No groups are avaialble for the county, <?= $this->county->name; ?></div>
<? endif; ?>
