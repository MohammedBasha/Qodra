/* global jQuery, document */
jQuery(document).ready(function($){
    // Add an arabic class for the Arabic html element
    var htmlElm = $('html'),
        htmlAttr = htmlElm.attr('lang');
    if (typeof htmlAttr !== typeof undefined && htmlAttr !== false) {
        htmlAttr == 'ar-aa' ? htmlElm.addClass('html-ar') : htmlElm.removeClass('html-ar')
    }
    
    // Change the Off Canvas Menu's title in arabic pages
    if (htmlAttr == 'ar-aa') {
        // The menu title in mobile view
        $('.t3-off-canvas-header-title').text('القائمة الرئيسية');
        
        // The read more button for articles
        $('.k2ReadMore, .customLayout .readmore a span').text('إقرأ المزيد');
        
        // The Contact us Page map module title
        $('.map_contact .module-title, .custom-footer-map .module-title span').text('موقعنا');
        
        // The contact form filed's placeholder
        $('#jform_contact_name').attr('placeholder','اسمك');
        $('#jform_contact_email').attr('placeholder','البريد الإلكترونى كمثال: email@domain.tld');
        $('#jform_contact_emailmsg').attr('placeholder','موضوعك');
        $('#jform_contact_message').attr('placeholder','رسالتك');
        
        // The Careers Pages
        $('.careers-view #categorylist_header_title').text('الوظيفة');
        $('.careers-view #applying-deadline-title').text('إنتهاء التقديم');
        $('.careers-view .tag-location .tag-label').text('المكان:');
    }
        
    // set the href to "#" for Sections Our products and Our Customers
    $("#bottom_mainbody_3 .defaultLayout .bt-inner a, .metro-item .mi-back .top h5 a").attr("href", "javascript:void(0)");

    // Add a target='_blank' for the social links
    $('.iconLink').each(function () {
        $(this).attr('target', '_blank');
    });
    
    // Contact us email field validation
    document.formvalidator.setHandler('email', function(value) {
      var regex= /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      return regex.test(value);
    });
});