jQuery(document).ready(
	function ($) {
		if (typeof chargeAfterCheckoutData !== "undefined") {
			function checkoutButtonUpdate() {
				if ($('.ca-checkout-button img').length === 0 && typeof ChargeAfter === 'object') {
					ChargeAfter.promotions?.update();
				}
			}

			const presentCheckout = function (hash) {
				const orderId = hash.match(/orderid=([a-zA-Z0-9]+)/)[1];
				const nonce = hash.match(/nonce=([a-zA-Z0-9]+)/)[1];

				window.location.hash = '';

				$.getJSON(
					chargeAfterCheckoutData.url.get_checkout_data + '&orderId=' + orderId + '&nonce=' + nonce,
					function (data) {
						if (data.error) {
							onError(data.error, 'CUSTOM_ERROR');
						} else {
							var opt = Object.assign(data, {
								onDataUpdate: function (updatedData, callback) {
									if (callback === undefined && updatedData.callback) {
										callback = updatedData.callback;
									}

									data = updatedData;
									if (updatedData.updatedData) {
										data = updatedData.updatedData;
									}

									if (chargeAfterCheckoutData.settings.with_data_update) {
										$.post(
											chargeAfterCheckoutData.url.on_data_update + '&orderId=' + orderId + '&nonce=' + nonce,
											data,
											function (data) {
												callback(data);
											},
											'json',
										)
									} else {
										callback(data);
									}
								},
								callback: function (token, data, error) {
									const timeout = setTimeout(function () {
										clearTimeout(timeout);

										if (error) {
											onError(error.message, error.code)
										} else if (token) {
											$.post({
												url: chargeAfterCheckoutData.url.create_charge + '&orderId=' + orderId + '&nonce=' + nonce,
												dataType: 'json',
												data: {
													confirmationToken: token,
													data: data
												}
											})
												.done(onRedirect)
												.fail(function (e) {
													onRedirect(JSON.parse(e.responseText));
												});
										}
									}, 500);
								}
							});

							ChargeAfter.checkout.present(opt);
						}
					}
				);
			};

			const onError = function (message, code) {
				$.get(chargeAfterCheckoutData.url.get_error + '&errorMsg=' + message + '&errorCode=' + code)
					.done(function (response) {
						const data = JSON.parse(response);

						if (data.redirect) {
							window.location.replace(data.redirect);
						}
					})
					.fail(function () {
						window.location.reload();
					});
			};

			const onRedirect = function (data) {
				if (data && data.redirect) {
					window.location.replace(data.redirect);
				}
			}

			window.addEventListener('hashchange', function () {
				let hash = window.location.hash;

				if (/#checkoutfinance=1/g.test(hash)) {
					presentCheckout(hash);
				}
			});

			$(window).on('ca_update_checkout_payment_method', function () {
				checkoutButtonUpdate();
			});

			// Custom WooCommerce hook for WooCommerce Checkout Block
			if (typeof wp != "undefined" && wp.hooks) {
				wp.hooks.addAction('ca_update_checkout_payment_method', 'wc-chargeafter/checkout-update', function () {
					setTimeout(function () {
						checkoutButtonUpdate();
					}, 1000);
				});
			}
		}
	}
);
