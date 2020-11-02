<h2><?php esc_html_e( 'Hide Xtra Menu Settings' ); ?></h2>
<p><?php esc_html_e( 'Select menu item to always show' ); ?></p>
<form method="post" name="save_settings">
	<?php wp_nonce_field( 'save-settings' ); ?>
	<input type="hidden" name="always_show" value='<?php echo implode(',', $settings['always_show']) ?>'>
	<table class="form-table" role="presentation">
		<tr>
			<?php
			// echo "<pre>";
			// var_dump($settings['change_wp_logo']);
			// exit();
			  ?>
			<th scope="row"><label><?php esc_html_e( 'Show/Hide' ); ?> </label></th>
			<td><div id="always_show_xtra"></div></td>
		</tr>
		<tr>
			<th scope="row"><label for="wp_xtra_logo"><?php esc_html_e( 'Update logo on wp login' ); ?> </label></th>
			<td>
				<input name="wp_xtra_logo" id="wp_xtra_logo" type="url" value='<?php echo $settings_logo; ?>' /> 
				<p class="description">
					<?php esc_html_e( 'Copy your logo link from wordpress media and paste it here' ); ?>
				</p>
			</td>
		</tr>
	</table>
	<p class="submit">
		<?php submit_button( __( 'Save settings' ), 'primary', 'save_settings', false ); ?>
	</p>
</form>