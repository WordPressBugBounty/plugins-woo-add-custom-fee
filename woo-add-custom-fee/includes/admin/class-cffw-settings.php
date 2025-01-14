<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'CFFW_Settings' ) ) :

    class CFFW_Settings {
       
        public function __construct() {
            add_action( 'woocommerce_settings_tabs_array', array( $this, 'cffw_add_settings_tab' ), 99 );
            add_action( 'woocommerce_settings_tabs_settings_wacf', array($this, 'cffw_settings_tab' ) );
            add_action( 'woocommerce_update_options_settings_wacf', array($this, 'update_settings') );
            add_action( 'admin_enqueue_scripts', array( $this, 'cffw_script' ) );
        }

        function cffw_script() {
            $screen    = get_current_screen();
            $screen_id = $screen ? $screen->id : '';
            if ( 'woocommerce_page_wc-settings' == $screen_id ) {
                wp_enqueue_script( 'wacf-script', WACF_URL . "assets/admin/cffw-backend-script.js", array(), WACF_VERSION, true );
            }

            if ( 'woocommerce_page_wc-settings' == $screen_id ) {
                wp_register_style('wacf_admin_styles', WACF_URL . '/assets/admin/cffw-admin.css', array(), WACF_VERSION);
                wp_enqueue_style('wacf_admin_styles');
            }
        }
        
        public function cffw_add_settings_tab( $settings_tabs ) {
            $settings_tabs['settings_wacf'] = __( 'Conditional Fee', 'conditional-fees-for-woocommerce-lite' );
            return $settings_tabs;
        }
        
        public function cffw_settings_tab() {
            woocommerce_admin_fields( self::get_settings() );
            include_once WACF_PATH . '/includes/admin/html-cffw-slider.php';
        }
        
        public static function update_settings() {
            woocommerce_update_options( self::get_settings() );
        }
       
        public static function get_settings() {
            
           $settings = array(
                'section_title' => array(
                    'name'     => __( 'Conditional Fee', 'conditional-fees-for-woocommerce-lite' ),
                    'type'     => 'title',
                    'desc'     => '',
                    'id'       => 'wdvc_tab_demo_section_title',
                    'desc_tip' => true,
                ),

                'enable' => array(
                    'name'     => __( 'Enable ', 'conditional-fees-for-woocommerce-lite' ),
                    'type' => 'checkbox',
                    'id'       => 'wacf_enable',
                    'desc_tip' => true,
                ),

                'label' => array(
                    'name'     => __( 'Custom Fee Label', 'conditional-fees-for-woocommerce-lite' ),
                    'type' => 'text',
                    'desc'     => __( 'Enter text for Custom Fee label', 'conditional-fees-for-woocommerce-lite' ),
                    'id'       => 'wacf_fee_label',
                    'desc_tip' => true,
                ),

                'type' => array(
                    'name'     => __( 'Custom Fee Type', 'conditional-fees-for-woocommerce-lite' ),
                    'type'     => 'select',
                    'desc'     => __( 'Type for custom fee', 'conditional-fees-for-woocommerce-lite' ),
                    'id'       => 'wacf_type',
                    'desc_tip' => true,
                    'options'  => array(
                        'fixed' => 'Fixed',
                        'percentage' => 'Percentage (total value of cart)',
                    ),
                ),

                'charges' => array(
                    'name'     => __( 'Custom Fee charges', 'conditional-fees-for-woocommerce-lite' ),
                    'type' => 'number',
                    'desc'     => __( 'Enter amount for Custom Fee charges (Percentage or Fixed)', 'conditional-fees-for-woocommerce-lite' ),
                    'id'       => 'wacf_fee_charges',
                    'desc_tip' => true,
                ),

                'taxable' => array(
                    'name'     => __( 'Taxable', 'conditional-fees-for-woocommerce-lite' ),
                    'type'     => 'select',
                    'desc'     => __( 'Check this box if would like to add tax to Custom Fee', 'conditional-fees-for-woocommerce-lite' ),
                    'id'       => 'wacf_taxable',
                    'desc_tip' => true,
                    'options'  => array(
                        true => 'Yes',
                        false => 'No',
                    ),
                ),

                'tax_class' => array(
                    'name'     => __( 'Tax Class', 'conditional-fees-for-woocommerce-lite' ),
                    'type'     => 'select',
                    'desc'     => __( 'Select Tax Class if tax is enabled', 'conditional-fees-for-woocommerce-lite' ),
                    'id'       => 'wacf_tax_class',
                    'desc_tip' => true,
                    'options'  => self::get_tax_options(),
                ),
                'enable_min' => array(
                    'name'     => __( 'Use Minimum threshold value ', 'conditional-fees-for-woocommerce-lite' ),
                    'type' => 'checkbox',
                    'id'       => 'wacf_enable_min',
                    'desc_tip' => true,
                ),
                'minimum' => array(
                    'name'     => __( 'Minimum Cart Amount', 'conditional-fees-for-woocommerce-lite' ),
                    'type' => 'number',
                    'desc'     => __( 'Set Minimum total cart amount on which you would like to apply Custom Fee', 'conditional-fees-for-woocommerce-lite' ),
                    'id'       => 'wacf_minimum',
                    'desc_tip' => true,
                ),
                'enable_max' => array(
                    'name'     => __( 'Use Maximum threshold value ', 'conditional-fees-for-woocommerce-lite' ),
                    'type' => 'checkbox',
                    'id'       => 'wacf_enable_max',
                    'desc_tip' => true,
                ),
                'maximum' => array(
                    'name'     => __( 'Maximum  Cart Amount', 'conditional-fees-for-woocommerce-lite' ),
                    'type' => 'number',
                    'desc'     => __( 'Set Maximum total cart amount on which you would like to apply Custom Fee', 'conditional-fees-for-woocommerce-lite' ),
                    'id'       => 'wacf_maximum',
                    'desc_tip' => true,
                ),
                // TODO - Add multiselect option to apply to only selected products
                'section_end' => array(
                     'type' => 'sectionend',
                     'id' => 'wacf_section_end',
                )
            );

            return apply_filters( 'wc_settings_wdvc_settings', $settings );
        }

        public static function get_tax_options() {
            $tax_classes = ! empty( 'yes' === get_option( 'woocommerce_calc_taxes', false ) ) ? wc_get_product_tax_class_options() : array();
            return $tax_classes;
        }
    }
    
    new CFFW_Settings;

endif;