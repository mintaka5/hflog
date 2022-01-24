<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title>Scanner Frequency Database</title>
		<style type="text/css">
			@import '/ode/assets/styles/freqs.css';
		</style>
		<script type="text/javascript" src="/ode/assets/jquery/jquery-1.4.4.min.js"></script>
		<script type="text/javascript" src="/ode/assets/jquery/jquery-ui-1.8.7.custom.min.js"></script>
		<script type="text/javascript">
		var globals = {
			'relurl':'/ode/',
			'siteurl':'/'
		};
		globals.ajaxurl = globals.relurl + 'controllers/ajax/';
		</script>
	</head>
	<body>
	<div id="freqContainer">
			<div>
				<h2>Scanner Frequency Database</h2>
				<div style="float:left;">
					<div style="margin-bottom:10px;">
						<a href="<?= $this->manager->friendlyAction(""); ?>">Qualsh</a>
					</div>
					<div style="margin-bottom:10px;">
						<ul class="horizNav">
							<li>
								<a href="<?= $this->manager->friendlyAction("frequencies"); ?>">Home</a>
							</li>
							<? if($this->auth->isAuth()): ?>
							<li>
								<a href="<?= $this->manager->friendlyAction("frequencies", "add"); ?>">Log frequency</a>
							</li>
							<? if($this->auth->isAdmin()): ?>
							<li>
								<a href="<?= $this->manager->action("frequencies", "admin"); ?>">Admin</a>
							</li>
							<? endif; ?>
							<? endif; ?>
						</ul>
					</div>
				</div>
				<div style="float:right;">
					<? if($this->auth->isAuth()): ?>
					Welcome, <?= $this->auth->getSession()->fullname(); ?>! 
					[<a href="<?= $this->manager->friendlyAction("user"); ?>">account</a>]
					[<a href="<?= $this->manager->friendlyAction("auth", "logout"); ?>">logout</a>]
					<? else: ?>
					<?= $this->loginForm; ?>
					<? endif; ?>
				</div>
				<br style="clear:both;" />
			</div>
			
			<? if($this->manager->isMode("admin")): ?>
			<? if($this->auth->getSession()->type()->type_name == "admin"): ?>
			<? if($this->manager->isTask()): ?>
			<div>
				<? if(!empty($this->freqs)): ?>
				<div>
					<table class="data">
						<thead></thead>
						<tbody>
							<? foreach($this->freqs as $freq): ?>
							<tr>
								<td><?= $freq->frequency; ?></td>
								<td><?= $freq->tone(); ?></td>
								<td><?= $freq->county()->name; ?>, <?= $freq->county()->state; ?></td>
								<td>
									<?= $freq->user()->fullname(); ?>
									<span style="font-size:80%;">
									<br />
									<?= $freq->user()->email; ?></span>
								</td>
								<td><?= $freq->description(); ?></td>
								<td class="cellnav">
									<div>
									<ul class="tablenav">
										<li>
											<a href="#" title="Edit">edit</a>
										</li>
										<li>
											<a href="#" title="View details">view</a>
										</li>
										<li>
											<a href="<?= $this->manager->action("frequencies", "admin", "approve", array("fid", $freq->id), array("cid", $freq->county()->cid)); ?>" title="Approve">appr.</a>
										</li>
									</ul>
									</div>
								</td>
							</tr>
							<? endforeach; ?>
						</tbody>
					</table>
				</div>
				<? else: ?>
				<div>No unapproved frequencies.</div>
				<? endif; ?>
			</div>
			<? endif; ?>
			
			<? if($this->manager->isTask("approve")): ?>
			<script type="text/javascript">
				$(function() {
					$("#addGroupForm").hide();
						
					$("#clickAddGroup").toggle(function() {
						$("#addGroupForm").show("fast", function() {
							$("#clickAddGroup").html("Close form");
						});
					}, function() {
						$("#addGroupForm").hide("fast", function() {
							$("#grpTitle").val("");
							$("#grpDesc").val("");
							$("#clickAddGroup").html("New group");
						});
					});
		
					$("#grpBtn").live("click", function() {
						if($("#grpTitle").val() != "") {
							$.post(globals.ajaxurl + "groups_add.php", {
								'cid':$("#cid").val(),
								'title': $("#grpTitle").val(),
								'desc': $("#grpDesc").val()
							}, function(d) {
								if(d.status == true) {
									$("#groupList").load(globals.ajaxurl + 'admin_group_list.php', {
										'cid':d.data.county_id,
										'fid':$("#fid").val()
									});
								}
							}, 'json');
						}
					});
				});
			</script>
			<div>
				<h2>Approve logged frequency <?= $this->freq->frequency; ?></h2>
				<div id="groupList">
					<? if(!empty($this->groups)): ?>
					<div>
						<table class="data" id="groupsData">
							<thead></thead>
							<tbody>
								<? foreach($this->groups as $num => $group): ?>
								<tr class="<?= ($num%2==0) ? "nobg" : "bg"; ?>">
									<td><?= $group->title; ?></td>
									<td>
									<a href="<?= $this->manager->action("frequencies", "admin", "select", array("group_id", $group->id), array("freq_id", $this->freq->id)); ?>" title="Select this group!">select &amp; approve</a>
									</td>
								</tr>
								<? endforeach; ?>
							</tbody>
						</table>
					</div>
					<? else: ?>
					<div>No groups are avaialble for the county, <?= $this->freq->county()->name; ?></div>
					<? endif; ?>
				</div>
				<div>
					<div>
						<a href="javascript:void(0);" id="clickAddGroup" title="Add new group!">New group</a>
					</div>
					<div id="addGroupForm">
						<input name="cid" id="cid" value="<?= $this->freq->county()->cid; ?>" type="hidden" />
						<input name="fid" id="fid" value="<?= $this->freq->id; ?>" type="hidden" />
						<div>
							<div>Title:</div>
							<div>
								<input type="text" name="grpTitle" id="grpTitle" />
							</div>
						</div>
						<div>
							<div>Description</div>
							<div>
								<textarea name="grpDesc" id="grpDesc"></textarea>
							</div>
						</div>
						<div>
							<button name="grpBtn" id="grpBtn">Add</button>
						</div>
					</div>
				</div>
			</div>
			<? endif; ?>
			
			<? endif; ?>
			<? endif; ?>
			
			<? if($this->manager->isMode("search")): ?>
			<div style="">
				<div style="float:right;">
					<form method="post" action="<?= $this->manager->friendlyAction("frequencies", "search"); ?>">
						<div>
							<label for="freq" style="font-weight:bold;">Frequency search:</label>
							<input type="text" name="freq" />
							<input type="submit" name="submitBtn" value="Search" />
						</div>
					</form>
				</div>
				<div style="clear:both; padding-top:10px;">
					<? if(!empty($this->freqs)): ?>
					<div style="">
						<table class="data">
							<thead>
								<tr>
									<th>County, State</th>
									<th>Group</th>
									<th>Frequency (MHz)</th>
									<th>Mode</th>
									<th>Tone</th>
									<th>Radio Tag</th>
									<th>Description</th>
								</tr>
							</thead>
							<tbody>
								<? foreach($this->freqs as $num => $freq): ?>
								<tr class="<?= ($num%2==0) ? "nobg" : "bg"; ?>">
									<td><?= $freq->county()->name; ?>, <?= $freq->county()->state()->name; ?></td>
									<td><?= $freq->group()->title(); ?></td>
									<td style="width:75px;"><?= $freq->frequency; ?></td>
									<td style="width:50px;" class="center"><?= $freq->mode()->title; ?></td>
									<td style="width:75px;" class="center"><?= $freq->tone(); ?></td>
									<td><?= $freq->tag; ?></td>
									<td><?= $freq->description(); ?></td>
								</tr>
								<? endforeach; ?>
							</tbody>
						</table>
					</div>
					<? else: ?>
					<div>No results</div>
					<? endif; ?>
				</div>
			</div>
			<? endif; ?>
		
			<? if($this->manager->isMode()): ?>
			<script type="text/javascript">
				$(function() {
					$("#selState").change(function() {
						$.post(globals.relurl + 'controllers/ajax/counties_select.php', {
							's':$("#selState").val()
						}, function(data) {
							$("#lyrCounties").html(data);
						}, "html");
					});
		
					$("#selCounties").live("change", function() {
						$.post(globals.relurl + 'controllers/ajax/frequencies_list.php', {
							'c': $("#selCounties").val()
						}, function(data) {
							$("#lyrFrequencies").html(data);
						}, "html");
					});
				});
			</script>
			<div>
				<div style="float:left;">
					<div class="formElement">
						<label for="selState" style="font-weight:bold; margin-bottom:10px;">Search by State &gt; County</label>
						<div class="input">
							<select name="selState" id="selState">
								<option value="">- select -</option>
								<? foreach($this->states as $state): ?>
								<option value="<?= $state->abbrev; ?>"><?= $state->name; ?> - <?= $state->abbrev; ?></option>
								<? endforeach; ?>
							</select>
						</div>
					</div>
				</div>
				<div id="lyrCounties" style="float:left; margin-left:10px;"></div>
				<div style="float:right; position:relative; top:14px;">
					<form method="post" action="<?= $this->manager->friendlyAction("frequencies", "search"); ?>">
						<div>
							<label for="freq" style="font-weight:bold;">Frequency search:</label>
							<input type="text" name="freq" />
							<input type="submit" name="submitBtn" value="Search" />
						</div>
					</form>
				</div>
				<br style="clear:left;" />
				<div style="float:left; width:550px; margin-right:20px;">
					<h2>Recent Frequencies (past 7 days)</h2>
					<? if(!empty($this->recents)): ?>
					<div>
						<? foreach($this->recents as $recent): ?>
						<h3><?= $recent['county']->name; ?>, <?= $recent['county']->state()->abbrev; ?></h3>
						<div>
							<? foreach($recent['groups'] as $group): ?>
							<h4><?= $group['group']->title; ?></h4>
							<div>
								<table class="data">
									<thead>
										<tr>
											<th>Frequency (MHz)</th>
											<th>Mode</th>
											<th>Tone</th>
											<th>Radio Tag</th>
											<th>Description</th>
										</tr>
									</thead>
									<tbody>
										<? foreach($group['freqs'] as $num => $freq): ?>
										<tr class="<?= ($num%2==0) ? "nobg" : "bg"; ?>">
											<td style="width:75px;"><?= $freq->frequency; ?></td>
											<td style="width:50px;" class="center"><?= $freq->mode()->title; ?></td>
											<td style="width:75px;" class="center"><?= $freq->tone(); ?></td>
											<td><?= $freq->tag; ?></td>
											<td><?= $freq->description(); ?></td>
										</tr>
										<? endforeach; ?>
									</tbody>
								</table>
							</div>
							<? endforeach; ?>
						</div>
						<? endforeach; ?>
					</div>
					<? else: ?>
					<div>No recent frequencies available.</div>
					<? endif; ?>
				</div>
				<div id="lyrFrequencies" style="float:left; width:550px;"></div>
			</div>
			<? endif; ?>
			
			<? if($this->manager->isMode("add")): ?>
			<? if($this->auth->isAuth()): ?>
			<? if($this->manager->isTask("multi")): ?>
			<div>
				<form method="post" action="<?= $this->manager->friendlyAction("frequencies", "multi"); ?>">
					<? for($i=0; $i<trim($_GET['num']); $i++): ?>
					<div class="freqRow">
						<div class="field">
							<div class="lbl">Frequency</div>
							<div class="ele">
								<input type="text" name="row[]['freq']" />
							</div>
						</div>
						<div class="field">
							<div class="lbl">Alpha Tag</div>
							<div class="ele">
								<input type="text" name="row[]['tag']" />
							</div>
						</div>
						<div class="field">
							<div class="lbl">CTCSS tone</div>
							<div class="ele">
								<select name="row[]['ctcss']">
									<option value="">n/a</option>
									<? foreach($this->ctcss as $ctcss): ?>
									<option value="<?= $ctcss->id; ?>"><?= $ctcss->hertz; ?></option>
									<? endforeach; ?>
								</select>
							</div>
						</div>
						<div class="field">
							<div class="lbl">DCS code</div>
							<div class="ele">
								<select name="row[]['dcs']">
									<option value="">n/a</option>
									<? foreach($this->dcs as $dcs): ?>
									<option value="<?= $dcs->id; ?>"><?= $dcs->dcs; ?></option>
									<? endforeach; ?>
								</select>
							</div>
						</div>
					</div>
					<? endfor; ?>
				</form>
			</div>
			<? endif;?>
			
			<? if($this->manager->isTask()): ?>
			<script type="text/javascript">
				$(function() {
					if($("#state").val() != "") {
						$("#county").empty();
						$('<option />').val('').text('- select -').appendTo("#county");
						
						$.post(globals.ajaxurl + 'counties_select.php', {
							's': $("#state").val(), 
							'_mode':'json'
						}, function(data) {
							$(data).each(function(i, v) {
								//console.log(v);
								$('<option />').val(v.cid).text(v.name).appendTo("#county");
							});
						}, "json");
					}
					
					$("#state").live("change", function() {
						$("#county").empty();
						$('<option />').val('').text('- select -').appendTo("#county");
						
						$.post(globals.ajaxurl + 'counties_select.php', {
							's': $("#state").val(), 
							'_mode':'json'
						}, function(data) {
							$(data).each(function(i, v) {
								//console.log(v);
								$('<option />').val(v.cid).text(v.name).appendTo("#county");
							});
						}, "json");
					});
		
					$("#county").live("change", function() {
						$.post(globals.ajaxurl + 'counties_select.php', {
							'c': $("#county").val(),
							'_mode':'session'
						}, function(data) {
							console.log(data);
							window.location.href = globals.siteurl + 'frequencies/add/info';
						}, 'json');
					});
				});
			</script>
			<div>
				<div>
					<div class="formElement">
						<label for="state">Select a state!</label>
						<div class="input">
							<select name="state" id="state">
								<option>- select -</option>
								<? foreach($this->states as $state): ?>
								<option value="<?= $state->abbrev; ?>"><?= $state->name; ?></option>
								<? endforeach; ?>
							</select>
						</div>
					</div>
					<div class="formElement">
						<label for="county">Select a county</label>
						<div class="input">
							<select name="county" id="county"></select>
						</div>
					</div>
				</div>
			</div>
			<? endif; ?>
			
			<? if($this->manager->isTask("info")): ?>
			<div><?= $this->form; ?></div>
			<? endif; ?>
			<? endif; // end Auth check ?>
			<? endif; ?>
		</div>
	</body>
</html>