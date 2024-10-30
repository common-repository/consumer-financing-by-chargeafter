if (typeof chargeAfterSdkData != "undefined") {
  function onLoadChargeAfterSDKScript() {
    var config = {
      apiKey: chargeAfterSdkData.public_key
    }

    ChargeAfter.init(config);
  }

  var script = document.createElement('script');
  script.src = chargeAfterSdkData.cdn_url + '/web/v2/chargeafter.min.js?t=' + Date.now();
  script.type = 'text/javascript';
  script.async = true;
  script.onload = onLoadChargeAfterSDKScript;
  document.body.appendChild(script);
}
