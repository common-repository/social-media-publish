
/**
 * Initialize Facebook SDK
 * @param {*} clientAppId 
 */
function initFacebookSdk(clientAppId) {
   FB.init({
      appId: clientAppId,
      autoLogAppEvents: true,
      xfbml: true,
      version: 'v10.0'
   });
}

/**
 * Show Facebook Login Dialog and get long live access token
 * @param {*} app_id 
 * @param {*} app_secret 
 */
function showLoginDialog(app_id, app_secret) {
   FB.login(function (loginResponse) {
      if (loginResponse.authResponse) {
         // GET FACEBOOK ACCESS TOKEN
         var accessToken = FB.getAuthResponse()['accessToken'];

         // CONVERT TO LONG-LIVE ACCESS TOKEN
         FB.api(`/oauth/access_token?grant_type=fb_exchange_token&client_id=${app_id}&client_secret=${app_secret}&fb_exchange_token=${accessToken}`, function (longLiveResponse) {

            if (longLiveResponse['access_token']) {
               var longLiveAccessToken = longLiveResponse['access_token'];

               var currentDate = new Date();
               var expiresDate = currentDate.setDate(currentDate.getDate() + 80);

               document.cookie = `smp_a=${longLiveAccessToken};expires=${new Date(expiresDate).toUTCString()}; path=/`;
               // Reload page to show authorized status message
               location.reload();
            } else {
               alert(longLiveResponse['error']['message']);
            }
         });

      } else {
         alert('User cancelled login or did not fully authorize.');
      }
   });
}
