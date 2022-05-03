jQuery(document).ready(function($){
	//Fixed-header
	if ($(window).width() > 1198.98) {

		$(window).bind("scroll resize",function() {    
			var scroll = $(window).scrollTop();
			
			if (scroll >= 300) {
				$(".main-header").addClass("pinned").css("transform","translateY(0)");
			} else if(scroll >= 150) {
				$(".main-header").css("transform","translateY(-100%)");
			} else {
				$(".main-header").removeClass("pinned").css("transform","translateY(0)");
			}
		});
	}


    // MAIN-NAV
	if ($(window).width() > 1198.98) {
		$('.has-dropdown, .has-sub-dropdown').mouseenter(function(){ 
			var $this = $(this); 
	
				$this.addClass('is-opened')
	
		}).mouseleave(function(){
	
			var $this = $(this);
	
				$this.removeClass('is-opened')
	
		});   
	}
    
   
	$(".list-mv > .has-dropdown > a, .list-mv > .has-dropdown > .dropdown > .btn-back").click(function () { 
		if ($(".has-dropdown").hasClass('is-opened')) {

			$(".has-dropdown.is-opened").removeClass("is-opened");

		} else {

			$(".has-dropdown.is-opened").removeClass("is-opened");

			if ($(this)) {

				$(this).parent().addClass("is-opened");
			}
		}
	});

	$(".has-sub-dropdown > a, .has-sub-dropdown > .sub-dropdown > .btn-back > a").click(function () { 
		if ($(".has-sub-dropdown").hasClass('is-opened')) {

			$(".has-sub-dropdown.is-opened").removeClass("is-opened");

		} else {

			$(".has-sub-dropdown.is-opened").removeClass("is-opened");

			if ($(this)) {

				$(this).parent().addClass("is-opened");
			}
		}
	}); 

	$('.has-dropdown > a, .has-dropdown > .dropdown > .btn-back > a').click(function() {
		$(".has-dropdown").parent().toggleClass('moves-out');
	});

	$('.has-sub-dropdown > a, .has-sub-dropdown > .sub-dropdown > .btn-back > a').click(function() {
		$(".has-sub-dropdown").parent().toggleClass('moves-out');
	});



	//CT-TOGGLE
    $('.ct-toggle').click(function() {
        ctToggle();
    });

    function ctToggle() {
		$('.th-right').toggleClass('is-opened');
	}
    
    //BURGER-MENU
    $('.nav-toggle').click(function() {
        navigationToggle();
    });

    function navigationToggle() {
		$('body').toggleClass('scroll-lock');
        $('.mh-center').toggleClass('is-opened');
		$(".has-dropdown.is-opened").removeClass("is-opened");
        $('.list-mv').removeClass('moves-out');
        $('.nav-toggle').toggleClass('is-actived');
    }
	


	//ACCOUNT-NAV
	$(".account-box i, .account-box .account-name").click(function() {
		accountToggle();
	});

	function accountToggle() {
		$('.dropdown-account').toggleClass('is-opened');
	}


	$(document).mouseup(function (e) {
		var var_burger = $("div[class='nav-btn']");//ubah ke class button nya
		if (!var_burger.is(e.target) && $('.mh-center').hasClass('is-opened')&& ( !$(".main-nav, .main-nav *, .switcher, .switcher *").is(e.target)) ) {
			navigationToggle();
		}
		
		// body...
		var var_search = $("div[class='btn-search']");//ubah ke class button nya
		if (!var_search.is(e.target) && $('.menubar-center').hasClass('open-search')&& ( !$(".btn-search, .box-search, .box-search *").is(e.target)) ) {
			searchToggle();
		}
		
		var var_account = $(".ct-toggle");//ubah ke class button nya
		if (!var_account.is(e.target) && $('.th-right').hasClass('is-opened')&& ( !$(".th-ctbox, .th-ctbox *").is(e.target)) ) {
			ctToggle();
		}
		


	});
	
	
	
	//PARALAX BG
	$(window).scroll(function() {
		var pixs = $(window).scrollTop(),
			scale = (pixs / 16000) + 1,
			opacity = 1 - pixs / 750;
		$(".banner-breadcrumb > .thumb-img").css({
			"transform": "translate3d(0, "+pixs/4+"px, 0)",
			"opacity": opacity
		});
	});

	//PROPOSAL
    $('.proposal, .btn-close').click(function() {
        proposalToggle();
    });

    function proposalToggle() {
        $('.form-overlays').toggleClass('is-actived');
		$('body').toggleClass('overlays-actived')
    }
	
	

    
});



