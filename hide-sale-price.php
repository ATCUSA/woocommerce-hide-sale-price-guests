<?php
/*
Plugin Name: Woocommerce Hide Sale Price for Guests
Plugin URI: https://github.com/ATCUSA/woocommerce-hide-sale-price-guests
Description: Hides the sale price of selected products for guests (non-logged-in users) in WooCommerce.
Version: 1.0.0
Author: Austin Cole
Author URI: https://github.com/ATCUSA/
*/

// Check if WooCommerce is active
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {

  // Function to add custom product field
  function hsp_add_custom_product_fields()
  {
    woocommerce_wp_checkbox(
      array(
        'id' => '_hide_sale_price',
        'label' => __('Hide sale price for guests', 'hide-sale-price'),
        'description' => __('Check this box to hide the sale price for non-logged-in users.', 'hide-sale-price')
      )
    );
  }
  // Hook the function into WooCommerce
  add_action('woocommerce_product_options_pricing', 'hsp_add_custom_product_fields');

  // Function to save the custom product field value to post meta
  function hsp_save_custom_product_fields($post_id)
  {
    $hide_sale_price = isset($_POST['_hide_sale_price']) ? 'yes' : 'no';
    update_post_meta($post_id, '_hide_sale_price', $hide_sale_price);
  }
  // Hook the function into WooCommerce
  add_action('woocommerce_process_product_meta', 'hsp_save_custom_product_fields');

  // Function to hide sale price for guests
  function hsp_hide_sale_price_for_guests($price, $product)
  {
    // Check if user is not logged in and the product has the custom field value set to "yes"
    if (!is_user_logged_in() && get_post_meta($product->get_id(), '_hide_sale_price', true) === 'yes') {
      // Check if product is on sale
      if ($product->is_on_sale()) {
        // If product is on sale, return an empty string to hide the sale price
        return '';
      }
    }
    // If either condition is not met, return the original price
    return $price;
  }
  // Hook the function into WooCommerce
  add_filter('woocommerce_get_price_html', 'hsp_hide_sale_price_for_guests', 10, 2);
}
