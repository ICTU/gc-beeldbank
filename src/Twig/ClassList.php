<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ClassList extends AbstractExtension {

  public function getFilters() {
    return [
      new TwigFilter('classlist', [$this, 'MakeList']),
    ];
  }

  /**
   * Make an array into a list of classes without spaces
   *
   * @param array $items array of string names
   *
   * @return string sanitized
   */


  public function MakeList(array $items) {

    $classlist = [];

    if(!is_array($items)){
      return '';
    }

    foreach ($items as $item) {

      // Filter out empties
      if (!empty($item)) {
        $classlist[] = trim($item);
      }
    }

    return implode(' ', $classlist);
  }
}