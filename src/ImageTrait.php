<?php

namespace App;

use Intervention\Image\ImageManagerStatic as Image;
use Symfony\Component\Filesystem\Filesystem;

trait ImageTrait {
  /**
   * Scale a given image
   *
   * @param string $directory_path directory path
   * @param \SplFileInfo $file base file
   *
   * @return array with thumb data
   */

  public static function ScaleImage(string $directory_path, \SplFileInfo $file ) {
    $filesystem = new Filesystem();
    $thumb = [];

    $thumbnail_path = $directory_path . '/thumbs';
    $thumbnail_filepath = $thumbnail_path. '/' . $file->getBaseName();

    if (!$filesystem->exists($thumbnail_path)) {
      $filesystem->mkdir($thumbnail_path);
    }

    // There is no thumb, create
    if (!($filesystem->exists($thumbnail_filepath))) {
      $thumbImage = Image::make($file);
      $thumbImage->resize(350, NULL, function ($constraint) {
        $constraint->aspectRatio();
      });
      $thumbImage->save($thumbnail_filepath);

      $thumb['path'] = $thumbnail_filepath;
      $thumb['filename'] = $file->getBaseName();
    }

    return $thumb;
  }

}

