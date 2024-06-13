<?php

/*
Plugin Name: Retry Failed WooCommerce Webhooks
Description: This plugin reschedules failed webhooks to retry if a 2xx response code is not received. Webhooks are disabled after 5 retries by default if the delivery URL returns an unsuccessful status such as 404 or 5xx. Successful responses are 2xx, 301 or 302. To increase the number of retries, you can use the woocommerce_max_webhook_delivery_failures filter function. Failers are logged in the webhook-delivery-failure log.
Version: 1.0
Author: Woo Growth Team
Domain: retry-failed-webhooks
License: GPL2
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


try {


		// add listener for woocommerce web hooks
		function woocommerce_webhook_listener_custom( $http_args, $response, $duration, $arg, $id ) {
			$response_code = wp_remote_retrieve_response_code( $response );

			if ( $response_code < 200 || $response_code > 299 ) {

				wc_get_logger()->debug(
					sprintf(
						'Webhook %s failed to deliver with a response coede of %s',
						$id,
						$response_code
					),
					array(
						'source'    => 'webhook-delivery-failure',
						'data'      => $response,
						'backtrace' => false,
					)
				);

				// re-queue web-hook for another attempt, retry every 5 minutes until success
				$timestamp = new DateTime( '+5 minutes' );
				$args = array( 'webhook_id' => $id, 'arg' => $arg );
				WC()->queue()->schedule_single( $timestamp, 'woocommerce_deliver_webhook_async', $args, $group = 'woocommerce-webhooks' );
			}
		}

		add_action( 'woocommerce_webhook_delivery', 'woocommerce_webhook_listener_custom', 10, 5 );

}
catch( Exception $ex ){

}
