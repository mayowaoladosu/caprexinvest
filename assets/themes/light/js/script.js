(function($) {

	"use strict";

	// flatpickr date and time
	$(".flatpickr").flatpickr({
		altInput: true,
		altFormat: "d/m/y",
		dateFormat: "Y-m-d",
		mode: "multiple" ,
		// minDate: "today" ,
		// maxDate: "2024-08-15"
	});
	// flatpickr date and time

	//Search Popup
	if($('#search-popup').length){
		//Show Popup
		$('.search-btn').on('click', function() {
			$('#search-popup').addClass('popup-visible');
		});
		$(document).keydown(function(e){
	        if(e.keyCode === 27) {
	            $('#search-popup').removeClass('popup-visible');
	        }
	    });
		//Hide Popup
		$('.close-search,.search-popup .overlay-layer').on('click', function() {
			$('#search-popup').removeClass('popup-visible');
		});
	}
	//Search Popup

	// nice select
	$(document).ready(function() {
		$('.nice-select').niceSelect();
	});
	// nice select

	// cmn select2 start
	$(document).ready(function () {
		$('.cmn-select2').select2({
			minimumResultsForSearch: Infinity // Disable search input
		});
	});
	// cmn select2 end






	//CART OFFCANVAS
	if($('#cart-offcanvas').length){
		//Show Popup
		$('.cart-toggler').on('click', function() {
			$('#cart-offcanvas').addClass('popup-visible');
		});
		$(document).keydown(function(e){
	        if(e.keyCode === 27) {
	            $('#cart-offcanvas').removeClass('popup-visible');
	        }
	    });
		//Hide Popup
		$('.close-search,.cart-offcanvas .overlay-layer').on('click', function() {
			$('#cart-offcanvas').removeClass('popup-visible');
		});
	}
	// CART OFFCANVAS


	// ISOTOP STARTS
	$(document).ready(function() {
		var $grid = $('.listing-row').isotope({
			itemSelector: '.grid-item',
			percentPosition: true,
			masonry: {
				// use outer width of grid-sizer for columnWidth
				columnWidth: 1
			}
		});

		// Filter items on button click
		$('.filter-button-group').on('click', 'button', function() {
			var filterValue = $(this).attr('data-filter');
			$grid.isotope({ filter: filterValue });
		});

		// Set default filter to ".cleaning" on page load
		var defaultFilter = '.cleaning';
		$grid.isotope({ filter: defaultFilter });

		$('.filter-button-group button').removeClass('active');
		$('.filter-button-group button[data-filter=".cleaning"]').addClass('active');

		// Active class toggle on button click
		$('.filter-button-group button').on('click', function(event) {
			$(this).siblings('.active').removeClass('active');
			$(this).addClass('active');
			event.preventDefault();
		});
	});
	// ISOTOP ENDS





	//--- LOAD MORE STARTS ---//
	$('.item-list').slice(0,3).show();

	$('.load-more').click(function(){
		$('.item-list:hidden').slice(0,1).slideDown(300);

		// hide btn after fully loaded
		if($('.item-list:hidden').length==0){
			$(this).fadeOut(300);
		}
	});
	//--- LOAD MORE ENDS ---//


	// BAR FILLAR
	if ($(".progress-bar").length){
		const skillsSection = document.getElementsByClassName("skills-section")[0];
		const progressBars = document.querySelectorAll('.progress-bar');

		function showProgress(){
			progressBars.forEach(progressBar => {
				const value = progressBar.dataset.progress;
				progressBar.style.opacity = 1;
				progressBar.style.width = `${value}%`;
			});
		}

		function hideProgress(){
			progressBars.forEach(p => {
				p.style.opacity = 0;
				p.style.width = 0;
			});
		}

		window.addEventListener('scroll', () => {
			const sectionPos = skillsSection.getBoundingClientRect().top;
			const screenPos = window.innerHeight;
			if (sectionPos < screenPos){
				showProgress();
			} else {
				hideProgress();
			}
		});
	}
	// BAR FILLAR

	// Social share start
	$("#shareBlock").socialSharingPlugin({
		urlShare: window.location.href,
		description: $("meta[name=description]").attr("content"),
		title: $("title").text(),
	});
	// Social share end


	// swiper thumb
	var swiper = new Swiper(".projectSwiper", {
		spaceBetween: 10,
		slidesPerView: 4,
		freeMode: true,
		watchSlidesProgress: true,
	});
	var swiper2 = new Swiper(".projectSwiper2", {
		spaceBetween: 10,
		navigation: {
		  nextEl: ".swiper-button-next",
		  prevEl: ".swiper-button-prev",
		},
		thumbs: {
		  swiper: swiper,
		},
	});
	// swiper thumb

	// input check value change
	$(document).ready(function() {
        $('.checkmark').on('click', function() {
            var color = $(this).siblings('input').data('color');
            $('.change-value').text(color);
            $('.checkbox').prop('checked', false);
            $(this).siblings('input').prop('checked', true);
        });
    });
	// input check value change

	// odommeter
	if ($(".odometer").length) {
		var odo = $(".odometer");
		odo.each(function () {
		  $(this).appear(function () {
			var countNumber = $(this).attr("data-count");
			$(this).html(countNumber);
		  });

		});
	}
	// odommeter

	// magnifipopup video
	$(document).ready(function() {
		$('.hv-popup-link').magnificPopup({
			disableOn: 700,
			type: 'iframe',
			mainClass: 'mfp-fade',
			removalDelay: 160,
			preloader: false,

			fixedContentPos: false
		});
	});
	// magnifipopup video

	// input field show hide password start
	if ($('.password-box').length) {
		const passwordBoxes = document.querySelectorAll('.password-box');

		passwordBoxes.forEach(box => {
			const password = box.querySelector('.password');
			const passwordIcon = box.querySelector('.password-icon');

			passwordIcon.addEventListener("click", function () {
				if (password.type == 'password') {
					password.type = 'text';
					passwordIcon.classList.add('fa-eye-slash');
				} else {
					password.type = 'password';
					passwordIcon.classList.remove('fa-eye-slash');
				}
			});
		});
	}
	// input field show hide password end

	//Hide Loading Box (Preloader)
	function handlePreloader() {
		if ($('.loader-wrap').length) {
			$('.loader-wrap').delay(1500).fadeOut(500);
		}
	}

	$(document).ready(function() {
		handlePreloader();

		if ($(".preloader-close").length) {
			$(".preloader-close").on("click", function() {
				$('.loader-wrap').stop(true).fadeOut(500);
			});
		}
	});
	//Hide Loading Box (Preloader)

	// Menu Style Start
    function dynamicCurrentMenuClass(selector) {
        let FileName = window.location.href.split('/').reverse()[0];

        selector.find('li').each(function () {
            let anchor = $(this).find('a');
            if ($(anchor).attr('href') == FileName) {
                $(this).addClass('current');
            }
        });
        // if any li has .current elmnt add class
        selector.children('li').each(function () {
            if ($(this).find('.current').length) {
                $(this).addClass('current');
            }
        });
        // if no file name return
        if ('' == FileName) {
            selector.find('li').eq(0).addClass('current');
        }
    }
	// Menu Style End

    // dynamic current class
    let mainNavUL = $('.main-menu').find('.navigation');
    dynamicCurrentMenuClass(mainNavUL);

	//Sticky Header Style and Scroll to Top
	function headerStyle() {
		if($('.main-header').length){
			var windowpos = $(window).scrollTop();
			var siteHeader = $('.main-header');
			var scrollLink = $('.scroll-to-top');
			var sticky_header = $('.main-header .sticky-header');
			if (windowpos > 100) {
				siteHeader.addClass('fixed-header');
				sticky_header.addClass("animated slideInDown");
				scrollLink.fadeIn(300);
			} else {
				siteHeader.removeClass('fixed-header');
				sticky_header.removeClass("animated slideInDown");
				scrollLink.fadeOut(300);
			}
		}
	}
	headerStyle();

	//  When sticky header is Scrollig
	$(window).on('scroll', function() {
		headerStyle();
	});
	//Sticky Header Style and Scroll to Top

	//Submenu Dropdown Toggle
	if($('.main-header li.dropdown ul').length){
		$('.main-header .navigation li.dropdown').append('<div class="dropdown-btn"><span class="fa fa-angle-right"></span></div>');
	}

	//Mobile Nav Hide Show
	if($('.mobile-menu').length){
		var mobileMenuContent = $('.main-header .nav-outer .main-menu').html();
		$('.mobile-menu .menu-box .menu-outer').append(mobileMenuContent);
		$('.sticky-header .main-menu').append(mobileMenuContent);
		//Dropdown Button
		$('.mobile-menu li.dropdown .dropdown-btn').on('click', function() {
			$(this).toggleClass('open');
			$(this).prev('ul').slideToggle(500);
			$(this).prev('.megamenu').slideToggle(500);
		});
		//Menu Toggle Btn
		$('.mobile-nav-toggler').on('click', function() {
			$('body').addClass('mobile-menu-visible');
		});
		//Menu Toggle Btn
		$('.mobile-menu .menu-backdrop,.mobile-menu .close-btn,.scroll-nav li a').on('click', function() {
			$('body').removeClass('mobile-menu-visible');
		});
	}

	// banner slide
	function bannerSlider() {
		// banner slide 01
		if ($(".banner-slider-1").length > 0) {
		    // Banner Slider
			var bannerSlider1 = new Swiper('.banner-slider-1', {
				preloadImages: false,
                loop: true,
                centeredSlides: false,
                resistance: true,
                resistanceRatio: 0.6,
                speed: 2400,
                spaceBetween: 0,
                parallax: false,
                effect: "fade",
				autoplay: {
				    delay: 8000,
                    disableOnInteraction: false
				},
				pagination: {
				el: '.slider__pagination',
				clickable: true,
			  	},
	            navigation: {
	                nextEl: '.banner-slider-button-next',
	                prevEl: '.banner-slider-button-prev',
	            },
			});
		}
	}
	bannerSlider();
	// banner slide

	// Single item Carousel
	if ($('.single-item-carousel').length) {
		var singleItemCarousel = new Swiper('.single-item-carousel', {
			preloadImages: false,
			loop: true,
			centeredSlides: false,
			resistance: true,
			resistanceRatio: 0.6,
			speed: 1400,
			spaceBetween: 10,
			parallax: false,
			effect: "slide",
			pagination: {
				el: '.slider__pagination',
				clickable: true,
			  },
			autoplay: {
				delay: 8000,
				disableOnInteraction: false
			},
			navigation: {
				nextEl: '.swiper-button-next',
				prevEl: '.swiper-button-prev',
			},
		});
	}
	// Single item Carousel

	// two item carousel
	if ($('.two-item-carousel').length) {
		var twoItemCarousel = new Swiper('.two-item-carousel', {
			preloadImages: false,
			loop: true,
			centeredSlides: false,
			resistance: true,
			resistanceRatio: 0.6,
			slidesPerView: 2,
			speed: 1400,
			spaceBetween: 30,
			parallax: false,
			effect: "slide",
			active: 'active',
			autoplay: {
				delay: 5000,
				disableOnInteraction: false
			},
			pagination: {
				el: '.slider__pagination',
				clickable: true,
			},
			breakpoints: {
				1400: {slidesPerView: 2},
                991: {slidesPerView: 2},
                640: { slidesPerView: 1},
            }
		});
	}
	// two item carousel

	// three item carousel
	if ($('.three-item-carousel').length) {
		var twoItemCarousel = new Swiper('.three-item-carousel', {
			preloadImages: false,
			loop: true,
			centeredSlides: false,
			resistance: true,
			resistanceRatio: 0.6,
			slidesPerView: 3,
			speed: 1400,
			spaceBetween: 30,
			parallax: false,
			effect: "slide",
			active: 'active',
			autoplay: {
				delay: 5000,
				disableOnInteraction: false
			},
			  pagination: {
				el: '.slider__pagination2',
				clickable: true,
			},
			navigation: {
				nextEl: '.slider-button-next4',
				prevEl: '.slider-button-prev4',
			},
			breakpoints: {
				1400: { slidesPerView: 3 },
				991: { slidesPerView: 2 },
				640: { slidesPerView: 1 }
			}
		});
	}
	// three item carousel

	// four item carousel
	if ($('.four-item-carousel').length) {
		var twoItemCarousel = new Swiper('.four-item-carousel', {
			preloadImages: false,
			loop: true,
			centeredSlides: false,
			resistance: true,
			resistanceRatio: 0.6,
			slidesPerView: 4,
			speed: 1400,
			spaceBetween: 30,
			parallax: false,
			effect: "slide",
			active: 'active',
			autoplay: {
				delay: 5000,
				disableOnInteraction: false
			},
			navigation: {
				nextEl: '.swiper-button-next',
				prevEl: '.swiper-button-prev',
			},
			breakpoints: {
				1400: { slidesPerView: 3},
                991: { slidesPerView: 2},
                640: { slidesPerView: 1},
            }
		});
	}
	// four item carousel

	// five item carousel
	if ($('.five-item-carousel').length) {
		var twoItemCarousel = new Swiper('.five-item-carousel', {
			preloadImages: false,
			loop: true,
			grabCursor: true,
			centeredSlides: false,
			resistance: true,
			resistanceRatio: 0.6,
			slidesPerView: 5,
			speed: 1400,
			spaceBetween: 30,
			parallax: false,
			effect: "slide",
			active: 'active',
			autoplay: {
				delay: 5000,
				disableOnInteraction: false
			},
			navigation: {
				nextEl: '.slider-button-next4',
				prevEl: '.slider-button-prev4',
			},
			breakpoints: {
				1400: {
					slidesPerView: 4,
				},
                991: {
                  slidesPerView: 3,
                },
                640: {
                  slidesPerView: 1,
                },
            }
		});
	}
	// five item carousel


	// owl theme
	if ($('.theme_carousel').length) {
		$(".theme_carousel").each(function (index) {
			var $owlAttr = {},
			$extraAttr = $(this).data("options");
			$.extend($owlAttr, $extraAttr);
			$(this).owlCarousel($owlAttr);
		});
	}
	// owl theme


	// progress (scroll to top) start
	$('.scroll-top-inner').on("click", function () {
		$('html, body').animate({scrollTop: 0}, 500);
		return false;
	});

	function handleScrollbar() {
        const bHeight = $('body').height();
        const scrolled = $(window).innerHeight() + $(window).scrollTop();

        let percentage = ((scrolled / bHeight) * 100);

        $('.scroll-top-inner .bar-inner').css( 'width', percentage + '%');
    }

	$(window).on('scroll', function() {
		handleScrollbar();
		if ($(window).scrollTop() > 200) {
			$('.scroll-top-inner').addClass('visible');
		} else {
			$('.scroll-top-inner').removeClass('visible');
		};
	});
	// progress (scroll to top) end

	// Elements Animation
	if($('.wow').length){
		var wow = new WOW(
		  {
			boxClass:     'wow',
			animateClass: 'animated',
			offset:       0,
			mobile:       true,
			live:         true
		  }
		);
		wow.init();
	}
	// Elements Animation

	// curved-circle
	$(window).scroll(function() {
		var theta = $(window).scrollTop() / 15;
		$(".round-box-content .curved-circle").css({ transform: "rotate(" + theta + "deg)" });
	});
	// curved-circle

	$(window).on('load', function() {

		//Jquery Curved Circle
		if ($('.curved-circle').length) {
			$('.curved-circle').circleType({
			  position: 'absolute',
			  dir: 1,
			  radius: 70,
			  forceHeight: true,
			  forceWidth: true
			});
		}
		if ($('.curved-circle-2').length) {
			$('.curved-circle-2').circleType({
			  position: 'absolute',
			  dir: 1,
			  radius: 170,
			  forceHeight: true,
			  forceWidth: true
			});
		}
	});


})(window.jQuery);


$(document).ready(function() {
    // Open the sidebar when clicking the button
    $('.shope-canvas-btn').on('click', function() {
        $('.shop-sidebar').css('left', '0');
    });

    // Close the sidebar when clicking the close button
    $('.shop-filter-close').on('click', function() {
        $('.shop-sidebar').css('left', '-160%');
    });
});
