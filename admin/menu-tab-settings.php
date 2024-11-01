<div class="wrap">
   <h2>App Settings</h2>

   <form method="post" action="options.php">
      <?php settings_fields( 'smp-plugin-settings-group' ); ?>
      <?php do_settings_sections( 'smp-plugin-settings-group' ); ?>

      <table class="form-table" aria-describedby="social-media-publish-settings">
         <tr>
            <th scope="row">Client ID</th>
            <td>
               <input type="text" name="smp_fb_app_id" value="<?php echo esc_attr( get_option('smp_fb_app_id') ); ?>" />
            </td>
         </tr>
            
         <tr>
            <th scope="row">Client Secret</th>
            <td>
               <input type="text" name="smp_fb_app_secret" value="<?php echo esc_attr( get_option('smp_fb_app_secret') ); ?>" />
            </td>
         </tr>   

         <tr>
            <th scope="row">Instagram</th>
            <td>
               <label>
                  <div class="smp-tooltip"><span class="dashicons dashicons-editor-help"></span>
                     <span class="smp-tooltiptext">Required permissions: ads_management, business_management, instagram_basic, instagram_content_publish, pages_read_engagement</span>
                  </div>

                  <input type="checkbox" name="smp_instagram_auto_publish" value="1" 
                     <?php checked( esc_attr( get_option('smp_instagram_auto_publish')), 1 ); ?>
                  > Enable auto publish after post						
               </label>
            </td>
         </tr>    

         <tr>
            <th scope="row">Facebook</th>
            <td>
               <label>               
                  <div class="smp-tooltip"><span class="dashicons dashicons-editor-help"></span>
                     <span class="smp-tooltiptext">Required permissions: pages_manage_posts, pages_read_engagement</span>
                  </div>

                  <input type="checkbox" name="smp_facebook_auto_publish" value="1" 
                     <?php checked( esc_attr( get_option('smp_facebook_auto_publish')), 1 ); ?>
                  > Enable auto publish after post						
               </label>
            </td>
         </tr>   
      </table>    
      
      <?php submit_button(); ?>      
   </form>

   <h2>Facebook API Graph</h2>

   <table class="form-table" aria-describedby="social-media-publish-settings">
      <tr>
         <th scope="row" style="width: 80px">
            <input 
               type="button" 
               name="authorize"
               class="button action" 
               value="Authorize" 
               onclick='showLoginDialog(
                  "<?php echo esc_attr( get_option("smp_fb_app_id") ); ?>",
                  "<?php echo esc_attr( get_option("smp_fb_app_secret") ); ?>",
               )'
            >
         </th>
         <td>
            <?php smp_check_authentication(); ?>
         </td>
      </tr>
   </table>
</div>

<?php 
   /**
    * Check cookie is valid and set
    */
   function smp_check_authentication() {
      if (isset($_COOKIE['smp_a'])) {
         ?> <p class="dashicons-before dashicons-yes-alt text-success">You are authorized</p> <?php
      } else {
         ?> <p class="dashicons-before dashicons-dismiss text-error">Not authorized, please reassign to Facebook.</p> <?php
      }     
   }

   /* -------------------------------------------------------------------------- */
   /*                                Debug Section                               */
   /*                              Used for Developing purposes
   /* -------------------------------------------------------------------------- */
   
   // the button has been pressed 
   if (isset($_POST['test_button'])) {
      
      error_log('TEST BUTTON CLICKED'); 
   }
   ?>
   
   <!-- <h2>DEBUG Section</h2>

   <form action="options-general.php?page=social-media-publish" method="post">
      <?php // wp_nonce_field('test_button_clicked'); ?>
      <input type="hidden" value="true" name="test_button" />
      <?php // submit_button('Test'); ?>
   </form> -->




