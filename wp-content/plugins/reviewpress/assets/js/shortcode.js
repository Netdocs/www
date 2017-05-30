/*
 * Tinymce plugin for reviewpress
 */

(function() {
   tinymce.create('tinymce.plugins.reviewpress', {

      init : function(editor, url) {

            editor.addButton( 'reviewpressbutton', {
                  title: 'ReviewPress Shortcodes',
                  type: 'menubutton',
                  icon: 'icon reviewpress-shortcodes-icon',
                  menu: [

                           {
                             text: 'Show Form',
                             onclick : function(){
                                   editor.insertContent( '[REVIEWPRESS_FORM]');
                             }
                           },
                           {
                             text: 'Show Reviews',
                             onclick : function(){
                                   editor.insertContent( '[REVIEWPRESS_SHOW]');
                             }
                           },
                           {
                             text : 'Advance',
                             onclick : function(){
                               tb_show("reviewpress Advanced Shortcodes: Use it according to your needs.", "admin-ajax.php?action=reviewpress_advanced_shortcode");
                               tinymce.DOM.setStyle(["TB_overlay", "TB_window", "TB_load"], "z-index", "999999");
                               var tb = jQuery("#TB_window");
                               if (tb)
                               {
                                 var tbCont = tb.find('#TB_ajaxContent');
                                 tbCont.css({ width : 'auto', height : 'auto',background : '#efefef' });

                               }
                             }
                           }
                     ]
            });
      },
      createControl : function(n, cm) {
         return null;
      },
      getInfo : function() {
         return {
            longname : "ReviewPress Shortcodes",
            author : 'WPBrigade',
            authorurl : 'https://wpbrigade.com/',
            infourl : 'https://wpbrigadecom/',
            version : "1.1"
         };
      }
   });

   tinymce.PluginManager.add('reviewpressbutton', tinymce.plugins.reviewpress);
})();
