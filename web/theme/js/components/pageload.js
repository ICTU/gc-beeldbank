/**
 *
 * @type {jQuery.fn.init|jQuery|HTMLElement}
 * Functionality for showing correct section whenever it's in the URL
 */


function getCurrentSection() {
  const currentUrl = window.location.href.split("/");
  const currentSection = currentUrl[currentUrl.length - 1];
  const targetLink = $('.main-menu__link[href="' + currentSection + '"]');


  if (targetLink.length && $(currentSection).length) {

    if(targetLink.hasClass('main-menu__link--sub')) {
      const parentLink = targetLink.parents('.main-menu__item--with-sub');

      parentLink.find('> a').trigger('click');
    } else {
      targetLink.trigger('click');
    }
  }
}

$(window).on('load', function () {
  getCurrentSection();
});


