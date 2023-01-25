<?php

namespace App;

use Symfony\Component\Filesystem\Filesystem;


class MakeFile {

  use FileSizeTrait;
  use ImageTrait;

  /**
   * Return a file array
   *
   * @param \SplFileInfo $basefile Bytes to make readable
   * @param string $directory_path Path of directory
   * @param array|null $directory_config Config of directory
   *
   * @return array file with associated data
   */

  public static function Make(\SplFileInfo $basefile, string $directory_path, array $directory_config = NULL): array {
    $file = [];

    $fileName = $basefile->getBaseName();
    $file['name'] = $fileName;
    $file['path'] = $directory_path . '/' . $fileName;
    $file['size'] = FileSizeTrait::HumanReadableBytes($basefile->getSize());

    if (!empty($directory_config['files']) && array_key_exists($fileName, $directory_config['files'])) {
      $file['name'] = $directory_config['files'][$fileName]['name'] ?? $directory_config['files'][$fileName];
      $file['descr'] = $directory_config['files'][$fileName]['descr'] ?? '';
    }

    // Add info based on description
    switch ($basefile->getExtension()) {
      case 'jpg':
      case 'jpeg':
      case 'png':
        $file['type'] = 'img';
        $file['typedescr'] = 'Kan gebruikt worden op het web';

        // Get Dimensions
        $dimensions = getimagesize($basefile->getRealPath());

        $file['dimensions']['width'] = $dimensions[0];
        $file['dimensions']['height'] = $dimensions[1];

        if ($dimensions[0] > 600) {
          // Create a thumbnail if it's a large file
          $thumb_path = $directory_path . '/thumbs/' . $fileName;

          if (file_exists($thumb_path)) {
            $thumb['path'] = $thumb_path;
            $thumb['name'] = $basefile->getBaseName();
          }
          else {
            $thumb = ImageTrait::ScaleImage($directory_path, $basefile);
          }

          $file['thumb'] = $thumb ?? NULL;

          // Add size
          if ($dimensions[0] < 499) {
            $file['dimensions']['size'] = 'small';
          }
          elseif ($dimensions[0] > 500 && $dimensions[0] < 999) {
            $file['dimensions']['size'] = 'medium';
          }
          elseif ($dimensions[0] > 1000 && $dimensions[0] < 1599) {
            $file['dimensions']['size'] = 'large';
          }
          elseif ($dimensions[0] > 1600) {
            $file['dimensions']['size'] = 'x-large';
          }
        }
        break;
      case 'svg':
        $file['type'] = 'img';
        $file['typedescr'] = 'Te gebruiken voor web, vectorbestand';
        break;
      case 'eps':
        $file['type'] = 'eps';
        $file['typedescr'] = 'Te gebruiken voor drukwerk';
        break;
      case 'pdf':
        $file['type'] = 'pdf';
        $file['typedescr'] = 'Te gebruiken voor of als drukwerk';
        break;
      case 'ai':
        $file['type'] = 'ai';
        $file['typedescr'] = 'Bronbestand voor ontwerpen';
        break;
      case 'json':
        $file['type'] = 'json';

        if ($fileName !== 'config.json') {
          $data = json_decode(file_get_contents($file['path']), TRUE);
          $file['data'] = $data;

          switch ($data['type']) {
            case 'download':
              $file['download'] = '/' . $file['path'] = $directory_path . '/' . $file['data']['download'];
              break;
          }
        }
        break;
    }

    if (!file_exists($file['path'])) {
      return [];
    }

    return $file;
  }


}

