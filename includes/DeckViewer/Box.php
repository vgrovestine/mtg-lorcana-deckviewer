<?php
/*
**
** Copyright 2024 Vincent Grovestine (vincent@grovestine.com) -- All rights reserved.
**
*/

namespace DeckViewer {

  /**************************************************************************************************/

  class Box {
    protected $decklist_dir;

    private const DEFAULT_DECKLIST_DIR = '/decks/';

    function __construct($decklist_dir = '') {
      $this->decklist_dir = (empty($decklist_dir) ? self::DEFAULT_DECKLIST_DIR : $decklist_dir);
      if (!is_dir($decklist_dir)) {
        $this->decklist_dir = dirname($_SERVER['SCRIPT_FILENAME']) . $this->decklist_dir;
        if (!is_dir($decklist_dir)) {
          die('Fatal error: Decklist directory (' . $decklist_dir . ') does not exist.');
        }
      }
    }

    function markupNavigation($base_href = '') {
      $glob = glob($this->decklist_dir . '*.txt');
      $markup = '';
      foreach ($glob as $deck_file) {
        $markup .= '<li><a href="' . $base_href . '?deck=' . urlencode(basename($deck_file, '.txt')) . '">' . basename($deck_file, '.txt') . '</a></li>';
      }
      $markup = '<nav class="deck_box"><ul>' . $markup . '</ul></nav>';
      return $markup;
    }
  }

  /**************************************************************************************************/

  class MtgBox extends Box {
    function getDeckFromUrl() {
      $deck_name = '';
      if (array_key_exists('deck', $_GET)) {
        if (!empty($_GET['deck'])) {
          $deck_name = trim(urldecode($_GET['deck']));
        }
      }
      if (!empty($deck_name)) {
        return new MtgDeck(ucwords($deck_name), $this->decklist_dir . $deck_name . '.txt');
      }
      return false;
    }
  }

  /**************************************************************************************************/

  class LorcanaBox extends Box {
    function getDeckFromUrl() {
      $deck_name = '';
      if (array_key_exists('deck', $_GET)) {
        if (!empty($_GET['deck'])) {
          $deck_name = trim(urldecode($_GET['deck']));
        }
      }
      if (!empty($deck_name)) {
        return new LorcanaDeck(ucwords($deck_name), $this->decklist_dir . $deck_name . '.txt');
      }
      return false;
    }
  }

  /**************************************************************************************************/
}
