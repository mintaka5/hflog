<?php if ($this->manager->isMode('thanks')): ?>
    <h2>Thanks, for your contribution</h2>
    <div>
        A &#36;5 USD contribution has been made to the site, in order to maintain servers and the application itself. Your pro account
        will be activated immediately upon the site owner (myself) confirming the contribution transaction. If you have any questions or concerns
        do not hesitate to contact me, using your transaction ID: <em><?php echo $this->transaction_id; ?></em>.
    </div>
<?php endif; ?>

<?php if ($this->manager->isMode()): ?>
    <form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post">
        <input type="hidden" name="cmd" value="_xclick" />
        <input type="hidden" name="at" value="<?php echo PAYPAL_SANDBOX_ID_TOKEN ?>" />
        <input type="hidden" name="business" value="cjwalsh-facilitator@ymail.com" />
        <input type="hidden" name="item_name" value="HF Logbook Donation (http://apps.qualsh.com/hflogs)" />
        <input type="hidden" name="item_number" value="HFLD0001" />
        <input type="hidden" name="no_shipping" value="1" />
        <input type="hidden" name="no_note" value="1" />
        <input type="hidden" name="currency_code" value="USD" />
        <input type="hidden" name="lc" value="AU" />
        <input type="hidden" name="bn" value="PP-BuyNowBF" />
        <!-- <input type="hidden" name="return" value="<?php //echo APP_SITE_URL; ?>" /> -->
        <div id="donateDesc">We need to keep the servers running. Thank you for your help.</div>
        
        <div id="donateForm">
            Amount (in USD): 
               <!-- <select name="amount">
                    <option value="1.00">&#36;1.00</option>
                    <option value="1.00">&#36;5.00</option>
                    <option value="1.00">&#36;10.00</option>
                    <option value="1.00">&#36;20.00</option>
                </select> -->
            <input type="hidden" name="amount" value="5.00" />
            &#36;5.00
            <input type="submit" class="button" value="Donate" />
        </div>
    </form>
<?php endif; ?>
