/* Requires jQuery */

$(window).load(function() {
  $('.slider').flexslider({
    namespace: "slider__",
    selector: '.slider__slides > .slider__slide',
    animation: 'slide',
    slideshow: false,

    // Primary Controls
    prevText: 'Forrige',
    nextText: 'Neste'
  });
});