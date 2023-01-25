/**
 *
 * @type {jQuery.fn.init|jQuery|HTMLElement}
 * Functionality for menu
 */


const mainNav = $('.main-menu');
const menuActive = $('.show-sub');
const mainMenuLink = $('.main-menu__link--main');

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
  const w = $(window).width();

  doNav(w);
});

$(window).on('resize', function () {
  const w = $(window).width();

  doNav(w);
});

if (!menuActive.length) {
  const firstMenuItem = $('.main-menu__list > li:first-child > .main-menu__link');
  const targetNode = $(firstMenuItem.attr('href'));

  firstMenuItem.parent().addClass('show-sub');
  $('.collection__content', targetNode).removeAttr('hidden');

}

mainMenuLink.on('click', function toggleActiveState() {
  const menuItem = $(this).parent();
  $('.collection__content').attr('hidden', 'hidden');
  $('.collection__toggle-foldout').attr('aria-expanded', 'false');
  const targetNode = $($(this).attr('href'));

  if (!menuItem.hasClass('show-sub')) {
    mainNav.find('.show-sub').removeClass('show-sub');
    mainNav.find('.active').removeClass('active');

    $('.collection__content', targetNode).removeAttr('hidden');
    $('.collection__toggle-foldout', targetNode).attr('aria-expanded', 'true');

    menuItem.addClass('show-sub');
    $(this).addClass('active');
  }
});


// =========================================================================================================
