<div id="socializer" class="">
	<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
            <input type="hidden" name="cmd" value="_xclick" />
	    <input type="hidden" name="business" value="cjwalsh@ymail.com" />
	    <input type="hidden" name="item_name" value="HF Logbook Donation" />
	    <input type="hidden" name="item_number" value="<?php echo UUID::get(); ?>" />
	    <input type="hidden" name="no_shipping" value="1" />
	    <input type="hidden" name="no_note" value="1" />
	    <input type="hidden" name="currency_code" value="USD" />
	    <input type="hidden" name="lc" value="AU" />
	    <input type="hidden" name="bn" value="PP-BuyNowBF" />
	    <input type="hidden" name="return" value="<?php echo APP_SITE_URL; ?>" />
	    <div id="donateDesc">Servers aren't free! Thank you =).</div>

	    <div id="donateForm">
	    	Amount (in USD):
                    <input type="hidden" name="amount" value="5.00" />
                    &#36;5.00
                    <button class="btn btn-sm" type="submit">Donate</button>
	    </div>
	</form>
</div>
