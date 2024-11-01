
console.log('LOAD GUTENBERG');


/**
 * POST SAVING
 */
wp.data.subscribe(function () {
   var isSavingPost = wp.data.select('core/editor').isSavingPost();
   var isAutosavingPost = wp.data.select('core/editor').isAutosavingPost();

   if (isSavingPost && !isAutosavingPost) {
      // Here goes your AJAX code ......
   }
});


jQuery(document).ready(function () { // wait for page to finish loading 

   /* -------------------------------------------------------------------------- */
   /*                                   Notices                                  */
   /* -------------------------------------------------------------------------- */
   function showNotice(message, type = "success") {
      wp.data.dispatch('core/notices').createNotice(
         type, // Can be one of: success, info, warning, error.
         message, // Text string to display.
         {
            isDismissible: true, // Whether the user can dismiss the notice.
            // Any actions the user can perform.
            // actions: [
            //    {
            //       url: '#',
            //       label: 'View post',
            //    },
            // ],
         }
      );
   }


   /* -------------------------------------------------------------------------- */
   /*                                  Reupload                                  */
   /* -------------------------------------------------------------------------- */

   function reupload(socialMediaName) {
      // Disable button during send process
      jQuery(`#${socialMediaName}-reupload`).attr("disabled", true);

      jQuery.ajax({
         type: 'POST',
         url: smp_gutenberg.ajaxurl,
         data: {
            action: `smp_${socialMediaName}_reupload`,
            post_id: smp_gutenberg.post_id
         },
         success: function (data) {
            data = data.replace("0", "");
            showNotice(data);

            // Enable button
            jQuery(`#${socialMediaName}-reupload`).attr("disabled", false);
         },
         error: function (errorThrown) {
            var errorMessage = `${errorThrown['status']} ${errorThrown['statusText']}: ${errorThrown['responseText']}`;
            showNotice(errorMessage, "error");

            // Enable button
            jQuery(`#${socialMediaName}-reupload`).attr("disabled", false);
         }
      });
   }

   /* -------------------------------------------------------------------------- */
   /*                                Button Action                               */
   /* -------------------------------------------------------------------------- */

   // https://www.user-mind.de/ajax-richtig-in-wordpress-nutzen/
   jQuery("#instagram-reupload").click(function () {
      reupload("instagram");
   });

   jQuery("#facebook-reupload").click(function () {
      reupload("facebook");
   });
});


