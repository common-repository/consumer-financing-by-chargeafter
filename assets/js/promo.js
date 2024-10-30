jQuery( document ).ready(
  function ($) {
    function promotionalUpdate( items = [] ) {
      if (typeof ChargeAfter === 'object') {
        ChargeAfter.promotions?.update(items);
      }
    }

    function onProductChangeVariation() {
      function onVariantChange(variant) {
        let newPrice = variant.display_price,
          widget = $(".ca-promotional-widget.ca-product");

        if (newPrice && widget) {
          let widgetItemPrice = widget.data("widget-item-price"),
            widgetItemSku = widget.data("widget-item-sku");

          if (newPrice !== widgetItemPrice && widgetItemSku) {
            widget.data("widget-item-price", newPrice);

            promotionalUpdate( [{ sku: widgetItemSku, price: newPrice }] );
          }
        }
      }

      $( document.body ).on("show_variation", ".single_variation_wrap", function (event, variant) {
        onVariantChange(variant);
      });
    }

    function onCartUpdate() {
      // Native WooCommerce hook.
      $( document.body ).on( 'updated_cart_totals update_checkout' , function () {
        promotionalUpdate();
      } );

      // Custom WooCommerce hook for WooCommerce Cart Block
      if (typeof wp != "undefined" && wp.hooks) {
        wp.hooks.addAction( 'ca_updated_cart_totals', 'wc-chargeafter/promotional-update', function (updatedTotal) {
          promotionalUpdate( [{ sku: 'cart_sku', price: updatedTotal }] );
        } );
      }
    }

    onProductChangeVariation();
    onCartUpdate();
  }
);
