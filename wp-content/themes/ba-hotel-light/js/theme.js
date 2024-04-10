(function($){
	"use strict";

////////////////////////////////////

$(document).ready(function(){
    
    $('p:empty').remove();
    
    ////////Lazy load images////////
    
     var lazy_src = 'imgsrc';
    
     if ($(window).width() <= 510){
            lazy_src = 'imgsrc-sm';
     } else if ($(window).width() <= 1140){
            lazy_src = 'imgsrc-md';
     }    
    
     $(document).find('div[data-imgsrc]').each(function(el){
        $(this).css( 'background-size', '');
        $(this).css( 'background-image', $(this).data(lazy_src) );
     }); 
    
    ////////Front page carousel//////////////
    
    if ($('#home_carousel').length > 0){
        
        var first_main_slide = $('#home_carousel').find("img[data-imgsrc]").first();
        first_main_slide.attr("src", first_main_slide.data(lazy_src));
        
        first_main_slide.one("load", function() {
            // do stuff
            $('#home_carousel .carousel-item-first-time').removeClass('carousel-item-first-time');
        }).each(function() {
            if(this.complete) {
                $(this).trigger('load'); 
                }
        });
        
        first_main_slide.removeAttr("data-imgsrc");
        first_main_slide.parent().find("img.carousel-item-spinner").remove();
        
        $('#home_carousel').carousel({
            interval: 5000
        });
        
        $('#home_carousel').on('slide.bs.carousel', function (ev) {
            var lazy = $(ev.relatedTarget).find("img[data-imgsrc]");
            lazy.attr("src", lazy.data(lazy_src));
            lazy.removeAttr("data-imgsrc");
            lazy.parent().find("img.carousel-item-spinner").remove();
        });
    }
    
    ////////Slick Carousel////////////
    
    var var_autoplay = typeof slick_var_autoplay !== 'undefined' ? slick_var_autoplay : false;
    var var_autoplaySpeed = typeof slick_var_autoplaySpeed !== 'undefined' ? slick_var_autoplaySpeed : 4000;
    
			$('.slick_carousel').slick({
				infinite: true,
				arrows: true,
                autoplay: var_autoplay,
                speed: 2000,
                autoplaySpeed: var_autoplaySpeed,
				nextArrow: '<span class="slick-arrow slick-arrow-right"></span>',
				prevArrow: '<span class="slick-arrow slick-arrow-left"></span>',
				dots: false,
				slidesToShow: 1,
				slidesToScroll: 1,
                responsive: [
								{
									breakpoint: 1199,
									settings: {
										slidesToShow: 1
									}
								}
							]
				});
    
    /////////Search form//////
    
    /// init search box visibility
    update_search_box_visibility();
    
    $(window).resize(function () { 
     waitForFinalEvent(function(){
         update_search_box_visibility();
      }, 500, "update search box visibility on window resize");
    });
    
    $('.search-box-mobile').on('click', 'span.active', function(event){
        var par = $(this).parent();
        $(par).find('.search-box-mobile-expand, .search-box-mobile-close').toggleClass('active');
        $('#search_form').toggleClass('active');
    });
    
    ///////Smooth scrolling
    
    var $root = $('html, body');
    
    $('#content a[href^="#"]').on('click', function(event){
        event.preventDefault();
        var addressValue = this.href.split('#')[1];
        if (addressValue != 'home_carousel' && !$(event.target).is('.my_bookings_table_a_expand')){
          $root.animate({
            scrollTop: $($.attr(this, 'href')).offset().top - 150
          }, 700);
        }
    });
     
   //// Checkout scroll //////
   
    if ( $('#checkout_form_block').length > 0 ){
        
        $root.animate({
            scrollTop: $('#checkout_form_block').offset().top - 50
          }, 700);
        
    }
    
    //// Search results scroll //////
    
    if ($('.babe_search_results').length > 0){
        
        var search_div = $('.babe_search_results').first();
        $root.animate({
            scrollTop: $(search_div).offset().top - 100
          }, 700);
          
    }
     
   //// Confirmation scroll //////
   
    if ( $('#confirmation_message').length > 0 ){
        
        $root.animate({
            scrollTop: $('#confirmation_message').offset().top - 50
          }, 700);
        
    }
    
  /////////////NavBar///////////

    $('.navbar-toggler').keydown(function(e){
        if(e.which == 13){ // enter
            e.preventDefault();
            $(this).click();
            $(this).parents().eq(1).find('.navbar-nav a:first').focus();
        }
    });

    $('.header-menu-row .navbar-nav a').focus( function(e){
        if ( $(window).width() > 991 ) {
            var par = $(this).parent();
            $(par).siblings('li').find('ul li').trigger('mouseout');
            $(par).trigger('mouseover');
        }
    });

    $('.header-menu-row .navbar-nav a.dropdown-toggle').keydown(function(e){
        if( e.which == 9) { // tab
            if ( $(window).width() <= 991 ) { // tab
                e.preventDefault();
                $(this).find('.caret').click();
                $(this).parent().siblings('.dropdown-menu-active').find('.caret').click();
                $(this).parent().find('.dropdown-menu a:first').focus();
            }
        }
    });

    $('.header-menu-row .navbar-nav a').focus( function(e){
        if ( $(window).width() <= 991 ) {
            var par = $(this).parents().eq(1);
            if ($(par).hasClass('navbar-nav')){
                $(par).find('.caret i').removeClass('fa-chevron-up').addClass('fa-chevron-down');
                $(par).find('li.dropdown-menu-active').removeClass('dropdown-menu-active');
            }
        }
    });

    $('.header-menu-row .navbar-nav a:last').keydown(function(e){
        if(e.which == 9){ // tab
            if ( $(window).width() <= 991 ) {
                e.preventDefault();
                $('.header-menu-row .navbar-nav a:first').focus();
            }
        }
    });

    $('.navbar-nav a.mobile-menu-close-link').keydown(function(e){
        if(e.which == 13){ // enter
            e.preventDefault();
            $('.header-top-row').find('.navbar-toggler').click().focus();
        }
    });
  
  $(window).scroll(function (event) {
      var scroll = $(window).scrollTop();
      var body = document.querySelector("body.header_sticky");
      
      if (body){
      
      if (scroll >= 150 ){
         if (!body.classList.contains("header_sticky_scrolled")){
           $('#header').css('opacity', 0);
           $('body').addClass('header_sticky_scrolled');
           setTimeout(function(){
            resizeFunc();
           }, 200);
           $('#header').animate({ 'opacity':1 }, 500);
         }
        
      } else {
         
         if (body.classList.contains("header_sticky_scrolled")){
           $('#header').css('opacity', 0);
           $('body').removeClass('header_sticky_scrolled');
           setTimeout(function(){
            resizeFunc();
           }, 200);
           $('#header').animate({ 'opacity':1 }, 500);
         }         
      }
      
      }
      
  });
  
  const menu_underline = document.querySelector(".menu-underline");
  const menu_links = document.querySelectorAll("#nav_menu > .nav-item > .nav-link");
  
  for (let i = 0; i < menu_links.length; i++) {
    menu_links[i].addEventListener("mouseenter", mouseenterFunc);
  }
  
  function mouseenterFunc() {
    if (!this.parentNode.classList.contains("active")) {
        for (let i = 0; i < menu_links.length; i++) {
            if (menu_links[i].parentNode.classList.contains("active")) {
                menu_links[i].parentNode.classList.remove("active");
             }
        }
        
        this.parentNode.classList.add("active");

        var computedStyle = getComputedStyle(this);
        
        var block_top = parseInt(this.parentNode.offsetTop);
        
        const width = this.getBoundingClientRect().width - parseInt(computedStyle.paddingLeft) - parseInt(computedStyle.paddingRight);

        const left = this.getBoundingClientRect().left + parseInt(computedStyle.paddingLeft);
        const top = block_top + parseInt(computedStyle.paddingTop) + parseInt(computedStyle.lineHeight);
        
        menu_underline.style.width = `${width}px`;

        menu_underline.style.left = `${left}px`;
        menu_underline.style.top = `${top}px`;
        menu_underline.style.transform = "none";
     }
  }

  resizeFunc();
  
  $("#nav_menu").on('mouseleave', function(){
     resizeFunc();      
  });

  window.addEventListener("resize", resizeFunc);
  
  function resizeFunc(){
    
    var active = document.querySelector("#nav_menu > .nav-item.current-menu-item > .nav-link");
    
    if (!active){
        active = document.querySelector("#nav_menu > .nav-item.current-menu-parent > .nav-link");
    }
  
    menu_underline.style.transform = "translateX(-60px)";
    $("#nav_menu > .nav-item.active").removeClass('active');
   
    if (active) {   
        var computedStyle = getComputedStyle(active);

        var block_top = parseInt(active.parentNode.offsetTop);
        
        const width = active.getBoundingClientRect().width - parseInt(computedStyle.paddingLeft) - parseInt(computedStyle.paddingRight);
        const left = active.getBoundingClientRect().left + parseInt(computedStyle.paddingLeft);
        const top = block_top + parseInt(computedStyle.paddingTop) + parseInt(computedStyle.lineHeight);
        
        menu_underline.style.width = `${width}px`;
        menu_underline.style.left = `${left}px`;
        menu_underline.style.top = `${top}px`;
        menu_underline.style.transform = "none";
    
    } else {
        menu_underline.style.transform = "none";
        menu_underline.style.width = `0px`;
        menu_underline.style.left = document.querySelector("#nav_menu").getBoundingClientRect().left;//`0px`;
    }
  }
  ///////////
    
  var resized = false;
  var caret_class = 'fa-chevron-down';
  
  function bindNavbar() {
		if ($(window).width() > 100) {			
			$('.dropdown-toggle').click(function(e) {
                if($(e.target).is('.caret, .caret i') && $(window).width() < 992){
                   var caret = $(this).find('.caret i').first(); 
                   caret.toggleClass(caret_class);
                   caret.toggleClass('fa-chevron-up');
                   $(this).parent().toggleClass('dropdown-menu-active');
                   e.stopPropagation();
                   e.preventDefault(); 
                }
                if($(window).width() >= 992){
                   e.stopPropagation();
                   e.preventDefault();
                }
                if (!$(e.target).is('.caret, .caret i')){
				    window.location = $(this).attr('href');
				}
			});
		}
		else {
			$('.navbar-nav .dropdown').off('mouseover').off('mouseout');
		}
	}
    
	bindNavbar();
    
    $('.dropdown').on('mouseover', function(ev){
        
      var window_width = $(window).width();
        
      if(window_width >= 992){
        
        var parent_dropdown = $(this).parent();
        var subdropdown = $(this).find('.dropdown-menu').first();
        var caret = $(this).find('.caret i').first();
        var caret_class_new = 'fa-chevron-up';
        
        if ($(this).hasClass('dropdown-submenu')){
            
            var block_width = $(this).outerWidth();
            var subblock_width = subdropdown.outerWidth();
            var block_left = $(this).offset().left;
            var css_left = subblock_width+'px';
            caret_class_new = 'fa-chevron-right';
            
            if (block_left + block_width*2 + 20 > window_width){
               css_left = '-'+css_left;
               caret_class_new = 'fa-chevron-left';
            }

            $(this).addClass('dropdown-submenu-expanded');
            subdropdown.css('left', css_left);
            
        } else {

            $(this).addClass('dropdown-expanded');
            subdropdown.css('top', 'calc(100% - 3px)');
            
        }
        
        caret.removeClass(caret_class).addClass(caret_class_new);
        
      }  
    });
    
    $('.dropdown').on('mouseout', function(ev){
     
      if($(window).width() >= 992){

          $(this).removeClass('dropdown-expanded');
          $(this).removeClass('dropdown-submenu-expanded');
        var subdropdown = $(this).find('.dropdown-menu').first();
        var caret = $(this).find('.caret i').first();
        
        if ($(this).hasClass('dropdown-submenu')){
            subdropdown.css('left', '0');
        } else {
            subdropdown.css('top', '100%');
        }
        
        caret.removeClass('fa-chevron-right');
        caret.removeClass('fa-chevron-up');
        caret.removeClass('fa-chevron-left');
        caret.addClass(caret_class);
      }  
    });
    ///////////////// end navbar/////////////
    
});

//////////Update search box visibility//////////////////

var update_search_box_visibility = function() {
    
    if ($('.search-box-mobile').length > 0){
        if ($(window).width() < 768) {
           $('.search-box-mobile > span').removeClass('active'); 
           $('.search-box-mobile .search-box-mobile-expand').addClass('active');
           $('#search_form').removeClass('active');
        } else {
           $('.search-box-mobile > span').removeClass('active');
           $('#search_form').addClass('active'); 
        }
    }
};

/////////////////////////

var waitForFinalEvent = (function () {
  var timers = {};
  return function (callback, ms, uniqueId) {
    if (!uniqueId) {
      uniqueId = "Don't call this twice without a uniqueId";
    }
    if (timers[uniqueId]) {
      clearTimeout (timers[uniqueId]);
    }
    timers[uniqueId] = setTimeout(callback, ms);
  };
})();

////////////////////////

})(jQuery);
