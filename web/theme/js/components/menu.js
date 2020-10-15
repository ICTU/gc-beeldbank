/**
 *
 * @type {jQuery.fn.init|jQuery|HTMLElement}
 * Functionality for menu
 */


const mainNav = $('.main-menu');
const menuActive = $('.show-sub');

function doNav(width) {
  Waypoint.destroyAll();

  mainNav.waypoint(function (direction) {
    if (direction === 'down') {
      mainNav.addClass('fixed');
    } else {
      mainNav.removeClass('fixed');
    }
  });
}

$(window).on('load', function () {
  var w = $(window).width();

  doNav(w);
});

$(window).on('resize', function () {
  var w = $(window).width();

  doNav(w);
});


if(!menuActive.length); {
  $('.main-menu__item:first-child').addClass('show-sub');
}


$('.menu-link--main').on('click', function(){
  const menuItem = $(this).parent();

  if(!menuItem.hasClass('show-sub')){
    mainNav.find('.show-sub').removeClass('show-sub');
    mainNav.find('.active').removeClass('active');

    menuItem.addClass('show-sub');
    $(this).addClass('active');
  }
});



// =========================================================================================================
