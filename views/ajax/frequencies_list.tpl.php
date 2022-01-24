<div>
	<? if($this->groups != false): ?>
	<h2>Frequencies for <?= $this->county->name; ?>, <?= $this->county->state()->abbrev; ?></h2>
	<div>
		<? foreach($this->groups as $group): ?>
		<div>
			<h4><?= $group->title; ?></h4>
			<div>
				<? $freqs = $group->frequencies(); if(!empty($freqs)): ?>
				<table class="data">
					<thead>
						<tr>
							<th>Frequency (MHz)</th>
							<th>Mode</th>
							<th>Tone</th>
							<th>Channel tag</th>
							<th>Details</th>
						</tr>
					</thead>
					<tbody>
						<? foreach($freqs as $num => $freq): ?>
						<tr class="<?= ($num%2==0) ? "nobg" : "bg"; ?>">
							<td style="width:50px;"><?= $freq->frequency; ?></td>
							<td style="width:50px;" class="center">
								<?= $freq->mode()->title; ?>
								<?= $this->binToText($freq->is_encrypted, "", "(encrypted)"); ?>
							</td>
							<td style="width:75px;" class="center"><?= $freq->tone(); ?></td>
							<td><?= $freq->tag; ?></td>
							<td><?= $freq->description; ?></td>
						</tr>
						<? endforeach; ?>
					</tbody>
				</table>
				<? else: ?>
				<div>No frequencies.</div>
				<? endif; ?>
			</div>
		</div>
		<? endforeach; ?>
	</div>
	<? else: ?>
	<div>
		No frequencies avaialble for this county. 
		<a href="<?= $this->manager->friendlyAction("frequencies", "add", null, array("cid", $this->county->cid)); ?>">Add one</a>
	</div>
	<? endif; ?>
</div>