<div class="formElement">
	<label for="selCounties">Select a county!</label>
	<div class="input">
		<select id="selCounties" name="selCounties">
			<option>- select -</option>
			<? foreach($this->counties as $county): ?>
			<option value="<?= $county->cid; ?>"><?= $county->name; ?></option>
			<? endforeach; ?>
		</select>
	</div>
</div>