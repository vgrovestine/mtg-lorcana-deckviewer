<?php
/*
**
** Copyright 2024 Vincent Grovestine (vincent@grovestine.com) -- All rights reserved.
**
*/

namespace DeckViewer {

  /**************************************************************************************************/

  class Utility {

    static function diagnostic($thing, $value, $data_dir = '/data/diagnostics/') {
      $now = time();
      $backtrace = debug_backtrace();
      $txt = array();
      $txt[] = date('Y-m-d H:i:s', $now);
      $txt[] = @array_pop(explode('\\', $backtrace[1]['class'])) . '.' . $backtrace[1]['function'];
      $txt[] = (string) $thing;
      $txt[] = (string) $value;
      $txt = implode("\t", $txt) . "\n";
      return file_put_contents(dirname($_SERVER['SCRIPT_FILENAME']) . $data_dir . date('ymd', $now) . '-log.txt', $txt, FILE_APPEND | LOCK_EX);
    }

    static function dump($value, $exit = false, $verbose = false) {
      echo '<pre class="dump">';
      if ($verbose) {
        var_dump($value);
      } else {
        print_r($value);
      }
      echo '</pre>';
      if ($exit) {
        exit();
      }
    }
  }

  /**************************************************************************************************/
}
