<?php
/**
 * Payment Gateway Registry
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DONASAI_Gateway_Registry {

    private static $gateways = array();

    /**
     * Initialize Gateways
     */
    public static function init() {
        // Register default manual gateway
        self::register_gateway( new DONASAI_Gateway_Manual() );

        // Allow others to register
        do_action( 'donasai_register_gateways' );
    }

    /**
     * Register a new gateway
     */
    public static function register_gateway( DONASAI_Gateway $gateway ) {
        self::$gateways[ $gateway->get_id() ] = $gateway;
    }

    /**
     * Get all registered gateways
     */
    public static function get_gateways() {
        return self::$gateways;
    }

    /**
     * Get specific gateway
     */
    public static function get_gateway( $id ) {
        return isset( self::$gateways[ $id ] ) ? self::$gateways[ $id ] : null;
    }
}
