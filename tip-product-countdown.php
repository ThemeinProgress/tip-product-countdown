<?php

/*
 Plugin Name: TIP Product Countdown
 Plugin URI: https://www.themeinprogress.com/tip-product-countdown-for-woocommerce
 Description: TIP Product Countdown for WooCommerce is a lightweight, simple, and highly customizable plugin that allows you to add a product countdown for WooCommerce quickly and intuitively. Designed to boost urgency and conversions, the plugin displays a countdown timer directly on product pages or archive pages of your online store.
 Version: 1.0.0
 Text Domain: tip-product-countdown
 Author: ThemeinProgress
 Author URI: https://www.themeinprogress.com
 License: GPL3
 Domain Path: /languages/

 Copyright 2025  ThemeinProgress  (email : support@wpinprogress.com)

 This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License, version 2, as published by the Free Software Foundation.

 This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.

 You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'TIP_PRODUCT_COUNTDOWN_FOLDER', plugins_url(false, __FILE__ ));
define( 'TIP_PRODUCT_COUNTDOWN_FOLDER_NAME', 'tip-product-countdown' );
define( 'TIP_PRODUCT_COUNTDOWN_UPGRADE_LINK', 'https://www.themeinprogress.com/tip-product-countdown-for-woocommerce/' );

if( !class_exists( 'tip_product_countdown' ) ) {

	class tip_product_countdown {
	
        private $added_hook = false;

		/**
		* Constructor
		*/
			 
		public function __construct() {
            
			add_action( 'plugins_loaded', array(&$this,'plugins_setup'));
			add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'plugin_action_links' ), 10, 2 );
			add_action( 'admin_menu', array(&$this, 'admin_menu'), 100);

			add_action( 'wp_enqueue_scripts', array(&$this,'load_public_scripts') );
			add_action( 'admin_enqueue_scripts', array(&$this,'load_admin_scripts') );

            add_filter( 'woocommerce_product_data_tabs', array(&$this,'product_data_tabs') );
            add_action( 'woocommerce_product_data_panels', array(&$this,'product_data_panels'));
            add_action( 'woocommerce_process_product_meta', array(&$this,'save_product_data'));

            add_action( 'woocommerce_single_product_summary', array($this,'show_countdown'), 15 );


		}

        /**
		* Get postmeta
		*/

        public function get_post_meta( $meta_key, $default = '', $post_id = null ) {

            if ( empty( $post_id ) ) {

                global $post;

                if ( ! isset( $post->ID ) ) {
                    return $default;
                }

                $post_id = $post->ID;

            }

            $value = get_post_meta( $post_id, $meta_key, true );

            return ! empty( $value ) ? $value : $default;

        }

        /**
		* Sanitize datatime
		*/

        public function sanitize_datatime($v) {

            $datetime = sanitize_text_field( $v );

            $dt = DateTime::createFromFormat( 'Y-m-d\TH:i', $datetime );

            if ( ! $dt ) {
                return '';
            }

            return $dt->format( 'Y-m-d H:i:s' );

        }

		/**
		* Plugin setup
		*/
			 
		public function plugins_setup() {

            if ( !class_exists( 'WooCommerce' ) ) {
                add_action( 'admin_notices', array(&$this,'admin_notice') );
                return;
            }

			require_once dirname(__FILE__) . '/core/includes/class-notice.php';
	
		}

		/**
		* Admin notice
		*/
			 
		public function admin_notice() {

            if ( !current_user_can( 'activate_plugins' ) ) {
                return;
            }

            $class = 'notice notice-error';
            $message = __( 'TIP Product Countdown: WooCommerce is required for this plugin to work properly.', 'tip-product-countdown' );
            printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );

        }

		/**
		* Plugin action links
		*/

		public function plugin_action_links( $links ) {

			$links[] = '<a href="'. esc_url( get_admin_url(null, 'options-general.php?page=tip_product_countdown_upsell_panel') ) .'">' . esc_html__('Pro version','tip-product-countdown') . '</a>';

			return $links;

		}
		/**
		 * Admin Menu
		 */

		public function admin_menu() {
			add_options_page(
				esc_html__('TIP Product Countdown', 'tip-product-countdown'),
				esc_html__('TIP Product Countdown', 'tip-product-countdown'),
				'manage_options',
				'tip_product_countdown_upsell_panel',
				array(&$this, 'upsell_panel')
			);
		}

		/**
		 * Upsell Panel
		 */

		public function upsell_panel() {

        ?>

            <div class="tip-product-countdown-upsell-panel tip-product-countdown-notice">
            
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

				</div>

                <div class="clear"></div>

            </div>

        <?php

		}

        /**
		* Loadable scripts
		*/
			 
		public function loadable_scripts() {

            $loadable = false;

            if ( 
                is_product() 
            ) {

                $enable_countdown = get_post_meta( get_the_ID(), 'tip_product_countdown_enable_countdown', true );
                $sale_to = strtotime(get_post_meta( get_the_ID(), 'tip_product_countdown_date', true ));

                if ( 
                    $enable_countdown == 'yes' &&
                    $sale_to && time() < (int) $sale_to
                ) {
                    $loadable = true;
                }
            }

            if ( is_shop() || is_product_taxonomy() ) {

                $loadable = true;

            }

            return $loadable;
		
		}

		/**
		* Load public scripts
		*/
			 
		public function load_public_scripts() {

            if ( $this->loadable_scripts() == false ) {
                return;
            }

            wp_enqueue_script(
                'tip-product-countdown-script',
                plugins_url( 'assets/js/countdown.js', __FILE__ ),
                array( 'jquery' ),
                '1.0',
                true
            );

            wp_localize_script(
                'tip-product-countdown-script',
                'tip_product_countdown_data', array(
                    'expired_countdown_text' => __('This offer has expired', 'tip-product-countdown')
                )
            );

            wp_enqueue_style(
                'tip-product-countdown-style', plugins_url( 'assets/css/countdown.css', __FILE__ ), array(), '1.0'
            );
		
		}

        /**
		* Load admin scripts
        */

		public function load_admin_scripts() {

            $screen = get_current_screen();

            if ( isset( $screen->id ) && $screen->id === 'product' ) {
                wp_enqueue_style(
                    'tip-product-countdown-admin',
                    plugins_url( '/core/assets/css/admin.css', __FILE__ ),
                    array(),
                    '1.0.0'
                );
            }

        }

		/**
		* Product data tabs
		*/
			 
		public function product_data_tabs($tabs) {

            $tabs['woocommerce_countdown'] = array(
                'label'  => __( 'Countdown', 'tip-product-countdown' ),
                'target' => 'woocommerce_countdown_product_data',
                'class'  => array('tip_product_countdown_tab show_if_simple','show_if_variable','show_if_grouped','show_if_external'),
            );
            return $tabs;

        }

		/**
		* Product data panels
		*/
			 
		public function product_data_panels() {

            global $post;

        ?>

            <div id="woocommerce_countdown_product_data" class="panel woocommerce_options_panel">

                <?php

                    // Nonce
                    wp_nonce_field( 'tip_product_countdown_save_data', 'tip_product_countdown_nonce' );

                    // Enable countdown
                    woocommerce_wp_checkbox( array(
                        'id' => 'tip_product_countdown_enable_countdown',
                        'label' => __( 'Enable countdown', 'tip-product-countdown' ),
                        'description' => __( 'Would you like to enable the countdown for this product?.', 'tip-product-countdown' ),
                    ));

                    // Countdown date (timestamp or date string)
                    woocommerce_wp_text_input( array(
                        'id' => 'tip_product_countdown_date',
                        'label' => __( 'Countdown end', 'tip-product-countdown' ),
                        'description' => __( 'Select the end date and time for the offer', 'tip-product-countdown' ),
                        'type' => 'datetime-local',
                        'class' => 'short',
                    ));

                ?>
            </div>

        <?php

        }

		/**
		* Save product data
		*/
			 
		public function save_product_data($post_id) {

            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
                return;
            }

            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return;
            }

            if ( empty($_POST['tip_product_countdown_nonce'] ) ) {
                return;
            }

            /* Sanitize nonce */

            $nonce = sanitize_text_field(wp_unslash($_POST['tip_product_countdown_nonce']));
            if ( !wp_verify_nonce( $nonce, 'tip_product_countdown_save_data' ) ) {
                return;
            }

            /* Sanitize checkbox -> tip_product_countdown_enable_countdown  */
            if ( isset($_POST['tip_product_countdown_enable_countdown'])) {
                update_post_meta( $post_id, 'tip_product_countdown_enable_countdown', 'yes' );
            } else {
                update_post_meta( $post_id, 'tip_product_countdown_enable_countdown', false );
            }

            /* Sanitize datetime -> tip_product_countdown_date  */
            if (isset($_POST['tip_product_countdown_date'])) {
                $date = $this->sanitize_datatime($_POST['tip_product_countdown_date']);
                update_post_meta( $post_id, 'tip_product_countdown_date', $date );
            } 

        }

		/**
		* Show countdown
		*/
			 
		public function show_countdown() {

            global $post;

            $enable_countdown = $this->get_post_meta('tip_product_countdown_enable_countdown', false);
            $countdown_expiration = $this->get_post_meta('tip_product_countdown_date', false);
            $sale_to = strtotime($countdown_expiration);

            if ( 
                $enable_countdown == 'yes' &&
                $sale_to && time() < (int) $sale_to
            ) {

                echo '<div class="tip_product_countdown_wrap tip_product_countdown_layout_1">';

                    echo '<div class="tip_product_countdown_container" data-end="'.esc_attr($sale_to).'">';
                        echo '<div class="tip_product_countdown">';
                            echo '<div class="tip_product_countdown_item">';
                                echo '<span class="tip_product_countdown_number tip_days">0</span>';
                                echo '<span class="tip_product_countdown_label">' . esc_html__('Days','tip-product-countdown') . '</span>';
                            echo '</div>';
                            echo '<div class="tip_product_countdown_item">';
                                echo '<span class="tip_product_countdown_number tip_hours">0</span>';
                                echo '<span class="tip_product_countdown_label">' . esc_html__('Hours','tip-product-countdown') . '</span>';
                            echo '</div>';
                            echo '<div class="tip_product_countdown_item">';
                                echo '<span class="tip_product_countdown_number tip_minutes">0</span>';
                                echo '<span class="tip_product_countdown_label">' . esc_html__('Minutes','tip-product-countdown') . '</span>';
                            echo '</div>';
                            echo '<div class="tip_product_countdown_item">';
                                echo '<span class="tip_product_countdown_number tip_seconds">0</span>';
                                echo '<span class="tip_product_countdown_label">' . esc_html__('Seconds','tip-product-countdown') . '</span>';
                            echo '</div>';
                        echo '</div>';
                    echo '</div>';

                echo '</div>';

            }

        }

	}

	new tip_product_countdown();

}

?>