<?php
   /**
    * !!! Metabox calls publish_post hook twice
    */

   function smp_add_meta_box(){
      add_meta_box( 'smp-meta-box', 'Social Media', 'smp_init_meta_box', 'post', 'side', 'high' );
   }

   /**
    * Initialize Meta Box
    */
   function smp_init_meta_box(){ 

      if ( get_post_status ( get_the_id() ) == 'publish' ) {
         $instagramMeta = get_post_meta( get_the_id(), 'smp-ig-published', true );

         if ( !empty($instagramMeta) ) {
         ?> 
         <div class="components-panel__row">      
            <p class="dashicons-before dashicons-instagram text-success">Published: <?php echo $instagramMeta ?></p> 
            <button id="instagram-reupload" class="components-button is-secondary is-small">Reupload</button>
         </div>
         <?php
         }

         $facebookMeta = get_post_meta( get_the_id(), 'smp-fb-published', true );
         if ( !empty($facebookMeta) )
         {
            ?>
            <div class="components-panel__row">      
               <p class="dashicons-before dashicons-facebook text-success">Published: <?php echo $facebookMeta ?></p> 
               <button id="facebook-reupload" class="components-button is-secondary is-small">Reupload</button>
            </div>
            <?php
         }
      } else {
         ?>
            <p>Not published</p>
         <?php
      }
   }

   add_action( 'add_meta_boxes', 'smp_add_meta_box' );

   /**
    * Reupload post to instagram
    */
   function smp_instagram_reupload() {
      $post_id = sanitize_text_field($_POST['post_id']);  

      if ( !empty($post_id) ) {
         $title = get_the_title( $post_id );
         $post   = get_post( $post_id );

         $post_thumbnail_id = get_post_thumbnail_id( $post );
         $featured_image_url = wp_get_attachment_image_url( $post_thumbnail_id, 'original' );

         smp_post_instagram( $title, $featured_image_url );
         update_post_meta( $post_id, 'smp-ig-published' , date("d.m.Y H:i"));    

         // Show notice on gutenberg screen
         echo $title . ' uploaded to Instagram.';
      } else {
         echo 'No post ID available';
      }
   }

   /**
    * Reupload to Facebeook
    */
   function smp_facebook_reupload() {
      $post_id = sanitize_text_field($_POST['post_id']);  

      if ( !empty($post_id) ) {
         $title = get_the_title( $post_id );
         $post   = get_post( $post_id );

         smp_publish_facebook( $title, get_post_permalink( $post) );
         update_post_meta( $post_id, 'smp-fb-published' , date("d.m.Y H:i"));    

         // Show notice on gutenberg screen
         echo $title . ' uploaded to Instagram.';
      } else {
         echo 'No post ID available';
      }
   }

   add_action( 'wp_ajax_smp_instagram_reupload', 'smp_instagram_reupload' );
?>