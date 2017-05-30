<div class="wrap">
    <h3><?php _e('Interactive Map Settings'); ?></h3>

    <form method="post" action="options.php">
        <?php settings_fields('interactive-map-settings-group'); ?>
        <?php do_settings_sections('interactive-map-settings-group'); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e("Map Width :"); ?></th>
                <td>
                    <input type="text" name="map_width"  value="<?php echo esc_attr(get_option('map_width')); ?>" /> px
                </td>                
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e("Map Height :"); ?></th>
                <td>
                    <input type="text" name="map_height"  value="<?php echo esc_attr(get_option('map_height')); ?>" /> px
                </td>                
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e("Map Type :"); ?></th>
                <td>
                    <select class="regular-select" name="map_type">
                        <option value="world" <?php selected(get_option('map_type'), 'world'); ?>><?php _e("World"); ?></option>
                        <option value="usa" <?php selected(get_option('map_type'), 'usa'); ?>><?php _e("USA"); ?></option>
                        <option value="europe" <?php selected(get_option('map_type'), 'europe'); ?>><?php _e("Europe"); ?></option>
                        <option value="germany" <?php selected(get_option('map_type'), 'germany'); ?>><?php _e("Germany"); ?></option>
                        <option value="africa" <?php selected(get_option('map_type'), 'africa'); ?>><?php _e("Africa"); ?></option>
                        <option value="asia" <?php selected(get_option('map_type'), 'asia'); ?>><?php _e("Asia"); ?></option>
                        <option value="australia" <?php selected(get_option('map_type'), 'australia'); ?>><?php _e("Australia"); ?></option>
                        <option value="algeria" <?php selected(get_option('map_type'), 'algeria'); ?>><?php _e("Algeria"); ?></option>
                        <option value="argentina" <?php selected(get_option('map_type'), 'argentina'); ?>><?php _e("Argentina"); ?></option>
                        <option value="brazil" <?php selected(get_option('map_type'), 'brazil'); ?>><?php _e("Brazil"); ?></option>
                        <option value="canada" <?php selected(get_option('map_type'), 'canada'); ?>><?php _e("Canada"); ?></option>
                        <option value="france" <?php selected(get_option('map_type'), 'france'); ?>><?php _e("France"); ?></option>
                        <option value="greece" <?php selected(get_option('map_type'), 'greece'); ?>><?php _e("Greece"); ?></option>
                        <option value="iran" <?php selected(get_option('map_type'), 'iran'); ?>><?php _e("Iran"); ?></option>
                        <option value="iraq" <?php selected(get_option('map_type'), 'iraq'); ?>><?php _e("Iraq"); ?></option>
                        <option value="north-america" <?php selected(get_option('map_type'), 'north-america'); ?>><?php _e("North America"); ?></option>
                        <option value="russia" <?php selected(get_option('map_type'), 'russia'); ?>><?php _e("Russia"); ?></option>
                        <option value="south-america" <?php selected(get_option('map_type'), 'south-america'); ?>><?php _e("South America"); ?></option>
                        <option value="tunisia" <?php selected(get_option('map_type'), 'tunisia'); ?>><?php _e("Tunisia"); ?></option>
                        <option value="turkey" <?php selected(get_option('map_type'), 'turkey'); ?>><?php _e("Turkey"); ?></option>                        
                    </select>
                </td>                
            </tr>
             <tr valign="top">
                <th scope="row"><?php _e("Map Color :"); ?></th>
                <td>
                    <input type="text" name="map_color" class="mp_color" value="<?php echo esc_attr(get_option('map_color')); ?>" />
                </td>                
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e("Map Background Color :"); ?></th>
                <td>
                    <input type="text" name="map_background_color" class="mp_background" value="<?php echo esc_attr(get_option('map_background_color')); ?>" />
                </td>                
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e("Map Border Color :"); ?></th>
                <td>
                    <input type="text" name="map_border_color" class="mp_border" value="<?php echo esc_attr(get_option('map_border_color')); ?>" />
                </td>                
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e("Map Selected Region Color :"); ?></th>
                <td>
                    <input type="text" name="map_selected_color" class="mp_selected_color" value="<?php echo esc_attr(get_option('map_selected_color')); ?>" />
                </td>                
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e("Map Border Width :"); ?></th>
                <td>
                    <select class="regular-select" name="map_border_width">
                        <option value="1" <?php selected(get_option('map_border_width'), 1); ?>><?php _e("1"); ?></option>
                        <option value="2" <?php selected(get_option('map_border_width'), 2); ?>><?php _e("2"); ?></option>
                        <option value="3" <?php selected(get_option('map_border_width'), 3); ?>><?php _e("3"); ?></option>
                        <option value="4" <?php selected(get_option('map_border_width'), 4); ?>><?php _e("4"); ?></option>
                        <option value="5" <?php selected(get_option('map_border_width'), 5); ?>><?php _e("5"); ?></option>
                        <option value="6" <?php selected(get_option('map_border_width'), 6); ?>><?php _e("6"); ?></option>
                        <option value="7" <?php selected(get_option('map_border_width'), 7); ?>><?php _e("7"); ?></option>
                        <option value="8" <?php selected(get_option('map_border_width'), 8); ?>><?php _e("8"); ?></option>
                        <option value="9" <?php selected(get_option('map_border_width'), 9); ?>><?php _e("9"); ?></option>
                        <option value="10" <?php selected(get_option('map_border_width'), 10); ?>><?php _e("10"); ?></option>
                    </select>
                </td>                
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e("Map Border Opacity :"); ?></th>
                <td>
                    <select class="regular-select" name="map_border_opacity">
                        <option value="0.25" <?php selected(get_option('map_border_opacity'), 0.25); ?>><?php _e("0.25"); ?></option>
                        <option value="0.50" <?php selected(get_option('map_border_opacity'), 0.50); ?>><?php _e("0.50"); ?></option>
                        <option value="1" <?php selected(get_option('map_border_opacity'), 1); ?>><?php _e("1"); ?></option>                       
                    </select>
                </td>                
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e("Map Zoom :"); ?></th>
                <td>
                    <select class="regular-select" name="map_zoom">
                        <option value="true" <?php selected(get_option('map_zoom'), 'true'); ?>><?php _e("Enable"); ?></option>
                        <option value="false" <?php selected(get_option('map_zoom'), 'false'); ?>><?php _e("Disable"); ?></option>                       
                    </select>
                </td>                
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e("Map MultiSelect Regions/Countries :"); ?></th>
                <td>
                    <select class="regular-select" name="map_multiselect">
                        <option value="true" <?php selected(get_option('map_multiselect'), 'true'); ?>><?php _e("Enable"); ?></option>
                        <option value="false" <?php selected(get_option('map_multiselect'), 'false'); ?>><?php _e("Disable"); ?></option>                       
                    </select>
                </td>                
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e("Map Region Hover Color :"); ?></th>
                <td>
                    <input type="text" name="map_region_hover_color" class="mp_region_hover" value="<?php echo esc_attr(get_option('map_region_hover_color')); ?>" />
                </td>                
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e("Show Labels :"); ?></th>
                <td>
                    <select class="regular-select" name="map_labels">
                        <option value="true" <?php selected(get_option('map_labels'), 'true'); ?>><?php _e("Enable"); ?></option>
                        <option value="false" <?php selected(get_option('map_labels'), 'false'); ?>><?php _e("Disable"); ?></option>                       
                    </select>
                </td>                
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e("Show ToolTip :"); ?></th>
                <td>
                    <select class="regular-select" name="map_tooltip">
                        <option value="true" <?php selected(get_option('map_tooltip'), 'true'); ?>><?php _e("Enable"); ?></option>
                        <option value="false" <?php selected(get_option('map_tooltip'), 'false'); ?>><?php _e("Disable"); ?></option>                       
                    </select>
                </td>                
            </tr>
        </table>
        <?php submit_button(); ?>
    </form>
</div>
