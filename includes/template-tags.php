<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function ss_purchase_code_form() {
	?>
		<form action="<?php the_permalink(); ?>" method="post" id="ss-envato-verify-form" name="ss-envato-verify-form">
			<h4><?php _e('Envato Purchase Code') ?></h4>
			<p><?php _e('Enter the purchase code to access the latest plugins updates.') ?></p>
			<input type="text" id="ss-envato-license" name="ss-envato-license"/>
			<input name="submit" type="submit" value="Submit" />
			<div class="clear"></div>
		</form>
	<?php
}