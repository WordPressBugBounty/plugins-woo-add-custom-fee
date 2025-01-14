<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'CFFW_Front' ) ) :

    class CFFW_Front {

        public function __construct() {
            add_action( 'woocommerce_cart_calculate_fees', array( $this, 'wacf_add_fee_cart' ) );
        }

        public function wacf_add_fee_cart() {
            // Get options
            $enable_fee     = get_option('wacf_enable', 'no');
            $minimum_fee    = get_option('wacf_minimum', 0);
            $maximum_fee    = get_option('wacf_maximum', 0);
            $enable_min_fee = get_option('wacf_enable_min', 'no');
            $enable_max_fee = get_option('wacf_enable_max', 'no');

            $cart_total = WC()->cart->subtotal_ex_tax;

            // Only proceed if fees are enabled
            if ( 'yes' !== $enable_fee ) {
                return;
            }

            // Check minimum and maximum conditions
            if ( 'yes' === $enable_min_fee && $cart_total < $minimum_fee ) {
                return;
            }

            if ( 'yes' === $enable_max_fee && $maximum_fee > 0 && $cart_total > $maximum_fee ) {
                return;
            }

            // Calculate fee if all conditions are met
            $this->wacf_calculate_fee($cart_total);
        }

        public function wacf_calculate_fee( $cart_total ) {
            $fee_type   = get_option('wacf_type', 'fixed'); // Default to fixed if not set
            $fee_label  = get_option('wacf_fee_label', __('Custom Fee', 'conditional-fees-for-woocommerce-lite'));
            $fee_amount = get_option('wacf_fee_charges', 0);
            $taxable    = get_option('wacf_taxable', false);
            $tax_class  = get_option('wacf_tax_class', '');

            // Calculate fee
            if ( isset($fee_amount) && $fee_amount > 0 ) {
	            $fee = ( 'percentage' === $fee_type ) ? ( $cart_total * $fee_amount / 100 ) : $fee_amount;

	            if ( '1' === $taxable ) {
	            	$taxes = $this->apply_taxes( $tax_class, $fee );
	            	$fee = $taxes + $fee;
	            }

	            // Add fee to WooCommerce cart
				WC()->cart->add_fee( $fee_label, $fee, false );
			}
        }

        public function apply_taxes( $tax_class, $fee ) {
		
			$rates = WC_Tax::get_rates( $tax_class );
			$taxes = array_sum( WC_Tax::calc_tax( $fee, $rates, false ) );
			return $taxes;
		}
    }

    new CFFW_Front();

endif;
