/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


jQuery(document).ready(function() {
    // Work listing
    if (jQuery('#grid').length > 0)
        jQuery('#grid').mixItUp();

    new grid3D(document.getElementById('grid3d'));

    itemResize();
    jQuery(window).resize(function() {
        itemResize();
    });

});



function itemResize() {
    var mod_w = jQuery('#mod-bt-portfolio-gallery').width();
    var ratio = Math.floor(mod_w / modPortfolioCfg.item_min_w);
    var el_width = Math.floor(mod_w / ratio);
    jQuery('#mod-bt-portfolio-gallery figure').css({"width": el_width + "px"});
}

