<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CleanClass extends AbstractExtension {

  public function getFilters() {
    return [
      new TwigFilter('clean_class', [$this, 'MakeCleanClass']),
    ];
  }

  /**
   * Make a string into a safe classname
   *
   * @param string $classname string to be clean classed
   *
   * @return string sanitized
   */


  public function MakeCleanClass(string $classname) {

    if(empty($classname)){
      return '';
    }

    $sanitized = preg_replace('/[\W\-_]/', '-', $classname);

    return strtolower($sanitized);
  }
}