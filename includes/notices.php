<?php
   /**
    * Show Dismissible Notification
    * https://developer.wordpress.org/reference/hooks/admin_notices/
    */
   function smp_show_notification($message, $type = "error") {
      $class = 'notice notice-' . $type . ' is-dismissible';   
      printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( __( $message ) ) );
   }   

   /**
    * SESSION DOCUMENTATION
    * https://www.sitepoint.com/displaying-errors-from-the-save-post-hook-in-wordpress/
    */
   if ( !session_id() ) {
      session_start();
   }

   function smp_admin_notices() {         
      // SESSION
      if ( array_key_exists( 'smp_errors', $_SESSION ) ) {

         $message = $_SESSION['smp_errors'];

         smp_show_notification($message);

         unset( $_SESSION['smp_errors'] );
      }       
   }

   add_action('admin_notices', 'smp_admin_notices');
?>

