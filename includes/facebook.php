<?php 
   /**
    * Get the facebook api graph url.
    *
    * @return string
    */
   function smp_get_facebook_graph_url()
   {
      return 'https://graph.facebook.com/v10.0/';
   }

   /**
    * Check response for error message.
    * The message saved in smp_errors to display on admin page.
    */
   function smp_is_facebook_error($response) {
      $responseBody = $response['body'];
      if ( strpos( $responseBody, 'error' ) ) {
         $jsondecode = json_decode($responseBody);
         $errorMessage = $jsondecode->error->type . ': ' . $jsondecode->error->message;

         error_log('FACEBOOK ERROR: ' . print_r($responseBody, true));
         $_SESSION['smp_errors'] = esc_attr( $errorMessage );  

         return true;
      }

      return false;
   }

   function smp_get_facebook_page_id() {
      $response = wp_safe_remote_get( smp_get_facebook_graph_url() . 'me/accounts?access_token=' . $_COOKIE['smp_a'] );

      if ( is_array( $response ) && !is_wp_error( $response ) && !smp_is_facebook_error( $response ) ) {
         $body = $response['body']; // use the content
         error_log('FACEBOOK PAGE BODY: ' . $body);
         $jsonResponse = json_decode($response['body']);
         $facebookPageId = $jsonResponse->data[0]->id;
         error_log('FACEBOOK PAGE ID: ' . $facebookPageId);

         return $facebookPageId;
      }
   } 

   function smp_get_instagram_business_account_id($facebookPageId) {
      $response = wp_safe_remote_get( smp_get_facebook_graph_url() . $facebookPageId . '?fields=instagram_business_account&access_token=' . $_COOKIE['smp_a'] );
  
      if ( is_array( $response ) && ! is_wp_error( $response ) && !smp_is_facebook_error( $response ) ) {
         $body    = $response['body']; // use the content
         error_log('INSTAGRAM BUSINESS BODY: ' . $body);

         $jsonResponse = json_decode($response['body']);
         $instagramBusinessAccountId = $jsonResponse->instagram_business_account->id;
         error_log('INSTAGRAM BUSINESS ID: ' . $instagramBusinessAccountId);

         return $instagramBusinessAccountId;
      }
   }

   function smp_create_ig_container($instagramBusinessAccountId, $title, $image_url) {
      $response = wp_safe_remote_post( smp_get_facebook_graph_url() . $instagramBusinessAccountId . '/media?image_url=' . $image_url . '&caption=' . $title . '&access_token=' . $_COOKIE['smp_a'] , array(
         'method' => 'POST',
         'timeout' => 45,
         'redirection' => 5,
         'httpversion' => '1.0',
         'blocking' => true,
         'headers' => array(),
         'cookies' => array()
         )
      );

      if ( is_wp_error( $response ) || smp_is_facebook_error( $response )) {
         $error_message = $response->get_error_message();
         error_log('IG CONTAINER ERROR: ' . $error_message);
      } else {
         error_log('IG CONTAINER BODY: ' . $response['body']);
         $jsonResponse = json_decode($response['body']);
         $igContainerId = $jsonResponse->id;
         error_log('IG CONTAINER ID: ' . $igContainerId);

         return $igContainerId;
      }
   }

   function smp_publish_instagram_photo($instagramBusinessAccountId, $igContainerId) {
      $response = wp_safe_remote_post( smp_get_facebook_graph_url() . $instagramBusinessAccountId . '/media_publish?creation_id=' . $igContainerId . '&access_token=' . $_COOKIE['smp_a'] , array(
         'method' => 'POST',
         'timeout' => 45,
         'redirection' => 5,
         'httpversion' => '1.0',
         'blocking' => true,
         'headers' => array(),
         'cookies' => array()
         )
      );

      if ( is_wp_error( $response ) ) {
         $error_message = $response->get_error_message();
         error_log('PUBLISH INSTAGRAM ERROR: ' . $error_message);
      } else {
         error_log('PUBLISH INSTAGRAM BODY: ' . $response['body']);
         $jsonResponse = json_decode($response['body']);

         return $jsonResponse->id;
      }
   }

   /**
    * Upload instagram photo
    * https://developers.facebook.com/docs/instagram-api/guides/content-publishing/
    * Permissions: ads_management, business_management, instagram_basic, instagram_content_publish, pages_read_engagement
    */
   function smp_post_instagram($caption, $image_url) {
      $facebookPageId = smp_get_facebook_page_id();

      if ( !empty($facebookPageId) ) {
         $instagramBusinessAccountId = smp_get_instagram_business_account_id($facebookPageId);
        
         if ( !empty ($instagramBusinessAccountId) ) {
            $igContainerId = smp_create_ig_container($instagramBusinessAccountId, $caption, $image_url);

            if ( !empty($igContainerId) ) {
               $publish = smp_publish_instagram_photo($instagramBusinessAccountId, $igContainerId);    
               error_log('PUBLISHED TO INSTAGRAM: ' . $publish);
            }
         }       
      }   
   }

   /**
    * Publish a link to facebook page
    * https://developers.facebook.com/docs/pages/publishing/
    * Permissions: pages_manage_posts, pages_read_engagement
    */
   function smp_publish_facebook($message, $permalink) {
      $facebookPageId = smp_get_facebook_page_id();
      $pageAccessToken = smp_get_page_access_token( $facebookPageId );

      $requestUrl = smp_get_facebook_graph_url() . $facebookPageId . '/feed?message=' . $message 
      . '&link=' . $permalink . '&access_token=' . $pageAccessToken;

      $response = wp_safe_remote_post( $requestUrl , array(
         'method' => 'POST',
         'timeout' => 45,
         'redirection' => 5,
         'httpversion' => '1.0',
         'blocking' => true,
         'headers' => array(),
         'cookies' => array()
         )
      );

      if ( is_wp_error( $response ) || smp_is_facebook_error( $response )) {
         $error_message = $response->get_error_message();
         error_log('FACEBOOK PUBLISH ERROR: ' . $error_message);
      } else {
         // FB response will be page-post-id
         error_log('PUBLISH FACEBOOK BODY: ' . $response['body']);

         $jsonResponse = json_decode($response['body']);

         // Return page-post-id
         return $jsonResponse->id;
      }
   }

   /**
    * Get Page Access Token
    * https://developers.facebook.com/docs/pages/access-tokens/#get-a-page-access-token
    */
   function smp_get_page_access_token( $facebookPageId = null) {

      if ( empty($facebookPageId) ) {
         $facebookPageId = smp_get_facebook_page_id();
      }

      $response = wp_safe_remote_get( smp_get_facebook_graph_url() . $facebookPageId . '?fields=access_token&access_token=' . $_COOKIE['smp_a'] );

      if ( is_array( $response ) && !is_wp_error( $response ) && !smp_is_facebook_error( $response ) ) {
         $body = $response['body']; // use the content
         error_log('FACEBOOK PAGE ACCESS_TOKEN BODY: ' . $body);

         $jsonResponse = json_decode($response['body']);
         return $jsonResponse->access_token;
      }
   }
?>