$(window).on("load",function(){
// Banner Slide
$(".banner-slide").each(function(){
    var $this = $(this);
    setTimeout(function(){
        $this.find(".swiper-slide:first-child").addClass("is-active-slide");
    },800)
    var bannerSlide = new Swiper($(this), {
        effect: "fade",
        speed: 800,
        simulateTouch: false,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        // pagination: {
        //     el: ".swiper-counter",
        //     clickable: true,
        //     type: "fraction",
        //     formatFractionCurrent: function (number) {
        //         return ('0' + number).slice(-2);
        //     },
        //     formatFractionTotal: function (number) {
        //         return ('0' + number).slice(-2);
        //     },
        //     renderFraction: function (currentClass, totalClass) {
        //         return '<span class="' + currentClass + '"></span>' +
        //             '' +
        //             '<span class="' + totalClass + '"></span>';
        //     }
        // }
    });
    bannerSlide.on("slideChange",function(){
        $this.find(".swiper-slide-active").removeClass("is-active-slide");
    });
    bannerSlide.on("slideChangeTransitionEnd",function(){
        $this.find(".swiper-slide-active").addClass("is-active-slide");
    });
});

// // Product Slide
// $(".product-slide-2").each(function(){
//     var $this = $(this);
//     var productSlides = new Swiper($this,{
//         slidesPerView: 4,
//         loop: true,
//         centeredSlides: true,
//         speed: 4000,
//         simulateTouch: false,
//         autoplay: {
//             delay: 0,
//             pauseOnMouseEnter: true,
//             disableOnInteraction: false
//         },
//         breakpoints:{
//             1367:{
//                 slidesPerView: 5,
//             }
//         }
//     });
//     $this.on("mouseenter",function(){
//         productSlides.autoplay.stop();
//         productSlides.params.speed = 0;
//     });
//     $this.on("mouseleave",function(){
//         productSlides.autoplay.start();
//         productSlides.params.speed = 4000;
//     });
// });

// Career Slide
$(".career-slide").each(function(){
    var $this = $(this);
    var careerSlide = new Swiper($this,{
        slidesPerView: 3,
        centeredSlides: true,
        loop: true,
        speed: 800,
        spaceBetween: 32
    });
});

});