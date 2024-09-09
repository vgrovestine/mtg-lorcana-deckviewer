<?php
/*
**
** Copyright 2024 Vincent Grovestine (vincent@grovestine.com) -- All rights reserved.
**
*/

namespace DeckViewer {

  /**************************************************************************************************/

  class Deck {
    protected $name;
    protected $cards;

    function __construct($name = 'Untitled Deck', $decklist_file = '') {
      $this->setName($name);
      $this->cards = array();
      if (!empty($decklist_file)) {
        $this->addCardsFromDecklist($decklist_file);
      }
      $this->sortByName();
      $this->sortByQuantity();
      $this->sortByPart();
    }

    protected function setName($name) {
      $name = trim($name);
      $this->name = $name;
      return (!empty($name));
    }

    function getName() {
      return $this->name;
    }

    protected function setCards($cards) {
      return false;
    }

    function getCards($part = '') {
      $card = array();
      foreach ($this->cards as $card) {
        if ($card->isPart($part)) {
          $cards[] = $card;
        }
      }
      return $cards;
    }

    function attribRarityScore($part = '') {
      $points = 0;
      foreach ($this->getCards($part) as $card) {
        $points += $card->getQuantity() * $card->attribRarityPoints();
      }
      $rarity_point_values = array_values($this->getFirstCard()::RARITY_POINTS);
      arsort($rarity_point_values, SORT_NUMERIC);
      $max_points = $rarity_point_values[0] * $this->attribQuantity($part);
      $score = 100 - round(($points / $max_points) * 100);
      return $score;
    }

    function attribColors($delimiter = '') {
      $colors = array();
      foreach ($this->getCards() as $card) {
        if (!$card->isColor(false) && !$card->isColor('C')) {
          if (empty($colors)) {
            $colors = $card->getColors();
          } else {
            $colors = array_merge($colors, $card->getColors());
          }
        }
      }
      $colors = (empty($colors) ? array('C') : array_unique($colors, SORT_STRING));
      return (empty($delimiter) ?  $colors : implode($delimiter, $colors));
    }

    function attribQuantity($part = '') {
      $quantity = 0;
      foreach ($this->getCards($part) as $card) {
        if ($card->isPart($part)) {
          $quantity += $card->getQuantity();
        }
      }
      return $quantity;
    }

    function attribTypeQuantity($type, $part = '') {
      $quantity = 0;
      foreach ($this->getCards($part) as $card) {
        if ($card->isType($type)) {
          $quantity += $card->getQuantity();
        }
      }
      return $quantity;
    }

    function attribTotalPrice($part = '') {
      $total_price = 0;
      foreach ($this->getCards($part) as $card) {
        $total_price += $card->getPrice() * $card->getQuantity();
      }
      return (empty($total_price) ? 'n/a' : number_format($total_price, 2));
    }

    function addCard($name, $quantity = 1, $part = '', $set_code = '', $favorite = '') {
      $this->cards[] = new Card($name, $quantity, $part, $set_code, $favorite);
    }

    function addCardsFromDecklist($file) {
      if (file_exists($file)) {
        $deck_list = file_get_contents($file);
        $part = '';
        if (!empty($deck_list)) {
          $deck_lines = explode("\n", $deck_list);
          foreach ($deck_lines as $line) {
            $line = trim($line);
            if (!empty($line)) {
              preg_match('/^((?P<quantity>\d+)\s+)?(?P<name>[^\(\d\*]+)(\s+(\((?P<set_code>\w+)\)))?(\s+(?P<collector_number>\d+))?(\s+(?P<favorite>\*+))?$/', $line, $tokens, PREG_UNMATCHED_AS_NULL);
              if (empty($tokens['quantity'])) {
                $part = $tokens['name'];
              } else {
                $this->addCard($tokens['name'], $tokens['quantity'], $part, $tokens['set_code'], $tokens['favorite']);
              }
            }
          }
          if (empty($this->getName())) {
            $this->setName(basename($file));
          }
          return true;
        }
      }
      return false;
    }

    function getCardByIdx($idx) {
      return (isset($this->cards[$idx]) ? $this->cards[$idx] : false);
    }

    function getFirstCard() {
      return $this->getCardByIdx(0);
    }

    function getLastCard() {
      return $this->getCardByIdx(count($this->getCards()) - 1);
    }

    function getFavoriteCards() {
      $favorite_cards = array();
      foreach ($this->cards as $card) {
        if ($card->isFavorite()) {
          $favorite_cards[] = $card;
        }
      }
      if (empty($favorite_cards)) {
        $favorite_cards[] = $this->getFirstCard();
      }
      return $favorite_cards;
    }

    function getValidParts() {
      //return array('deck', 'sideboard');
      return $this->getFirstCard()::VALID_PARTS;
    }

    function getDefaultPart() {
      //return 'deck';
      return $this->getFirstCard()::DEFAULT_PART;
    }

    function getParts() {
      $parts = array();
      foreach ($this->cards as $card) {
        $parts[] = $card->getPart();
      }
      $parts = array_unique($parts);
      return $parts;
    }

    function getTypes($part = '') {
      $types = array();
      foreach ($this->cards as $card) {
        if ($card->isPart($part)) {
          foreach ($card->getTypes() as $type) {
            $types[] = $type;
          }
        }
      }
      $types = array_unique($types);
      asort($types);
      return $types;
    }

    /*
    function writeCache() {
      $cache_path = dirname($_SERVER['SCRIPT_FILENAME']) . self::DATA_DIR_DECK_CACHE . $this->getName() . '.json';
      return file_put_contents($cache_path, json_encode($this));
    }

    function readCache() {
      $cache_path = dirname($_SERVER['SCRIPT_FILENAME']) . self::DATA_DIR_DECK_CACHE . $this->getName() . '.json';
      if (!file_exists(($cache_path))) {
        return false;
      }
      return file_get_contents($cache_path);
    }
    */

    function sortByName() {
      usort($this->cards, function ($a, $b) {
        return strcasecmp($a->getName(), $b->getName());
      });
    }

    function sortByTypes() {
      usort($this->cards, function ($a, $b) {
        return strcasecmp($a->getTypes(), $b->getTypes());
      });
    }

    function sortByQuantity() {
      usort($this->cards, function ($a, $b) {
        return $b->getQuantity() - $a->getQuantity();
      });
    }

    function sortByPart() {
      usort($this->cards, function ($a, $b) {
        return strcmp($a->getPart(), $b->getPart());
      });
    }

    function prepareDecklistAsText() {
      $deck = $this;
      $deck->sortByPart();
      $decklist_txt = '';

      foreach ($deck->getParts() as $part) {
        $part_txt = array("\n\n" . ucwords($part));
        $cards = $deck->getCards($part);
        foreach ($cards as $card) {
          $card_txt = array();
          $card_txt[] = $card->getQuantity();
          $card_txt[] = $card->getNameAndSubname();
          $card_txt[] = '(' . $card->getSetCode() . ')';
          if (!empty($card->getCollectorNumber())) {
            $card_txt[] = $card->getCollectorNumber();
          }
          $part_txt[] = implode(' ', $card_txt);
        }
        $decklist_txt .= implode("\n", $part_txt);
      }
      return trim($decklist_txt);
    }

    function markupAsImages($part = '', $label_parts = false) {
      $markup = '';
      if ($label_parts && !empty($part)) {
        $this->sortByPart();
      }
      $label_text = '';
      foreach ($this->getCards($part) as $card) {
        if ($label_parts && strcasecmp($label_text, $card->getPart()) !== 0) {
          $label_text = $card->getPart();
          $markup .= '<div class="card label ' . strtolower($label_text) . '"><p>' . ucwords($label_text) . '</p></div>';
        }
        $markup .= $card->markupAsImage($part);
      }
      return $markup;
    }

    function markupColors($colors) {
      $markup = '';
      if (!is_array($colors)) {
        preg_match_all('/[a-z0-9]/i', $colors, $colors);
      }
      foreach ($colors as $color) {
        $markup .= '<span class="color ' . strtolower($color) . '">' . $color . '</span>';
      }
      return $markup;
    }

    function markupAttributes() {
      $markup = '<dl class="attributes">';
      $markup .= '<dt class="quantity">' . ucwords($this->getDefaultPart()) . ' Size</dt>';
      $markup .= '<dd class="quantity">' . $this->attribQuantity($this->getDefaultPart()) . ' cards</dd>';
      $markup .= '<dt class="colors">Colors</dt>';
      $markup .= '<dd class="colors">' . $this->markupColors($this->attribColors()) . '</dd>';
      $markup .= '<dt class="types">Card Types</dt>';
      $markup .= '<dd class="types"><ul>';
      foreach ($this->getTypes($this->getDefaultPart()) as $type) {
        $markup .= '<li>' . $type . ': ' . $this->attribTypeQuantity($type, $this->getDefaultPart()) . ' (' . round(100 * $this->attribTypeQuantity($type, $this->getDefaultPart()) / $this->attribQuantity($this->getDefaultPart()), 1) . '%)</li>';
      }
      $markup .= '</ul></dd>';
      $markup .= '<dt class="rarity">Rarity Score</dt>';
      $markup .= '<dd class="rarity">' . $this->attribRarityScore($this->getDefaultPart()) . '<span class="secondary_info">/100</span></dd>';
      $markup .= '<dt class="price">Deck Price</dt>';
      $markup .= '<dd class="price">$' . $this->attribTotalPrice($this->getDefaultPart()) . ' <span class="secondary_info">(USD)</span></dd>';
      $markup .= '</dl>';
      return $markup;
    }
  }

  /**************************************************************************************************/

  class MtgDeck extends Deck {

    function __construct($name = 'Untitled Deck', $decklist_file = '') {
      parent::__construct($name, $decklist_file);
      $this->sortByName();
      $this->sortByLegendary();
      $this->sortByQuantity();
      $this->sortLandsLast();
      $this->sortByPart();
    }

    function addCard($name, $quantity = 1, $part = '', $set_code = '', $favorite = '') {
      $this->cards[] = new MtgCard($name, $quantity, $part, $set_code, $favorite);

      $last_idx = count($this->cards) - 1;
      for ($k = 0; $k < $last_idx; $k++) {
        if (
          $this->cards[$k]->getName() == $this->cards[$last_idx]->getName()
          && $this->cards[$k]->getSubname() == $this->cards[$last_idx]->getSubname()
          && $this->cards[$k]->getSetCode() == $this->cards[$last_idx]->getSetCode()
          && $this->cards[$k]->getPart() == $this->cards[$last_idx]->getPart()
        ) {
          $this->cards[$k]->setQuantity($this->cards[$k]->getQuantity() + $this->cards[$last_idx]->getQuantity());
          array_pop($this->cards);
          $last_idx--;
        }
      }
    }

    function attribFormats($delimiter = '') {
      $formats = array();
      $singleton = true;
      foreach ($this->getCards() as $card) {
        if (empty($formats)) {
          $formats = $card->getFormats();
        } else {
          $formats = array_intersect($formats, $card->getFormats());
        }
        if ($card->getQuantity() > 1) {
          $singleton = false;
        }
      }
      if (!$singleton) {
        $singleton_formats = array();
        foreach ($formats as $format) {
          if (preg_match('/(commander|brawl|highlander|gladiator|oathbreaker)/i', $format)) {
            $singleton_formats[] = $format;
          }
        }
        $formats = array_diff($formats, $singleton_formats);
      }
      return (empty($delimiter) ? $formats : implode($delimiter, $formats));
    }

    function attribColors($delimiter = '') {
      $colors = array();
      foreach ($this->getCards() as $card) {
        if (!$card->isColor(false) && !$card->isColor('C')) {
          if (empty($colors)) {
            $colors = $card->getColors();
          } else {
            $colors = array_merge($colors, $card->getColors());
          }
        }
      }
      $colors = (empty($colors) ? array('C') : array_unique($colors, SORT_STRING));
      return (empty($delimiter) ? $colors : implode($delimiter, $colors));
    }

    function attribAverageCmc($part = '') {
      $cnt_cmc = 0;
      $tot_cmc = 0;
      foreach ($this->getCards($part) as $card) {
        if (!is_null($card->getCmc())) {
          $cnt_cmc += $card->getQuantity();
          $tot_cmc += $card->getQuantity() * $card->getCmc();
        }
      }
      $average_cmc = round($tot_cmc / $cnt_cmc, 1);
      return $average_cmc;
    }

    function attribQuantity($part = '', $include_land = true) {
      $quantity = 0;
      foreach ($this->getCards($part) as $card) {
        if ($include_land || (!$include_land && !$card->isLand())) {
          $quantity += $card->getQuantity();
        }
      }
      return $quantity;
    }

    function attribSuggestedLandQuantity($part = '') {
      $cmc_land_quantity = 20 + $this->attribAverageCmc($part) * 2;
      $non_land_quantity = 60 - $cmc_land_quantity;
      $land_quantity = round($cmc_land_quantity * $this->attribQuantity($part, false) / $non_land_quantity);
      return $land_quantity;
    }

    function sortByLegendary() {
      usort($this->cards, function ($a, $b) {
        return $a->isLegendary() < $b->isLegendary();
      });
    }

    function sortLandsLast() {
      usort($this->cards, function ($a, $b) {
        if ($a->isBasicLand()) {
          $a_name = 'zzz' . $a->getName();
        } else if ($a->isLand()) {
          $a_name = 'yyy' . $a->getName();
        } else {
          $a_name = 'aaa';
        }
        if ($b->isType('basic land')) {
          $b_name = 'zzz' . $b->getName();
        } else if ($b->isLand()) {
          $b_name = 'yyy' . $b->getName();
        } else {
          $b_name = 'aaa';
        }
        return strcasecmp($a_name, $b_name);
      });
    }

    function markupColorSymbols($colors) {
      $markup = '';
      if (!is_array($colors)) {
        preg_match_all('/[a-z0-9]/i', $colors, $colors);
      }
      foreach ($colors as $color) {
        $markup .= '<span class="ms ms-' . strtolower($color) . '">' . $color . '</span>';
      }
      return $markup;
    }

    function markupAsTable($part = '') {
      $markup = '';
      foreach ($this->getCards($part) as $card) {
        $markup .= $card->markupAsTableRow($part);
      }
      if (!empty($markup)) {
        $markup = '<table>'
          . (empty($part) ? '' : '<caption>' . ucwords($part) . '</caption>')
          . '<thead><tr>'
          . '<th class="quantity">Qty</th>'
          . '<th class="name">Card Name</th>'
          . '<th class="type">Type</th>'
          . '<th class="colors">Colors</th>'
          . '<th class="cost">Cost</th>'
          . '<th class="cmc">CMC</th>'
          . '<th class="rarity">Rarity</th>'
          . '<th class="set">Set</th>'
          . '<th chass="price">USD ea.</th>'
          . '<th class="part">Part</th>'
          . '</tr></thead>'
          . '<tbody>'
          . $markup
          . '</tbody>'
          . '</table>';
      }
      return $markup;
    }

    function markupAttributes() {
      $markup = '<dl class="attributes">';
      $markup .= '<dt class="quantity">' . ucwords($this->getDefaultPart()) . ' Size</dt>';
      $markup .= '<dd class="quantity">' . $this->attribQuantity($this->getDefaultPart()) . ' cards</dd>';
      $markup .= '<dt class="colors">Colors</dt>';
      $markup .= '<dd class="colors">' . $this->markupColorSymbols($this->attribColors()) . '</dd>';
      $markup .= '<dt class="cmc">Average CMC</dt>';
      $markup .= '<dd class="cmc"><ul>';
      foreach ($this->getParts() as $part) {
        $markup .= '<li>' . ucwords($part) . ': ' . $this->attribAverageCmc($part) . '</li>';
      }
      $markup .= '</ul></dd>';
      $markup .= '<dt class="lands">Suggested Land Quantity</dt>';
      $markup .= '<dd class="lands"><span class="secondary_info">&plusmn;</span>' . $this->attribSuggestedLandQuantity($this->getDefaultPart()) . '</dd>';

      $markup .= '<dt class="types">Card Types</dt>';
      $markup .= '<dd class="types"><ul>';
      foreach ($this->getTypes($this->getDefaultPart()) as $type) {
        $markup .= '<li>' . $type . ': ' . $this->attribTypeQuantity($type, $this->getDefaultPart()) . ' (' . round(100 * $this->attribTypeQuantity($type, $this->getDefaultPart()) / $this->attribQuantity($this->getDefaultPart()), 1) . '%)</li>';
      }
      $markup .= '</ul></dd>';
      $markup .= '<dt class="formats">Legal Formats</dt>';
      $markup .= '<dd class="formats"><ul>';
      foreach ($this->attribFormats() as $format) {
        $markup .= '<li>' . ucwords($format) . '</li>';
      }
      $markup .= '</ul></dd>';
      $markup .= '<dt class="rarity">Rarity Score</dt>';
      $markup .= '<dd class="rarity">' . $this->attribRarityScore($this->getDefaultPart()) . '<span class="secondary_info">/100</span></dd>';
      $markup .= '<dt class="price">Deck Price</dt>';
      $markup .= '<dd class="price">$' . $this->attribTotalPrice($this->getDefaultPart()) . ' <span class="secondary_info">(USD)</span></dd>';
      $markup .= '</dl>';
      return $markup;
    }
  }

  /**************************************************************************************************/

  class LorcanaDeck extends Deck {

    function __construct($name = 'Untitled Deck', $decklist_file = '') {
      parent::__construct($name, $decklist_file);
      $this->sortByName();
      $this->sortByQuantity();
      $this->sortByColor();
      $this->sortByPart();
    }

    function addCard($name, $quantity = 1, $part = '', $set_code = '', $favorite = '') {
      $this->cards[] = new LorcanaCard($name, $quantity, $part, $set_code, $favorite);

      $last_idx = count($this->cards) - 1;
      for ($k = 0; $k < $last_idx; $k++) {
        if (
          $this->cards[$k]->getName() == $this->cards[$last_idx]->getName()
          && $this->cards[$k]->getSubname() == $this->cards[$last_idx]->getSubname()
          && $this->cards[$k]->getSetCode() == $this->cards[$last_idx]->getSetCode()
          && $this->cards[$k]->getPart() == $this->cards[$last_idx]->getPart()
        ) {
          $this->cards[$k]->setQuantity($this->cards[$k]->getQuantity() + $this->cards[$last_idx]->getQuantity());
          array_pop($this->cards);
          $last_idx--;
        }
      }
    }

    function sortByColor() {
      usort($this->cards, function ($a, $b) {
        return strcasecmp($a->getColors(' '), $b->getColors(' '));
      });
    }

    function markupAsTable($part = '') {
      $markup = '';
      foreach ($this->getCards($part) as $card) {
        $markup .= $card->markupAsTableRow($part);
      }
      if (!empty($markup)) {
        $markup = '<table>'
          . (empty($part) ? '' : '<caption>' . ucwords($part) . '</caption>')
          . '<thead><tr>'
          . '<th class="quantity">Qty</th>'
          . '<th class="name">Card Name</th>'
          . '<th class="types">Type</th>'
          . '<th class="colors">Color</th>'
          . '<th class="cost">Cost</th>'
          . '<th class="inkwell">Inkwell</th>'
          . '<th class="lore">Lore</th>'
          . '<th class="rarity">Rarity</th>'
          . '<th class="set">Set</th>'
          . '<th class="price">USD ea.</th>'
          . '<th class="part">Part</th>'
          . '</tr></thead>'
          . '<tbody>'
          . $markup
          . '</tbody>'
          . '</table>';
      }
      return $markup;
    }
  }

  /**************************************************************************************************/
}
