<?php
   /**
    * Add Menu Page to Dashboard
    */
   function smp_admin_menu(){
      add_menu_page( 
         'Social Media Publish',    // Page Title
         'Social Media',            // Menu Title
         'manage_options',          // Capability
         'social-media-publish',    // Page slug
         'smp_init_menu_page',      // Callback to print html
         'dashicons-instagram',     // Icon url
         30                         // Menu position
      );
   }

   /**
    * Initialize Admin Menu Page
    */
   function smp_init_menu_page(){
      // Get the active tab from the $_GET param
      $defaultTab = 'settings';
      $tab = isset( $_GET['tab'] ) ? sanitize_text_field($_GET['tab']) : $defaultTab;

      ?>
      <!-- Our admin page content should all be inside .wrap -->
      <div class="wrap">
         <!-- Print the page title -->
         <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        
         <!-- Here are our tabs -->
         <nav class="nav-tab-wrapper">
            <a href="?page=social-media-publish&tab=settings" class="nav-tab <?php if($tab==='settings'):?>nav-tab-active<?php endif; ?>">Settings</a>
               <a href="?page=social-media-publish&tab=howto" class="nav-tab <?php if($tab==='howto'):?>nav-tab-active<?php endif; ?>">How to</a>
         </nav>

         <div class="tab-content">
         <?php 
            switch($tab) :
               case 'settings':   
                  include('menu-tab-settings.php');      
                  break;
               case 'howto':
                  include('menu-tab-howto.php');
                  break;
               default:
                  echo 'No content on this tab found';
                  break;
            endswitch; 
         ?>
         </div>
      </div>
<?php
   }
   /**
    * Loads Admin Scripts and Styles
    */
   function smp_load_admin_scripts() {      
      // Enqueue styles
      wp_enqueue_style('smp-admin', plugins_url('css/smp-admin.css', __FILE__ ));

      // Enqueue scripts
      wp_enqueue_script('smp-admin', plugins_url('/js/smp-admin.js', __FILE__), array());
      // https://connect.facebook.net/en_US/sdk.js
      wp_enqueue_script('facebook-sdk', plugins_url('/js/facebook-sdk.js', __FILE__), array(), '10.0', true);
      wp_enqueue_script('facebook', plugins_url('/js/facebook.js', __FILE__), array(), '1.0', true);
   }

   /**
    * Enqueue Gutenberg scripts
    */
   function smp_load_guten_enqueue() {
      wp_register_script( 'smp-gutenberg', plugins_url( '/js/smp-gutenberg.js', __FILE__ ), array('wp-editor') );

      wp_localize_script( 'smp-gutenberg', 'smp_gutenberg', array(
         'ajaxurl' => admin_url( 'admin-ajax.php' ),
         'post_id' => get_the_id()
      ));
      wp_enqueue_script( 'smp-gutenberg'); 
   }

   /**
    * Register plugin settings
    */
   function smp_register_plugin_settings() {
      register_setting( 'smp-plugin-settings-group', 'smp_fb_app_id' );
      register_setting( 'smp-plugin-settings-group', 'smp_fb_app_secret' );
      register_setting( 'smp-plugin-settings-group', 'smp_instagram_auto_publish' );  
      register_setting( 'smp-plugin-settings-group', 'smp_facebook_auto_publish' );     
   }

   /**
    * Initialize Facebook SDK in footer of admin page
    */
   function smp_init_facebook_sdk() { ?>
      <script type="text/javascript">
         jQuery(document).ready(function($) {
            initFacebookSdk('<?php echo esc_attr( get_option("smp_fb_app_id") ); ?>');        
         });
      </script> 
      <?php
   }



   /**
    * Actions
    */
   add_action( 'admin_enqueue_scripts','smp_load_admin_scripts' );
   add_action( 'enqueue_block_editor_assets', 'smp_load_guten_enqueue' );
   add_action( 'admin_menu', 'smp_admin_menu' );  
   add_action( 'admin_init', 'smp_register_plugin_settings' );
   add_action( 'admin_footer', 'smp_init_facebook_sdk' );
?>

