<?php
   /**
    * Listen for publishing of a new post
    */
   function smp_publish_post( $post_id, $post ) { 
      // Only runs if the post has status `publish`
      if ( get_post_status ( $post_id ) == 'publish') {
         // Check cookie is set and valid
         if (isset($_COOKIE['smp_a'])) {
            // Retrive image thumbnail
            $post_thumbnail_id = get_post_thumbnail_id( $post );
            $featured_image_url = wp_get_attachment_image_url( $post_thumbnail_id, 'original' );           

            /* -------------------------------------------------------------------------- */
            /*                                  INSTAGRAM                                 */
            /* -------------------------------------------------------------------------- */

            // Get instagram published date
            $instagramPublishedPostMeta = get_post_meta( $post_id, 'smp-ig-published', true );
            
            if( $featured_image_url && // Featured image exist
                esc_attr( get_option('smp_instagram_auto_publish')) && // Instagram auto publish is enabled
                empty( $instagramPublishedPostMeta ) // Post already published
               ) {          
               // Set Instagram published flag
               add_post_meta( $post_id, 'smp-ig-published' , date("d.m.Y H:i"));    
               smp_post_instagram($post->post_title, $featured_image_url);  
            }

            /* -------------------------------------------------------------------------- */
            /*                                  FACEBOOK                                  */
            /* -------------------------------------------------------------------------- */

            $facebookPublishedPostMeta = get_post_meta( $post_id, 'smp-fb-published', true );

            if ( esc_attr( get_option('smp_facebook_auto_publish')) && empty($facebookPublishedPostMeta) ) {

               smp_publish_facebook( $post->post_title, get_permalink( $post_id ) );

               // Set Facebook published flag
               add_post_meta( $post_id, 'smp-fb-published' , date("d.m.Y H:i"));    
            }
         }       
      }
   }
   
   add_action( 'publish_post', 'smp_publish_post', 10, 3 );
?>

