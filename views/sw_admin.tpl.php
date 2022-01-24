<html>
	<head>
		<title>Shortwave Admin</title>
	</head>
	<body>
		<div>
			<? if($this->manager->isMode()): ?>
			<div>
				<? if($this->manager->isTask()): ?>
				<div>
					
				</div>
				<? endif; ?>
			</div>
			<? endif; ?>
			
			<? if($this->manager->isMode("add")): ?>
			<div>
				<? if($this->manager->isTask()): ?>
				<div><?= $this->form; ?></div>
				<? endif; ?>
			</div>
			<? endif; ?>
		</div>
	</body>
</html>