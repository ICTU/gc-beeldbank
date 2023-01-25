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

  public function MakeList($items) {

    $classlist = [];

    foreach ($items as $item) {

      // Filter out empties
      if (!empty($item)) {
        $classlist[] = trim($item);
      }
    }

    $content = implode($classlist, ' ');

    return $content;
  }
}