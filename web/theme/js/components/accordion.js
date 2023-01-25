const toggleButton = $('.collection__toggle-accordion');

toggleButton.on('click', function toggleCollectionContent() {
  const accordionContent = $('#' + $(this).attr('aria-controls'));

  if(!$(this).hasClass('open')){
    accordionContent.removeAttr('hidden');
    $(this).attr('aria-expanded', true);

    $(this).addClass('open');
  } else {
    accordionContent.attr('hidden', 'hidden');
    $(this).attr('aria-expanded', false);

    $(this).removeClass('open');
  }

})

const accordionButton = $('.accordion-item__toggle');

accordionButton.on('click', function toggleAccordionContent() {
  const accordionContent = $('#' + $(this).attr('aria-controls'));

  if(!$(this).hasClass('open')){
    accordionContent.removeAttr('hidden');
    $(this).attr('aria-expanded', true);

    $(this).addClass('open');
  } else {
    accordionContent.attr('hidden', 'hidden');
    $(this).attr('aria-expanded', false);

    $(this).removeClass('open');
  }

})


// =========================================================================================================
