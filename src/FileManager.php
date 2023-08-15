<?php

namespace App;

use Symfony\Component\Finder\Finder;
use App\ImageTrait;


class FileManager {

  use FileSizeTrait;

  /**
   * Filter out opening hours exceptions which are in the past.
   */
  public function buildTree($pathname, $allowed, $level): array {
    $finder = new Finder();
    $return = [];

    $directoryList = $finder->directories()
      ->in($pathname)
      ->notPath('files')
      ->notPath('thumbs')
      ->depth('==0')
      ->sortByName();

    foreach ($directoryList as $directory) {
      $childDir = $pathname . '/' . $directory->getBaseName();
      $directory_basename = $directory->getBasename();
      $directory_config = [];
      $fileFinder = new Finder();
      $file_list = [];
      $directory_info['name'] = $directory_basename;

      // Fetch config
      $directory_config_file = $directory->getRealPath() . '/config.json';


      if ($directory_basename === 'cmyk') {
        $directory_config['name'] = 'Voor drukwerk';
        $directory_config['descr'] = 'Deze bestanden kan je gebruiken voor drukwerk.';
      }

      if ($directory_basename === 'rgb') {
        $directory_config['name'] = 'Voor web';
        $directory_config['descr'] = 'Deze bestanden zijn voor gebruik op het web.';
      }

      if (file_exists($directory_config_file)) {
        $directory_config = json_decode(file_get_contents($directory_config_file), TRUE);
      }
      elseif (!(file_exists($directory_config_file))) {
        $config_path = $pathname . '/config.json';

        if (file_exists($config_path)) {
          $directory_config = json_decode(file_get_contents($pathname . '/config.json'), TRUE);

          if (isset($directory_config['subdirs'][$directory_basename])) {
            $subdir_info = $directory_config['subdirs'][$directory_basename];

            $directory_config['name'] = !empty($subdir_info['subdir_name']) ? $subdir_info['subdir_name'] : $directory_basename;
            $directory_config['descr'] = !empty($subdir_info['descr']) ? $subdir_info['descr'] : '';
          }

          // There isn't a subdirectory name to use
          // and we don't want to use the name from the root config
          if ($level >= 1 && !isset($directory_config['subdirs'][$directory_basename])) {
            $directory_config = [
              'name' => $directory_basename,
              'descr' => '',
            ];
          }
        }
      }

      $directory_info = [
        'name' => !empty($directory_config['name']) ? $directory_config['name'] : $directory_basename,
        'descr' => !empty($directory_config['descr']) ? $directory_config['descr'] : '',
        'display_name' => !empty($directory_config['display_name']) ? $directory_config['display_name'] : '',
      ];

      // Fetch all files
      $files_in_directory = $fileFinder->files()
        ->in($directory->getRealPath())
        ->Name($allowed)
        ->notName('config.json')
        ->depth(0)
        ->sortByName();

      if ($files_in_directory->hasResults()) {
        foreach ($files_in_directory as $file) {
          $file_list[] = MakeFile::Make($file, $pathname . '/' . $directory_basename, $directory_config);
        }
      }

      $id = $childDir . '-' . $level;

      $return[] = [
        $childDir => is_dir($childDir) ? $this->buildTree($childDir, $allowed, $level + 1) : [],
        'files' => !empty($file_list) ? $file_list : [],
        'info' => $directory_info,
        'subdir_name' => !empty($this->buildTree($childDir, $allowed, $level + 1)) ? $childDir : '',
        'id' => strtolower(preg_replace('/[\W\-_]/', '-', $id)),
      ];
    }

    return $return;
  }

  /**
   * Create thumbs if they don't exist yet.
   *
   * @param array $array which needs to be searched.
   * @param string $key_to_fetch values of key we'd like to return
   *
   * @return array of paths which need to be thumbed.
   */

  public static function fetchThumbPaths(array $files, string $key_to_fetch): array {
    $results = [];

    $array_keys = array_keys($files);

    if (!$array_keys) {
      return [];
    }

    foreach ($array_keys as $key) {
      if ($key === $key_to_fetch) {
        if (!file_exists($files[$key])) {
          $results[] = $files[$key];
        }
      }

      if (is_array($files[$key])) {
        $results = array_merge($results, fileManager::fetchThumbPaths($files[$key], $key_to_fetch));
      }
    }

    return $results;
  }

  /**
   * Get all directories - and subdirectories within a given directory.
   *
   * @param string $directory path to directory.
   *
   * @return array with all files.
   */


  public static function getAllFiles(string $directory): array {
    $files = [];

    $allowed_extensions = [
      '*.png',
      '*.jpg',
      '*.jpeg',
      '*.svg',
      '*.eps',
      '*.pdf',
      '*.ai',
      '*.json',
    ];

    // All files that need to be shown
    $files[] = (new FileManager)->buildTree($directory, $allowed_extensions, 0);

    return $files[0];
  }

}