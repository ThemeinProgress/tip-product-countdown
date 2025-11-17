<?php
 
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if( !class_exists( 'tip_product_countdown_admin_notice' ) ) {

	class tip_product_countdown_admin_notice {
	
		/**
		 * Constructor
		 */
		 
		public function __construct( $fields = array() ) {

			global $pagenow;

			$user_id = get_current_user_id();

			$dismissed = get_user_meta( $user_id, 'tip_product_countdown_dismissed_notice_userid_' . $user_id, true );
			$is_upsell = ( $pagenow === 'options-general.php' && filter_input( INPUT_GET, 'page', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) === 'tip_product_countdown_upsell_panel' );

			if ( !$dismissed && !$is_upsell ) {
				add_action( 'admin_notices', array( $this, 'admin_notice' ) );
				add_action( 'admin_head', array( $this, 'dismiss' ) );
			}

			if ( !$dismissed || $is_upsell ) {
				add_action( 'admin_init', array( $this, 'admin_scripts' ), 11 );
			}

		}

		/**
		 * Admin Scripts
		 */

		public function admin_scripts() {

			global $wp_version, $pagenow;

			$file_dir = TIP_PRODUCT_COUNTDOWN_FOLDER . '/core/assets/css/';
			wp_enqueue_style ( 'tip-product-countdown-upsell', $file_dir . 'upsell.css', array(), '1.0.0');

		}

		/**
		 * Dismiss notice.
		 */
		
		public function dismiss() {

			if ( 
				isset($_GET['tip-product-countdown-dismiss']) && 
				check_admin_referer( 'tip-product-countdown-dismiss-' . get_current_user_id() )
			) {

				$notice_dismiss = (isset($_GET['tip-product-countdown-dismiss'])) ? true : false;
				update_user_meta( get_current_user_id(), 'tip_product_countdown_dismissed_notice_userid_' . get_current_user_id(), $notice_dismiss );
				remove_action( 'admin_notices', array(&$this, 'admin_notice') );
				
			} 
		
		}

		/**
		 * Admin notice.
		 */
		 
		public function admin_notice() {
			
		?>
			
            <div class="update-nag notice tip-product-countdown-notice">
            
				<div class="tip-product-countdown-notice-description">

                    <h3><?php esc_html_e( 'TIP Product Countdown', 'tip-product-countdown' );?></h3>

					<strong><?php esc_html_e( 'Pay what you want to unlock premium features like...', 'tip-product-countdown' );?></strong>

					<p class="notice-coupon-message">

						<span class="dashicon dashicons dashicons-yes-alt" size="10"></span><?php esc_html_e( '4 additional layouts', 'tip-product-countdown' ); ?><br/>
						<span class="dashicon dashicons dashicons-yes-alt" size="10"></span><?php esc_html_e( 'Show the countdown before the product title or after the add to cart button', 'tip-product-countdown' ); ?><br/>
						<span class="dashicon dashicons dashicons-yes-alt" size="10"></span><?php esc_html_e( 'Optional text above the countdown', 'tip-product-countdown' ); ?><br/>
						<span class="dashicon dashicons dashicons-yes-alt" size="10"></span><?php esc_html_e( 'Optional text below the countdown', 'tip-product-countdown' ); ?><br/>
						<span class="dashicon dashicons dashicons-yes-alt" size="10"></span><?php esc_html_e( 'Options to show the countdown on Shop and WooCommerce Archive Pages', 'tip-product-countdown' ); ?><br/>
						<span class="dashicon dashicons dashicons-yes-alt" size="10"></span><?php esc_html_e( 'Lifetime updates', 'tip-product-countdown' ); ?><br/>
						<span class="dashicon dashicons dashicons-yes-alt" size="10"></span><?php esc_html_e( 'Unlimited Website Usage', 'tip-product-countdown' ); ?><br/>
						<span class="dashicon dashicons dashicons-yes-alt" size="10"></span><?php esc_html_e( '30 days money back guarantee', 'tip-product-countdown' ); ?><br/>

					</p>

					<a target="_blank" href="<?php echo esc_url( TIP_PRODUCT_COUNTDOWN_UPGRADE_LINK . '?ref=2&campaign=notice' ); ?>" class="button"><?php esc_attr_e( 'Name your price', 'tip-product-countdown' ); ?></a>
					
					<div class="clear"></div>

					<?php printf('<a href="%1$s" class="dismiss-notice">'. esc_html__( 'Dismiss this notice', 'tip-product-countdown' ) .'</a>', esc_url( wp_nonce_url( add_query_arg( 'tip-product-countdown-dismiss', '1' ),'tip-product-countdown-dismiss-' . get_current_user_id() ))); ?>

				</div>

                <div class="clear"></div>

            </div>
		
		<?php
		
		}

	}

}

new tip_product_countdown_admin_notice();

?>