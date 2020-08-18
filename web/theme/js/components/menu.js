/**
 *
 * @type {jQuery.fn.init|jQuery|HTMLElement}
 * Functionality for menu
 */


const mainNav = $('.main-menu');

function doNav(width) {
  Waypoint.refreshAll();
}

console.log(mainNav);

$('.main-menu').waypoint(function (direction) {
  if (direction === 'down') {
    mainNav.addClass('fixed');
  } else {
    mainNav.removeClass('fixed');
  }
});

$(window).on('load', function () {
  var w = $(window).width();

  doNav(w);
});

$(window).on('resize', function () {
  var w = $(window).width();

  doNav(w);
});


// =========================================================================================================
