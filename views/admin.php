<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   Gallery_WP
 * @author    binaryhideout <m@binaryhideout.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2013 binaryhideout
 */
?>
<div class="wrap">

	<?php screen_icon(); ?>
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<form method="post" action="options.php">
		<?php
		settings_fields( $this->plugin_slug );
		do_settings_sections( $this->plugin_screen_hook_suffix );
		submit_button( __( 'Save Changes', $this->plugin_slug ) );
		?>
	</form>
</div>
