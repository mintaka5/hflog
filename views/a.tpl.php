<html>
	<head>
		<title></title>
		<script type="text/javascript" src="<?= APP_ASSETS_URL; ?>assets/jquery/jquery.masonry.min.js"></script>
		<script type="text/javascript">
			$(function() {
				$('#container').masonry({
					'itemSelector':'.item',
					'columnWidth':220,
					'isFitWidth':true
				});
			});
		</script>
		<style type="text/css">
			.item {
				margin:10px;
				float:left;
				border:1px solid #bfbfbf;
				
			}
			
			#container {
				margin-left:auto;
				margin-right:auto;
				width:970px;
			}
		</style>
	</head>
	<body>
		<div id="container">
			<div class="item" style="width:100%;">
				<h2>HF/SWL Log</h2>
			</div>
			<div class="item" style="" id="hfTable"></div>
			<div class="item" style="" id="">
				<div id="hfMap" style="width:400px; height:350px;"></div>
			</div>
		</div>
	</body>
</html>