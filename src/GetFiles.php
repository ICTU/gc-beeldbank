<?php

namespace App;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class Files {

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

class GetFiles {

  public static function files($dir) {
    // Custom frontpage url for different brands.

    $files = [];
    $finder = new Finder();
    $filesystem = new Filesystem();

    $dirs = $finder->directories()->in($dir)->notPath('/files')->depth('== 0');
    $d = 0;

    foreach ($dirs as $dir) {
      $i = 0;
      $d++;


      // Get subdir files
      $subdir = $dir->getRealPath();
      $subdirfinder = new Finder();

      if ($filesystem->exists($dir->getRealPath() . "/config.json")) {
        $dirdata = file_get_contents($dir->getRealPath() . "/config.json");
        $dirdata = json_decode($dirdata, TRUE);

        $files[$d]['name'] = $dirdata['name'];
        $files[$d]['descr'] = $dirdata['descr'];
      }

      $dirfiles = $subdirfinder->files()->in($subdir)->Name([
        '*.png',
        '*.jpg',
        '*.jpg',
        '*.svg',
        '*.eps',
      ])->depth('== 0')->sortByAccessedTime();


      // Get files or directory
      foreach ($dirfiles as $file) {
        $i++;

        $relPath = '/files/' . $dir->getBaseName() . '/' . $file->getBaseName();

        $files[$d]['files'][$i]['name'] = $file->getBaseName();
        $files[$d]['files'][$i]['path'] = $relPath;
        $files[$d]['files'][$i]['size'] = Files::HumanReadableBytes($file->getsize());
        $files[$d]['files'][$i]['added'] = $file->getmTime();
        $files[$d]['files'][$i]['changed'] = $file->getcTime();

        switch ($file->getExtension()) {
          case 'jpg':
          case 'png':

            $files[$d]['files'][$i]['type'] = 'img';
            $files[$d]['files'][$i]['descr'] = 'Te gebruiken voor web';

            $dimensions = getimagesize($file->getRealPath());

            if ($dimensions) {
              $files[$d]['files'][$i]['dimensions']['width'] = $dimensions[0];
              $files[$d]['files'][$i]['dimensions']['height'] = $dimensions[1];

              // create thumb if it's a big file
              if ($dimensions[0] > 600 && ($file->getExtension() === 'jpg' || $file->getExtension() == 'png')) {

                // check if thumb

                $thumb = ScaleImage::Scale($dir->getBaseName(), $file);

                if ($thumb) {
                  $files[$d]['files'][$i]['thumb']['name'] = $thumb['filename'];
                  $files[$d]['files'][$i]['thumb']['path'] = $thumb['path'];
                }
              }
            }
            break;
          case 'svg':
            $files[$d]['files'][$i]['type'] = 'img';
            $files[$d]['files'][$i]['descr'] = 'Te gebruiken voor web, vectorbestand';
            break;
          case 'pdf':
            $files[$d]['files'][$i]['type'] = 'pdf';
            $files[$d]['files'][$i]['descr'] = 'Te gebruiken voor drukwerk, vectorbestand';
            break;

          default:
            $files[$d]['files'][$i]['type'] = 'img';
            break;
        }

      }
    }


    return $files;

  }
}