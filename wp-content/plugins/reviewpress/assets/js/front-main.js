jQuery(document).ready(function($) {


  $('#starType').raty({
    cancel   : false,
    half     : true,
    starType : 'i',
    starHalf : 'wpbr-star-half',
    starOff  : 'wpbr-star-off',
    starOn   : 'wpbr-star-on',
    score    : function(){
      if ( $(this).data('score') !== '' ) {
        return $(this).data('score');
      }
    }
  });

  $('#heartType').raty({
    cancel   : false,
    half     : true,
    starType : 'b',
    starHalf : 'wpbr-heart-half',
    starOff  : 'wpbr-heart-off',
    starOn   : 'wpbr-heart-on',
    score    : function(){
      if ( $(this).data('score') !== '' ) {
        return $(this).data('score');
      }
    }
  });

  $('form[name="wpbr_review_form"]').on('submit', function( event ) {
    event.preventDefault();

    if( $('.g-recaptcha').length > 0 && grecaptcha.getResponse() == "") {
      $('.captcha-error').html("Please verify Google ReCaptcha");
      return;
    }else{
      $('.captcha-error').html();
    }
    var Form = $(this);
    var pageId = Form.find('input[name="wpbr_review_post_id"]').val();
    console.log(pageId);
    var reviewsArea =  $(this).parent().siblings('.reviewpres-reviews-wrapper');
    $(this).next('.reviewpres-reviews-wrapper').html('he;;po');
    if( $(this).find("input[name='score']").val() === "" ){
      alert("Please Rate Review First");
      return;
    }
    var data = $(this).serialize() + '&action=reviewpress_submit';
    $.ajax({
      url  : reviewpress.ajaxurl,
      type : 'POST',
      data : data,
      beforeSend : function() {
        $('.submit_button img').show();
        $(this).find("input[name='wpbr_review_form_submit']").prop('disabled', true);
      }
    })
    .done(function(response) {
      var myJSON = JSON.parse(response);
      Form.trigger("reset");
      $('.submit_button img').hide();
      $('.reviewpress-message').addClass('reviewpress-success').html(myJSON.message);

      if ( ! myJSON.auto_approve ) { return; }
      $.ajax({
        url : reviewpress.ajaxurl,
        type: 'POST',
        data: {
          action: 'get_ajax_reviews',
          pageId : pageId,
        }
      })
      .done(function(response) {
        reviewsArea.html( response );
      });

    });

  });
});

// window.onload = function() {
//   if(jQuery("#re-captcha").length !== 0){
//     var recaptcha = document.forms["review_press_form"]["g-recaptcha-response"];
//     recaptcha.required = true;
//     recaptcha.oninvalid = function(e) {
//       // do something
//       alert("Please complete the captcha");
//     }
//   }
// }
