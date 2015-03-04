(function($) {
  'use strict';
  var popupClassBase = '.js-popup',
      popupClassImage = popupClassBase + '--img',
      popupClassGallery = popupClassBase + '--gallery';


  // Add it after jquery.magnific-popup.js and before first initialization code
  $.extend(true, $.magnificPopup.defaults, {
    tClose: 'Lukk (Esc)', // Alt text on close button
    tLoading: 'Laster...', // Text that is displayed during loading. Can contain %curr% and %total% keys
    gallery: {
      tPrev: 'Forrige (Venstre pil)', // title for left button
      tNext: 'Neste (Høyre pil)', // title for right button
      tCounter: '<span>%curr% av %total%</span>' // markup of counter
    },
    image: {
      //titleSrc: 'data-title',
      titleSrc: function(item) {
        var str = item.el.attr('data-title') !== undefined ? '<h3 class="mfp-title__header">' + item.el.attr('data-title') + '</h3>' : '';
        if( item.el.attr('data-caption') !== undefined) {
          str += '<p class="mfp-title__caption">' + item.el.attr('data-caption') + '</p>';
        }
        if( item.el.attr('data-credit') !== undefined) {
          str += '<p class="mfp-title__credit">Foto: ' + item.el.attr('data-credit') + '</p>';
        }

        return str;
      },
      tError: '<a href="%url%">Bildet</a> kunne ikke lastes.' // Error message when image could not be loaded
    },
    ajax: {
      tError: '<a href="%url%">Innholdet</a> kunne ikke lastes.' // Error message when ajax request failed
    }
  });

  $(popupClassImage).magnificPopup({
    type: 'image'
  });

  $(popupClassGallery).magnificPopup({
    type: 'image',
    gallery: {
      enabled: true,
    }
  });

})(jQuery);