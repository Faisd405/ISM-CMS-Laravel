// Custom Label Button
$(".label-btn").each(function(){
    const text = $(this).html();
    $(this).empty();
    $(this).append("<span></span><span></span>");
    $(this).find("span").html(text);
});

// Split Text
$(".split-text").each(function(){
    var text = $(this).html().split(" ");
    $(this).empty().html(function() {
        for (i = 0; i < text.length; i++) {
            if (i == 0) {
                jQuery(this).append('<span class="overflow-hidden d-inline-flex split-wrap"><span class="split-word d-inline-flex">' + text[i] + '</span></span>');
            } else {
                jQuery(this).append(' <span class="overflow-hidden d-inline-flex split-wrap"><span class="split-word d-inline-flex">' + text[i] + '</span></span>');
            }
        }
    });
    var splitWord = $(".split-word")
    var split = $(this).find(splitWord);
    var delay = 0;
    split.each(function(){
        $(this).css("transition-delay", delay+"s");
        delay = delay + 0.0365;
    });
});

// Link Delay
$("a[href]:not([href='#!'],[href='javascript:;'],[data-bs-toggle],.nav-dot a,.fp-slidesNav a,[target='_blank'])").click(function(e){
    e.preventDefault();
    linkDelay();
    var url = $(this).attr("href");
    function linkDelay(){
        setTimeout(function() {
            window.location.href = url;
        },800);
    }
    $("body").addClass("is-active").removeClass("is-load");
});
window.addEventListener( "pageshow", function ( event ) {
    var historyTraversal = event.persisted || ( typeof window.performance != "undefined" && window.performance.navigation.type === 2 );
    if ( historyTraversal ) {
        // Handle page restore.
        window.location.reload();
    }
});

// Tooltip Bootstrap
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl)
});

// Post Entry Image Wrapper
$(".post-entry").each(function(){
    $(this).find("img").wrap("<figure class='figure-img'></figure>")
});

// Custom Dropdown
$(".dropdown-menu-content").css({height:0});
$(".dropdown").each(function(){
    var ddown = $(this).find(".dropdown-menu-content");
    var ddownUList = $(this).find(".dropdown-content");
    var ddownHeight = ddownUList.outerHeight();
    $(this).on('show.bs.dropdown', function(){
        ddown.css({height:""+ddownHeight+"px"})
    });
    $(this).on('hide.bs.dropdown', function(){
        ddown.css({height:0})
    });
});

// Custom Dropdown Scroll
$(".sub-dropdown-menu").each(function(){
    var dropHeight = $(this).outerHeight();
    var iconArrow = '<i class="icon fa-light fa-angle-down d-flex align-items-center justify-content-center"></i>'
    if(dropHeight >= 297){
        $(this).append(iconArrow);
    }
    $(this).find("ul").bind("scroll",function(){
        var scrollDrop = $(this).scrollTop();
        if(scrollDrop > 1) {
            $(this).parent().find(".icon").addClass("is-hide");
        } else {
            $(this).parent().find(".icon").removeClass("is-hide");
        }
    });
});

// Custom input focus
$(".form-control").each(function(){
    var inputForm = $(this);
    inputForm.on("blur",function(){
        inputForm.parent().removeClass("is-focused");
        if(inputForm.is(":invalid")) {
            inputForm.parent().addClass("is-invalid").removeClass("is-valid");
        } else {
            inputForm.parent().removeClass("is-invalid")
        }
    }).on("focus",function(){
        inputForm.parent().addClass("is-focused");
    }).on("change",function(){
        var inputVal = $(this).val().length;
        console.log(inputVal)
        if(inputVal > 0){
            inputForm.parent().addClass("is-valid");
        } else {
            inputForm.parent().removeClass("is-valid");
        }
    });
});

// Sticky Header
$(window).bind("scroll",function(){
    var scrollY = $(this).scrollTop();
    if (scrollY >= 500) {
        $(".section-header").addClass("is-sticky");
	} else {
        $(".section-header").removeClass("is-sticky");
    }
});

// On Load
$(window).on("load",function(){
    $("body").addClass("is-load");
    setTimeout(function(){
        $("body").addClass("is-ready");
    },300);
});

// Custom Post Animation
$(".post-entry").each(function(){
    $(this).find("h1,h2,h3,h4,h5,li,p").attr("data-aos","").addClass("aos-init anim-scroll-up")
});

gsap.registerPlugin(ScrollTrigger);

// Header Changer 
const scrollColorElems = document.querySelectorAll("[data-nav-color]");
scrollColorElems.forEach((colorSection, i) => {
    const dataClass = colorSection.dataset.navColor;
    ScrollTrigger.create({
        trigger: colorSection,
        scroller: "body",
        start: "top 5%",
        end: "bottom 5%",
        toggleClass: {
            targets: ".section-header",
            className: dataClass
        },
    });
});

//scrolling nav
$(".nav-dot a").bind("click", function(event) {
    var $anchor = $(this);
    if ($(window).width() < 992) {
        $("html, body").stop().animate({
            scrollTop: $($anchor.attr("href")).offset().top - 0
        }, 1000, "easeInOutExpo");
    } else {
        $("html, body").stop().animate({
            scrollTop: $($anchor.attr("href")).offset().top - 0
        }, 1000, "easeInOutExpo");	
    }
    event.preventDefault();
});

//burger menu
$(".burger-menu").on("click",function(){
    $("body").toggleClass("is-menu-active");
});

$(window).bind("resize load",function(){
    if($(window).width() < 991){
        $(".collapse-menu [data-bs-target]").attr("data-bs-toggle","collapse");
        $(".collapse-menu").find(".collapse").removeClass("show");
    } else {
        $(".collapse-menu [data-bs-target]").removeAttr("data-bs-toggle");
        $(".collapse-menu").find(".collapse").addClass("show");
    }
});

//scrolltrigger refresh init
var careerAccordion = document.getElementById('career-accordion')
careerAccordion.addEventListener('show.bs.collapse', function () {
    ScrollTrigger.refresh(true);
});