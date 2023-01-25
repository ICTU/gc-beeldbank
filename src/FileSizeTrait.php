<?php

namespace App;

trait FileSizeTrait {

  /**
   * Convert bytes to be human readable.
   *
   * @param int $bytes Bytes to make readable
   * @param int|null $precision Precision of rounding
   *
   * @return string Human readable bytes
   */
  public static function HumanReadableBytes($bytes, $precision = NULL): string {
    $byte_units = ["B", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"];
    $byte_precision = [0, 0, 1, 2, 2, 3, 3, 4, 4];
    $byte_next = 1024;

    for ($i = 0; ($bytes / $byte_next) >= 0.9 && $i < count($byte_units); $i++) {
      $bytes /= $byte_next;
    }

    return round($bytes, is_null($precision) ? $byte_precision[$i] : $precision) . ' ' . $byte_units[$i];
  }
}