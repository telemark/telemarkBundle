svgeezy.init('nocheck', 'png');

$('.js-font-adjust').hover(function(){
  $('.js-font-adjust-message').addClass('font-adjust__message--active');
}, function(){
  $('.js-font-adjust-message').removeClass('font-adjust__message--active');
});

(function($) {
  $('.js-sl').each(function(){
    var sl = $(this);
    sl.find('.js-sl-facebook').attr('href', 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURI(window.location.href) + '&lang=nb_NO');
    sl.find('.js-sl-twitter').attr('href', 'https://twitter.com/intent/tweet?via=telemarkfylke&lang=no&text=' + encodeURIComponent( $('h1').eq(0).text() ) + '&url=' + encodeURI(window.location.href));
    sl.find('.js-sl-googlepluss').attr('href', 'https://plus.google.com/share?url=' + encodeURI(window.location.href));
  });

  $('.js-sl-facebook, .js-sl-twitter, .js-sl-googlepluss').click(function(event){
    var link = $(this);
    event.preventDefault();
    window.open( link.attr('href'), link.text(), "menubar=0, resizable=0, width=500, height=500" );
  });
})(jQuery);


/*!
 * grunticon Stylesheet Loader | https://github.com/filamentgroup/grunticon | (c) 2012 Scott Jehl, Filament Group, Inc. | MIT license.
 */
window.grunticon=function(e){if(e&&3===e.length){var t=window,n=!(!t.document.createElementNS||!t.document.createElementNS("http://www.w3.org/2000/svg","svg").createSVGRect||!document.implementation.hasFeature("http://www.w3.org/TR/SVG11/feature#Image","1.1")||window.opera&&-1===navigator.userAgent.indexOf("Chrome")),o=function(o){var r=t.document.createElement("link"),a=t.document.getElementsByTagName("script")[0];r.rel="stylesheet",r.href=e[o&&n?0:o?1:2],a.parentNode.insertBefore(r,a)},r=new t.Image;r.onerror=function(){o(!1)},r.onload=function(){o(1===r.width&&1===r.height)},r.src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw=="}};


( function( grunticon ) {
  var gruntIconPath = '/bundles/tfktelemark/img/icons/';
  grunticon([ gruntIconPath + "icons.data.svg.css", gruntIconPath + "icons.data.png.css", gruntIconPath + "icons.fallback.css"]);
})( grunticon );