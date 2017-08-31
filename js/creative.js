/*!
 * Start Bootstrap - Creative Bootstrap Theme (http://startbootstrap.com)
 * Code licensed under the Apache License v2.0.
 * For details, see http://www.apache.org/licenses/LICENSE-2.0.
 */

(function($) {
    "use strict"; // Start of use strict

    // jQuery for page scrolling feature - requires jQuery Easing plugin
    $('a.page-scroll').bind('click', function(event) {
        var $anchor = $(this);
        $('html, body').stop().animate({
            scrollTop: ($($anchor.attr('href')).offset().top - 50)
        }, 1250, 'easeInOutExpo');
        event.preventDefault();
    });

    // Highlight the top nav as scrolling occurs
    $('body').scrollspy({
        target: '.navbar-fixed-top',
        offset: 51
    })

    // Closes the Responsive Menu on Menu Item Click
    $('.navbar-collapse ul li a').click(function() {
        $('.navbar-toggle:visible').click();
    });

    // Fit Text Plugin for Main Header
/*
    $("h1").fitText(
        1.2, {
            minFontSize: '35px',
            maxFontSize: '65px'
        }
    );
*/

    // Offset for Main Navigation
    $('#mainNav').affix({
        offset: {
            top: 100
        }
    })

    // Initialize WOW.js Scrolling Animations
    //new WOW().init();
    
    if (isInView($('#counting'))){
    var total = $(".found").val();    
    var comma_separator_number_step = $.animateNumber.numberStepFactories.separator(',')
    $('#lines').animateNumber({ number: total,  numberStep: comma_separator_number_step });
   }

/*
	$("#timespan")
 		.countdown("2015/12/01", function(event) {
     	$(this).text(
     	event.strftime('%D days %H:%M:%S')
     	);
	});
*/

})(jQuery); // End of use strict


$(window).scroll(function(){
   if (isInView($('#counting'))){
    var total = $(".found").val();    
    var comma_separator_number_step = $.animateNumber.numberStepFactories.separator(',')
    $('#lines').animateNumber({ number: total,  numberStep: comma_separator_number_step });
    
   }
      
})


function isInView(elem){
 //  return $(elem).offset().top - $(window).scrollTop() < $(elem).height() ;
}