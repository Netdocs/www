jQuery(document).ready(function($) {

  
   if ( $("#wpbr_form\\[google_captcha_site_key\\] , #wpbr_form\\[google_captcha_secret_key\\] , #wpbr_display\\[review_users\\]").hasClass('hidden') ) {
      $("#wpbr_form\\[google_captcha_site_key\\] , #wpbr_form\\[google_captcha_secret_key\\] , #wpbr_display\\[review_users\\]").closest('tr').hide();
   }

   $("label[for='wpuf-wpbr_form\\[google_captcha\\]']").on('click', function(event) {
      if($("label[for='wpuf-wpbr_form\\[google_captcha\\]']").children('input[type="checkbox"]').is(':checked')){
         $("#wpbr_form\\[google_captcha_site_key\\] , #wpbr_form\\[google_captcha_secret_key\\]").closest('tr').show();
         $("#wpbr_form\\[google_captcha_site_key\\] , #wpbr_form\\[google_captcha_secret_key\\]").removeClass('hidden');
      }else {
         $("#wpbr_form\\[google_captcha_site_key\\] , #wpbr_form\\[google_captcha_secret_key\\]").closest('tr').hide();
         $("#wpbr_form\\[google_captcha_site_key\\] , #wpbr_form\\[google_captcha_secret_key\\]").addClass('hidden');
      }
   });

   if($("label[for='wpuf-wpbr_form\\[google_captcha\\]']").children('input[type="checkbox"]').is(':checked')){
      $("#wpbr_form\\[google_captcha_site_key\\] , #wpbr_form\\[google_captcha_secret_key\\]").closest('tr').show();
      $("#wpbr_form\\[google_captcha_site_key\\] , #wpbr_form\\[google_captcha_secret_key\\]").removeClass('hidden');
   }




   $("label[for='wpuf-wpbr_display\\[review_authorization\\]\\[custom\\]']").on('click', function(event) {
      if($("label[for='wpuf-wpbr_display\\[review_authorization\\]\\[custom\\]']").children("input[type='radio']").is(':checked')){
         $("#wpbr_display\\[review_users\\]").closest('tr').show();
         $("#wpbr_display\\[review_users\\]").removeClass('hidden');
      }
   });

   $("label[for='wpuf-wpbr_display\\[review_authorization\\]\\[login\\]'] , label[for='wpuf-wpbr_display\\[review_authorization\\]\\[all\\]']").on('click', function(event) {
      if($("label[for='wpuf-wpbr_display\\[review_authorization\\]\\[login\\]'] , label[for='wpuf-wpbr_display\\[review_authorization\\]\\[all\\]']").children("input[type='radio']").is(':checked')){
         $("#wpbr_display\\[review_users\\]").closest('tr').hide();
         $("#wpbr_display\\[review_users\\]").addClass('hidden');
      }
   });

   if($("label[for='wpuf-wpbr_display\\[review_authorization\\]\\[custom\\]']").children("input[type='radio']").is(':checked')){
      $("#wpbr_display\\[review_users\\]").closest('tr').show();
      $("#wpbr_display\\[review_users\\]").removeClass('hidden');
   }


   $("#wpbr_save_setting_top , #wpbr_save_setting_bottom").on('click', function(event) {
      event.preventDefault();
      // $("#wpbr_display input[type='submit']").trigger('click');
      if ($("#wpbr_display").is(':visible') ){
         $("#wpbr_display input[type='submit']").trigger('click');
      }else if($("#wpbr_reviews").is(':visible')){
         $("#wpbr_reviews input[type='submit']").trigger('click');
      }else if($("#wpbr_form").is(':visible')){
         $("#wpbr_form input[type='submit']").trigger('click');
      }else if($("#wpbr_custom_css").is(':visible')){

         $("#wpbr_custom_css\\[custom_css\\]").html(editor.getValue());
         $("#wpbr_custom_css input[type='submit']").trigger('click');
      }
   });

   if($("#wpbr_custom_css\\[custom_css\\]").length !== 0){

      var editor = CodeMirror.fromTextArea(document.getElementById("wpbr_custom_css[custom_css]"), {
         lineNumbers: true,
         lineWrapping : true,
      });


      $('.CodeMirror').each(function(i, el){
         setTimeout(function() {
            el.CodeMirror.refresh();
         },1);
      });

      editor.on('change',function(cMirror){
         // get value right from instance
         $(".setting-notification").slideDown();
      });

   }

   $(".button.twitter ,.button.facebook").on('click', function(event) {
      event.preventDefault();

      var width  = 575,
      height = 400,
      left   = ($(window).width()  - width)  / 2,
      top    = ($(window).height() - height) / 2,
      url    = this.href,
      opts   = 'status=1' +
      ',width='  + width  +
      ',height=' + height +
      ',top='    + top    +
      ',left='   + left;

      window.open(url, '', opts);
   });

   $("#wpbr_display input[type='radio'] ,#wpbr_reviews input[type='radio'] , #wpbr_reviews select ,#wpbr_reviews input[type='number'] , #review-setting input[type='radio'] , #review-setting input[type='text'] , #review-setting input[type='checkbox']").on('change', function(event) {
      $(".setting-notification").slideDown();
   });

   $('.toplevel_page_all_reviews .wp-menu-image').replaceWith('<div class="icon-hover_starheart wp-menu-image"><span class="path1"></span><span class="path2"></span></div>');

});
