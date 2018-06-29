<?php
/**
 * Plugin Name:     Limited Checkout
 * Plugin URI:      https://webjarvis.com/limited-checkout
 * Description:     Limits checkout to a min of £15
 * Author:          Web Jarvis
 * Author URI:      https://webjarvis.com
 * Text Domain:     limited-checkout
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Limited_Checkout
 */

add_action( 'woocommerce_checkout_process', 'wc_minimum_order_amount' );
add_action( 'woocommerce_before_cart', 'wc_minimum_order_amount' );

function wc_minimum_order_amount() {
	$locked  = false;
	$minimum = 15;

	$cart = WC()->cart;
	$cart = array_filter( array_flatten( $cart->get_cart_contents() ), 'is_object' );

	foreach ( $cart as $product ) {
		if ( $product instanceof WC_Product_Simple ) {
			$locked = true;
		}
	}

	if ( WC()->cart->get_subtotal() < $minimum && $locked ) {

		if ( is_cart() ) {

			wc_print_notice(
				sprintf( 'Your current order subtotal is %s — you must have an order with a minimum of %s to place your order.',
					wc_price( WC()->cart->get_subtotal() ),
					wc_price( $minimum )
				), 'error'
			);

		} else {

			wc_add_notice(
				sprintf( 'Your current order subtotal is %s — you must have an order with a minimum of %s to place your order.',
					wc_price( WC()->cart->get_subtotal() ),
					wc_price( $minimum )
				), 'error'
			);

		}
	}

}

function array_flatten( $array, $depth = INF ) {
	$result = [];

	foreach ( $array as $item ) {
		if ( ! is_array( $item ) ) {
			$result[] = $item;
		} elseif ( $depth === 1 ) {
			$result = array_merge( $result, array_values( $item ) );
		} else {
			$result = array_merge( $result, array_flatten( $item, $depth - 1 ) );
		}
	}

	return $result;
}