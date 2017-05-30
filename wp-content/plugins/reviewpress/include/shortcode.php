<div class="shortcode-content">
    <div class="reviewpress-shortcode-edit-popup">
        <h3 class="popup-title"><?php esc_html_e( 'Select metrics and dimensions, sort them with metrics and show stats to your visitors (roles).', 'wp-reviewpress'); ?></h3>
        <div class="reviewpress-shortcode-edit-ui">
            <div class="reviewpress-shortcode-edit-ui-selector">

                <div class="selector-item">
                    <label for=""><?php esc_html_e( 'Show Reviews of Other Post/Page', 'wp-reviewpress'); ?></label>
                    <input type="checkbox" min="1" value="5" id="reviewpress_other_post" />
                </div>

                <div class="selector-item" id="reviewpress_other_post_id_container" style="display:none">
                    <label for=""><?php esc_html_e( 'Enter ID of Post/Page', 'wp-reviewpress'); ?></label>
                    <input type="number" min="0"  id="reviewpress_other_post_id" />
                </div>

                <div class="selector-item" >
                    <label for=""><?php esc_html_e( 'Show aggregate rating', 'wp-reviewpress'); ?></label>
                    <input type="checkbox" min="0"  id="reviewpress_aggregate" />
                </div>


                <div class="selector-item">
                    <label for="reviewpress_max_result"><?php esc_html_e( 'No of Reviews: ', 'wp-reviewpress'); ?></label>
                    <input type="number" min="1" value="5" id="reviewpress_max_result" />
                </div>

                <div style="clear:both"></div>
                
                <div class="selector-item right-side">
                    <button id="insert_shortcode" class="button-primary" style="padding:0 18px;"><?php esc_html_e( 'Ok', 'wp-reviewpress'); ?></button>
                    <button id="cancel" class="button"><?php esc_html_e( 'Cancel', 'wp-reviewpress'); ?></button>
                </div>
            </div>
        </div>

    </div>

</div>

<script type="text/javascript">

    jQuery(document).ready(function($) {


      $("#reviewpress_other_post").on('change' , function(event) {
        if(  $("#reviewpress_other_post").is(':checked')){
          $("#reviewpress_other_post_id_container").show();
        }else{
          $("#reviewpress_other_post_id_container").hide();
        }
      });

      $("#reviewpress_aggregate").on('change', function(event) {
        if ( $(this).is(':checked') ) {
            $("#reviewpress_max_result").parent().hide();
        }else{
          $("#reviewpress_max_result").parent().show();
        }
      });

      $('#insert_shortcode').click( function(){
          var id             = $("#reviewpress_other_post_id").val();
          var number_of_post = $("#reviewpress_max_result").val();
          var shortcode      = '';

          if ( $("#reviewpress_other_post").is(':checked') ) {
              shortcode = '[REVIEWPRESS_SHOW id="' + id + '"  number="' + number_of_post + '"]';
          } else {
            shortcode = '[REVIEWPRESS_SHOW  number="' + number_of_post + '"]';
          }

          if ( $("#reviewpress_aggregate").is(':checked') ) {
            if ( $("#reviewpress_other_post").is(':checked') ) {
                shortcode = '[REVIEWPRESS_RICH_SNIPPET  id="' + id + '"]';
            } else {
              shortcode = '[REVIEWPRESS_RICH_SNIPPET ]';
            }
          }
              tinyMCE.activeEditor.execCommand( 'mceInsertContent', 0, shortcode );
              tb_remove();
      });

      $('#cancel').click( function(){
        tb_remove();
      });

    });

</script>
