<?php

namespace App;


// create an image manager instance with favored driver

use Symfony\Component\Filesystem\Filesystem;

class FileSize {

  const BYTE_UNITS = ["B", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"];

  const BYTE_PRECISION = [0, 0, 1, 2, 2, 3, 3, 4, 4];

  const BYTE_NEXT = 1024;

  /**
   * Convert bytes to be human readable.
   *
   * @param int $bytes Bytes to make readable
   * @param int|null $precision Precision of rounding
   *
   * @return string Human readable bytes
   */
  public static function HumanReadableBytes($bytes, $precision = NULL) {
    for ($i = 0; ($bytes / self::BYTE_NEXT) >= 0.9 && $i < count(self::BYTE_UNITS); $i++) {
      $bytes /= self::BYTE_NEXT;
    }
    return round($bytes, is_null($precision) ? self::BYTE_PRECISION[$i] : $precision) . ' ' . self::BYTE_UNITS[$i];
  }
}


class MakeFile {

  public static function Make($basefile, $dir) {

    $filesystem = new Filesystem();
    $file = [];

    $fileName = $basefile->getBaseName();

    $file['name'] = $fileName;
    $file['path'] = $dir . '/' . $fileName;
    $file['size'] = FileSize::HumanReadableBytes($basefile->getSize());

    switch ($basefile->getExtension()) {
      case 'jpg':
      case 'png':
        $file['type'] = 'img';
        $file['descr'] = 'Te gebruiken voor web';

        $dimensions = getimagesize($basefile->getRealPath());

        $file['dimensions']['width'] = $dimensions[0];
        $file['dimensions']['height'] = $dimensions[1];

        if ($dimensions[0] > 600) {

          // Create a thumbnail if it's a large file
          $thumb = [];
          $thumbpath = $dir . '/thumbs/' . $fileName;

          if ($filesystem->exists($thumbpath)) {
            $thumb['path'] = $thumbpath;
            $thumb['name'] = $basefile->getBaseName();
          }
          else {
            $thumb = ScaleImage::Scale($dir, $basefile);
          }

          $file['thumb'] = $thumb;
        }
        break;
      case 'svg':
        $file['type'] = 'img';
        $file['descr'] = 'Te gebruiken voor web, vectorbestand';
        break;
      case 'eps':
        $file['type'] = 'eps';
        $file['descr'] = 'Te gebruiken voor drukwerk';
        break;
      case 'pdf':
        $file['type'] = 'pdf';
        $file['descr'] = 'Te gebruiken voor drukwerk';
        break;
    }

    return $file;
  }


}

