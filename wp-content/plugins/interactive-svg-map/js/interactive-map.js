jQuery(document).ready(function () {

    jQuery('#vmap').vectorMap({
        map: interactivemap.map + "_en",
        backgroundColor: interactivemap.map_background_color,
        borderColor: interactivemap.map_border_color,
        borderOpacity: interactivemap.map_border_opacity,
        borderWidth: interactivemap.map_border_width,
        color: interactivemap.map_color,
        enableZoom: interactivemap.map_zoom,
        hoverColor: interactivemap.map_region_hover_color,
        hoverOpacity: null,
        normalizeFunction: 'linear',
        scaleColors: ['#b6d6ff', '#005ace'],
        selectedColor: interactivemap.map_selected_color,
        selectedRegions: null,
        showTooltip: interactivemap.map_tooltip,
        //showLabels: interactivemap.map_labels,
        multiSelectRegion: interactivemap.map_multiselect,
    });
});