# retry-failed-woocommerce-webhooks

This plugin reschedules failed webhooks to retry if a 2xx response code is not received.

Webhooks are disabled after 5 retries by default if the delivery URL returns an unsuccessful status such as 404 or 5xx. Successful responses are 2xx, 301 or 302.

To increase the number of retries, you can use the `woocommerce_max_webhook_delivery_failures` filter function. The plugin logs failures in the `webhook-delivery-failure` log.
