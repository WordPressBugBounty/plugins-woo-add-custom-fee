<?php
/**
 * Plugin Name: Conditional Fees for WooCommerce Lite
 * Plugin URI: https://wpexperts.io
 * Description: Conditional Fees for WooCommerce Lite lets you add a single customizable fee based on cart total or cart total percentage. Use this custom fee plugin to experience a new level of control and flexibility.
 * Version: 1.7.0
 * Tested up to: 6.7
 * Author: WPExperts
 * Author URI: https://wpexperts.io
 * Developer: WPExperts
 * Developer URI: https://wpexperts.io
 * Text Domain: conditional-fees-for-woocommerce-lite
 * WC requires at least: 3.0
 * WC tested up to: 9.5
 * License: GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
defined( 'ABSPATH' ) || exit;


/* Freemius Integration Start*/
if ( ! function_exists( 'cfl_fs' ) ) {
    // Create a helper function for easy SDK access.
    function cfl_fs() {
        global $cfl_fs;

        if ( ! isset( $cfl_fs ) ) {
            // Include Freemius SDK.
            require_once dirname(__FILE__) . '/freemius/start.php';

            $cfl_fs = fs_dynamic_init( array(
                'id'                  => '17420',
                'slug'                => 'conditional-fees-lite',
                'premium_slug'        => 'conditional-fees---lite-premium',
                'type'                => 'plugin',
                'public_key'          => 'pk_db52ee2f01a9ff656ab05d7aeac3f',
                'is_premium'          => false,
                'has_addons'          => false,
                'has_paid_plans'      => false,
                'menu'                => array(
                    'first-path'     => 'admin.php?page=wc-settings&tab=settings_wacf',
                    'account'        => false,
                    'contact'        => false,
                    'support'        => false,
                ),
            ) );
        }

        return $cfl_fs;
    }

    // Init Freemius.
    cfl_fs();
    // Signal that SDK was initiated.
    do_action( 'cfl_fs_loaded' );
}
/* Freemius Integration End*/

define( 'WACF_URL', plugin_dir_url( __FILE__ ) );
define( 'WACF_PATH', plugin_dir_path( __FILE__ ) );
define( 'WACF_FILE', __FILE__ );
define( 'WACF_VERSION', '1.7.0' );

if ( ! class_exists( 'wacf' ) ) {
	
	/**
	 * wacf
	 */
	class wacf {

		public function __construct() {
			/**
			*   Filter active_plugins
			*
			*   @since 1.0
			*/
			if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {
				add_action( 'init', array( $this, 'wacf_load_textdomain' ) );
				add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'watq_add_action_links' ) );
				$this->include_file();
				add_action(
					'before_woocommerce_init',
					function () {
						if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
							\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
							\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__, true );
						}
					}
				);
			} else {
					 add_action( 'admin_notices', array( $this, 'inactive_plugin_notice' ) );
			}
		}

		public function watq_add_action_links( $actions ) {
			$temp = $actions['deactivate'];
			unset($actions['deactivate']);

			$mylinks = array(
				'<a href="https://wpexperts.io/products/conditional-fees-for-woocommerce/?utm_source=plugin_page&utm_medium=conditional_fees&utm_campaign=wordpress_org"><b>Get Premium</b></a>',
				'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=settings_wacf' ) . '">Settings</a>',
				$temp
			);
			$actions = array_merge( $actions, $mylinks );
			return $actions;
		}


		public function wacf_load_textdomain() {
			load_plugin_textdomain( 'conditional-fees-for-woocommerce-lite', false, basename( __DIR__ ) . '/languages/' );
		}

		public function include_file() {
			include_once WACF_PATH . '/includes/admin/class-cffw-settings.php';
			include_once WACF_PATH . '/includes/public/class-cffw-front.php';
		}

		public function inactive_plugin_notice() {
			// Deactivate the plugin.
			deactivate_plugins( __FILE__ );

			$wc_fee_woo_check = '<div id="message" class="error">
				<p><strong>' . __( 'Conditional Fees for WooCommerce Lite plugin is inactive.', 'conditional-fees-for-woocommerce-lite' ) . '</strong> The <a href="http://wordpress.org/extend/plugins/woocommerce/">WooCommerce plugin</a> ' . __( 'must be active for this plugin to work. Please install &amp; activate WooCommerce.', 'conditional-fees-for-woocommerce-lite' ) . ' »</p></div>';
			echo wp_kses_post( $wc_fee_woo_check );
		}
	}
	new wacf();
}
