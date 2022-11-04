$(window).on("load",function(){
    
// var productSlide = $(".product-slide").slick({
//     infinite: true,
//     slidesToShow: 5,
//     slidesToScroll: 0.1,
//     arrows: false,
//     dots: false,
//     autoplay: true,
//     speed: 300,
//     autoplaySpeed: 0,
//     cssEase: "linear",
//     pauseOnHover: true,
// });
// productSlide.on("mouseleave",function(){
//     productSlide.slick("slickPlay")
// });

var slideIntro = $(".slide-intro").slick({
    slidesToShow: 1,
    infinite: true,
    arrows: false,
    fade: true,
    speed: 800,
    autoplay: true,
    autoplaySpeed: 6000,
    pauseOnHover: false,
    focusOnSelect: false,
    prevArrow: '<button class="btn icon-btn btn-sm slick-arrow slick-prev"><i class="fa-light fa-arrow-left"></i></button>',
    nextArrow: '<button class="btn icon-btn btn-sm slick-arrow slick-next"><i class="fa-light fa-arrow-right"></i></button>',
    cssEase: "cubic-bezier(.19,.38,.05,1)",
});
$(".slide-intro .slick-current").addClass("is-active");
$(".slide-intro").on("afterChange", function(event, slick, currentSlide, nextSlide){
    setTimeout(function(){
        $(".slide-intro .slick-current").addClass("is-active");
    },300);
});
$(".slide-intro").on("beforeChange", function(event, slick, currentSlide, nextSlide){
    $(".slide-intro .slick-current").removeClass("is-active");
});

var slideBg = $(".slide-bg").slick({
    slidesToShow: 1,
    slidesToScroll: 1,
    arrows: false,
    fade: true,
    speed: 800,
    cssEase: "cubic-bezier(.19,.38,.05,1)",
    asNavFor: '.slide-thumb'
});

var slideThumb = $(".slide-thumb").slick({
    infinite: true,
    slidesToShow: 5,
    focusOnSelect: true,
    arrows: false,
    dots: false,
    autoplay: false,
    speed: 800,
    centerMode: true,
    cssEase: "cubic-bezier(.19,.38,.05,1)",
    asNavFor: '.slide-bg',
    responsive: [
        {
          breakpoint: 1024,
          settings: {
            slidesToShow: 3,
            centerMode: true,
          }
        },
    ]
});

$(".slide-bg .slick-current").addClass("is-active is-hide");
$(".slide-bg").on("afterChange", function(event, slick, currentSlide, nextSlide){
    setTimeout(function(){
        $(".slide-bg .slick-current").addClass("is-active");
    },400);
});
$(".slide-bg").on("beforeChange", function(event, slick, currentSlide, nextSlide){
    $(".slide-bg .slick-current").removeClass("is-active");
});

$(".slide-thumb").on("afterChange", function(event, slick, currentSlide, nextSlide){
    $(".slide-thumb .slick-current").addClass("is-active");
    rellax.refresh(); 
});
$(".slide-thumb").on("beforeChange", function(event, slick, currentSlide, nextSlide){
    $(".slide-thumb .slick-current").removeClass("is-active");
});
$(".back-intro").on("click",function(){
    setTimeout(function(){
        $(".banner-intro").removeClass("is-hide");
        slideIntro.slick('slickSetOption', {
            'autoplay': true
        }, true);
    },800);
    $(".back-intro").addClass("is-hide");
    $(".slide-bg .slide-item").addClass("is-hide");
    $(".slide-thumb .slick-current").removeClass("is-active");
});
$(".slide-thumb .product-item").on("click",function(){
    $(".banner-intro").addClass("is-hide");
    $(".back-intro").removeClass("is-hide");
    if($(".banner-intro").hasClass("is-hide")){
        $(".slide-bg .slide-item").removeClass("is-hide");
        slideIntro.slick('slickSetOption', {
            'autoplay': false
        }, true);
    } else {
        $(".slide-bg .slide-item").addClass("is-hide");
    }
});


});