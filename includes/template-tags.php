<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( ! function_exists( 'ss_purchase_code_form' ) ) {
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
}

if( ! function_exists( 'ss_show_purchase_codes' ) ) {
	function ss_show_purchase_codes() {
	?>
		<div class="themes-from-themeforest">
	        <?php

	        	$codes = SS_Envato_API()->purchase_repo->get_user_codes();
	        	//pr($codes);
	        	if(count($codes) > 0) {
	        		?>
						<h4><?php _e('Your Purchase Codes', 'ss-envato-api'); ?></h4>
	        		<?php
	        		for ($i=0; $i < count($codes); $i++) { 
	        			$return = json_decode($codes[$i]['api_response'], true);
	        			echo '<div class="envato-license">' . $return['item']['name'] . '<br>';
	        			echo 'code: <strong>' . $codes[$i]['purchase_code'] . '</strong>';
	        			echo '<br><a href="#" class="remove-purchase-code" data-id="' . $codes[$i]['id'] . '">remove</a></div>';
	        		}
	        	}
	        ?>
        </div>
	<?php
	}
}