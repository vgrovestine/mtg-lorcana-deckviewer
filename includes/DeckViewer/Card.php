<?php
/*
**
** Copyright 2024 Vincent Grovestine (vincent@grovestine.com) -- All rights reserved.
**
*/

namespace DeckViewer {

  /**************************************************************************************************/

  class Card {
    protected $name;
    protected $subname;
    protected $types;
    protected $subtypes;
    protected $colors;
    protected $cost;
    protected $cabbage;
    protected $power;
    protected $toughness;
    protected $rules_text;
    protected $set_code;
    protected $set_name;
    protected $collector_number;
    protected $rarity;
    protected $image_urls;
    protected $info_urls;
    protected $price_usd;
    protected $quantity;
    protected $part;
    protected $favorite;

    const VALID_PARTS = array('deck', 'sideboard');
    const DEFAULT_PART = self::VALID_PARTS[0];

    const RARITY_POINTS = array(
      'common' => 12,
      'uncommon' => 6,
      'rare' => 3,
      'other' => 1
    );

    function __construct($name, $quantity = 1, $part = '', $set_code = '', $favorite = 0) {
      $this->setNameAndSubname($name);
      $this->setTypes(null);
      $this->setSubtypes(null);
      $this->setColors(null);
      $this->setCost(null);
      $this->setCabbage(null);
      $this->setPower(null);
      $this->setToughness(null);
      $this->setRulesText(null);
      $this->setSetCode($set_code);
      $this->setCollectorNumber(null);
      $this->setRarity(null);
      $this->setImageUrls(null);
      $this->setInfoUrl(null, 'card');
      $this->setInfoUrl(null, 'set');
      $this->setPrice(null);
      $this->setQuantity($quantity);
      $this->setPart($part);
      $this->setFavorite($favorite);
    }

    /******************************************************************************/

    protected function setNameAndSubname($name, $delimiter = ' - ') {
      $name = trim($name);
      $tokens = explode($delimiter, $name);
      $this->setName($tokens[0]);
      $this->setSubname(isset($tokens[1]) ? $tokens[1] : '');
      return (!empty($name));
    }

    function getNameAndSubname($delimiter = ' - ') {
      return $this->name . (!empty($this->subname) ? $delimiter . $this->subname : '');
    }

    protected function setName($name) {
      $name = trim($name);
      $this->name = $name;
      return (!empty($name));
    }

    function getName() {
      return $this->name;
    }

    protected function setSubname($subname) {
      $subname = trim($subname);
      $this->subname = $subname;
      return (!empty($subname));
    }

    function getSubname() {
      return $this->subname;
    }

    protected function setTypes($types) {
      if (!is_array($types)) {
        $types = array($types);
      }
      for ($k = 0; $k < count($types); $k++) {
        $types[$k] = ucwords(trim($types[$k]));
      }
      $types = array_filter($types, 'strlen');
      $this->types = $types;
      return (!empty($types));
    }

    function getTypes($delimiter = '') {
      return (empty($delimiter) ? $this->types : implode($delimiter, $this->types));
    }

    function isType($type = '') {
      return (in_array(trim($type), $this->getTypes()));
    }

    protected function setSubtypes($subtypes) {
      if (!is_array($subtypes)) {
        $subtypes = array($subtypes);
      }
      for ($k = 0; $k < count($subtypes); $k++) {
        $subtypes[$k] = ucwords(trim($subtypes[$k]));
      }
      $subtypes = array_filter($subtypes, 'strlen');
      $this->subtypes = $subtypes;
      return (!empty($subtypes));
    }

    function getSubtypes($delimiter = '') {
      return (empty($delimiter) ? $this->subtypes : implode($delimiter, $this->subtypes));
    }

    function isSubtype($subtype = '') {
      return (in_array(trim($subtype), $this->getSubtypes()));
    }

    protected function setColors($colors = 'Colorless') {
      if (!is_array($colors)) {
        $colors = array($colors);
      }
      for ($k = 0; $k < count($colors); $k++) {
        $colors[$k] = ucwords(trim($colors[$k]));
      }
      $colors = array_filter($colors, 'strlen');
      $this->colors = $colors;
      return (!empty($colors));
    }

    function getColors($delimiter = '') {
      return (empty($delimiter) ? $this->colors : implode($delimiter, $this->colors));
    }

    function isColor($color = '') {
      $color = trim($color);
      if (empty($color) && empty($this->getColors())) {
        return true;
      } else if (in_array($color, $this->getColors())) {
        return true;
      }
      return false;
    }

    protected function setCost($cost) {
      $cost = trim($cost);
      $this->cost = $cost;
      return (!empty($cost));
    }

    function getCost() {
      return $this->cost;
    }

    protected function setCabbage($cabbage) {
      $cabbage = trim($cabbage);
      if (strlen($cabbage) == 0) {
        $this->cabbage = '';
        return false;
      }
      $this->cabbage = (empty($cabbage) ? 0 : 1);
      return true;
    }

    function getCabbage() {
      return $this->cabbage;
    }

    function isCabbage() {
      return $this->getCabbage();
    }

    protected function setPower($power) {
      $power = trim($power);
      if (is_numeric($power)) {
        $power = abs(round($power));
      } else if (in_array(strtolower($power), array('*,x,?'))) {
        $power = '*';
      } else {
        $power = '';
      }
      $this->power = $power;
      return (!empty($power));
    }

    function getPower() {
      return $this->power;
    }

    protected function setToughness($toughness) {
      $toughness = trim($toughness);
      if (is_numeric($toughness)) {
        $toughness = abs(round($toughness));
      } else if (in_array(strtolower($toughness), array('*,x,?'))) {
        $toughness = '*';
      } else {
        $toughness = '';
      }
      $this->toughness = $toughness;
      return (!empty($toughness));
    }

    function getToughness() {
      return $this->toughness;
    }

    protected function setRulesText($rules_text) {
      $rules_text = trim($rules_text);
      $this->rules_text = $rules_text;
      return (!empty($rules_text));
    }

    function getRulesText() {
      return $this->rules_text;
    }

    protected function setSetCode($set_code) {
      $set_code = strtoupper(trim($set_code));
      $this->set_code = $set_code;
      return (!empty($set_code));
    }

    function getSetCode() {
      return $this->set_code;
    }

    protected function setSetName($set_name) {
      $set_name = ucwords(trim($set_name));
      $this->set_name = $set_name;
      return (!empty($set_name));
    }

    function getSetName() {
      return $this->set_name;
    }

    function isSet($set_identifier = '') {
      $set_identifier = trim($set_identifier);
      if (strcasecmp($set_identifier, $this->getSetCode()) === 0) {
        return true;
      }
      if (stripos($this->getSetName(), $set_identifier) !== false) {
        return true;
      }
      return false;
    }

    protected function setCollectorNumber($collector_number) {
      $collector_number = trim($collector_number);
      if (!is_numeric($collector_number)) {
        $collector_number = '';
      }
      $this->collector_number = $collector_number;
      return (!empty($collector_number));
    }

    function getCollectorNumber() {
      return $this->collector_number;
    }

    protected function setRarity($rarity) {
      $rarity = strtolower(str_replace('_', ' ', trim($rarity)));
      $this->rarity = $rarity;
      return (!empty($rarity));
    }

    function getRarity() {
      return $this->rarity;
    }

    function isRarity($rarity) {
      return (strcasecmp(trim($rarity), $this->getRarity()) === 0 ? true : false);
    }

    protected function setImageUrls($image_urls) {
      if (!is_array($image_urls)) {
        $image_urls = array($image_urls);
      }
      for ($k = 0; $k < count($image_urls); $k++) {
        $image_urls[$k] = trim($image_urls[$k]);
      }
      $image_urls = array_filter($image_urls, 'strlen');
      $this->image_urls = $image_urls;
      return (!empty($image_urls));
    }

    function getImageUrls() {
      return $this->image_urls;
    }

    function getImageUrl() {
      return (empty($this->image_urls) ? false : $this->image_urls[0]);
    }

    protected function setInfoUrl($info_url, $key = 'card') {
      $info_url = trim($info_url);
      $key = trim($key);
      if (empty($key)) {
        return false;
      }
      if (!array($this->info_urls)) {
        $this->info_urls = array();
      }
      $this->info_urls[$key] = $info_url;
      return (!empty($info_url));
    }

    function getInfoUrl($key = 'card') {
      $key = trim($key);
      return (array_key_exists($key, $this->info_urls) ? $this->info_urls[$key] : false);
    }

    protected function setPrice($price_usd) {
      $price_usd = trim($price_usd);
      if (is_numeric($price_usd)) {
        $price_usd = round($price_usd, 2);
      } else {
        $price_usd = null;
      }
      $this->price_usd = $price_usd;
      return (!empty($price_usd));
    }

    function getPrice() {
      return number_format($this->price_usd, 2);
    }

    public function setQuantity($quantity) {
      $quantity = trim($quantity);
      if (is_numeric($quantity)) {
        $quantity = abs(round($quantity));
      } else {
        $quantity = 1;
      }
      $this->quantity = $quantity;
      return (!empty($quantity));
    }

    function getQuantity() {
      return $this->quantity;
    }

    protected function setPart($part) {
      $part = strtolower(trim($part));
      $this->part = (in_array($part, self::VALID_PARTS) ? $part : self::DEFAULT_PART);
    }

    function getPart() {
      return $this->part;
    }

    function isPart($part) {
      $part = trim($part);
      if (empty($part)) {
        return true;
      }
      return (strcasecmp($this->getPart(), $part) === 0 ? true : false);
    }

    public function setFavorite($favorite) {
      $favorite = trim($favorite);
      if (strlen($favorite) == 0) {
        $this->favorite = '';
        return false;
      }
      $this->favorite = (empty($favorite) ? 0 : 1);
      return true;
    }

    function getFavorite() {
      return $this->favorite;
    }

    function isFavorite() {
      return $this->getFavorite();
    }

    /******************************************************************************/

    protected function readApiCache($filename, $cache_dir, $cache_timeout) {
      $data = false;
      if (
        file_exists(dirname($_SERVER['SCRIPT_FILENAME']) . $cache_dir . $filename)
        && (time() - filemtime(dirname($_SERVER['SCRIPT_FILENAME']) . $cache_dir . $filename) < $cache_timeout)
      ) {
        $data = file_get_contents(dirname($_SERVER['SCRIPT_FILENAME']) . $cache_dir . $filename);
      }
      Utility::diagnostic('file_get_contents()', strlen($data) . ' from ' . $cache_dir . $filename . ' (timeout: ' . $cache_timeout . 's)');
      return $data;
    }

    protected function writeApiCache($filename, $data, $cache_dir) {
      Utility::diagnostic('file_put_contents()', strlen($data) . ' to ' . $cache_dir . $filename);
      return file_put_contents(dirname($_SERVER['SCRIPT_FILENAME']) . $cache_dir . $filename, $data);
    }

    /******************************************************************************/

    function attribRarityPoints() {
      if (array_key_exists($this->getRarity(), self::RARITY_POINTS)) {
        return self::RARITY_POINTS[$this->getRarity()];
      }
      return false;
    }

    /******************************************************************************/

    function markupColors($colors) {
      $markup = '';
      if (!is_array($colors)) {
        preg_match_all('/[a-z0-9]/i', $colors, $colors);
        $colors = $colors[0];
      }
      foreach ($colors as $color) {
        $markup .= '<span class="color ' . strtolower($color) . '">' . $color . '</span>';
      }
      return $markup;
    }

    function markupAsImage($part = '', $show_quantity = true) {
      $markup = '';
      if ($this->isPart($part)) {
        $markup .= '<a href="' . $this->getInfoUrl('card') . '" target="info">';
        $markup .= '<img src="' . $this->getImageUrl() . '"';
        $markup .= ' alt="' . $this->getNameAndSubname() . ' (' . $this->getSetName() . ')"';
        $markup .= '></a>';
        if ($show_quantity && $this->getQuantity() > 1) {
          $markup .= '<span class="quantity">&times;' . $this->getQuantity() . '</span>';
        }
        $markup = '<div class="card ' . $this->getPart() . '">' . $markup . '</div>';
      }
      return $markup;
    }
  }

  /**************************************************************************************************/

  class MtgCard extends Card {
    protected $cmc;
    protected $formats;

    const VALID_PARTS = array('deck', 'sideboard', 'commander', 'companion');

    const RARITY_POINTS = array(
      'common' => 176,
      'uncommon' => 48,
      'rare' => 14,
      'mythic' => 2,
      'special' => 1
    );

    function __construct($name, $quantity = 1, $part = '', $set_code = '', $favorite = 0) {
      parent::__construct($name, $quantity, $part, $set_code, $favorite);
      $this->setCmc('');
      $this->setFormats('');
      if ($this->setPropertiesFromApi('/data/scryfall/', (30 * 24 * 60 * 60)) === false) {
        // If initial API lookup failed, drop set_code and try again with only the generic card name
        if (!empty($this->getSetCode())) {
          $this->setSetCode('');
          $this->setPropertiesFromApi('/data/scryfall/', (30 * 24 * 60 * 60));
        }
      };
    }

    /******************************************************************************/

    protected function setTypeAndSubtype($type_subtype) {
      $tokens = preg_split('/[^\w\s]+/', $type_subtype, 0);
      $this->setTypes($tokens[0]);
      $this->setSubtypes(count($tokens) > 1 ? $tokens[1] : '');
      return true;
    }

    protected function setColors($colors = '') {
      if (empty($colors)) {
        $colors = 'C';
      }
      return parent::setColors($colors);
    }

    protected function setCmc($cmc, $land = false) {
      $cmc = trim($cmc);
      $this->cmc = (is_numeric($cmc) && (floor($cmc) && !$land == $cmc) ? abs($cmc) : null);
      return true;
    }

    function getCmc() {
      return $this->cmc;
    }

    protected function setFormats($formats) {
      $this->formats = (is_array($formats) ? $formats : array($formats));
      for ($k = 0; $k < count($this->formats); $k++) {
        $this->formats[$k] = ucwords(trim($this->formats[$k]));
      }
      return true;
    }

    function getFormats($delimiter = '') {
      return (empty($delimiter) ? $this->formats : implode($delimiter, $this->formats));
    }

    protected function setPart($part) {
      $part = strtolower(trim($part));
      $this->part = (in_array($part, self::VALID_PARTS) ? $part : parent::DEFAULT_PART);
    }

    function isLand() {
      foreach ($this->getTypes() as $type) {
        if (stripos($type, 'Land') !== false) {
          return true;
        }
      }
      return false;
    }

    function isBasicLand() {
      foreach ($this->getTypes() as $type) {
        if (strcasecmp($type, 'Basic Land') === 0) {
          return true;
        }
      }
      return false;
    }

    function isLegendary() {
      foreach ($this->getTypes() as $type) {
        if (stripos($type, 'Legendary') === 0) {
          return true;
        }
      }
      return false;
    }

    /******************************************************************************/

    protected function setPropertiesFromApi($cache_dir, $cache_timeout) {
      $json_filename = $this->getNameAndSubname() . (empty($this->getSetCode()) ? '' : ' (' . $this->getSetCode() . ')') . '.json';
      $scryfall_json = $this->readApiCache($json_filename, $cache_dir, $cache_timeout);
      if (empty($scryfall_json)) {
        usleep(150 * 1000); // Delay per API rate limit, https://scryfall.com/docs/api#rate-limits-and-good-citizenship
        $scryfall_url = 'https://api.scryfall.com/cards/named?formats=json&fuzzy=' . urlencode($this->getName()) . (empty($this->getSetCode()) ? '' : '&set=' . urlencode($this->getSetCode()));
        Utility::diagnostic('$scryfall_url', $scryfall_url);
        $http_context = stream_context_create(array('http' => array('header' => "User-Agent: DecklistViewer/0.1.24Q3;\nAccept: application/json;q=0.9,*/*;q=0.8;\n")));
        $scryfall_json = file_get_contents($scryfall_url, false, $http_context);
        if (empty($scryfall_json)) {
          return false;
        }
        $this->writeApiCache($json_filename, $scryfall_json, $cache_dir);
      }
      $scryfall_json = json_decode($scryfall_json);
      $this->setName(isset($scryfall_json->name) ? $scryfall_json->name : '');
      $this->setSetCode(isset($scryfall_json->set) ? $scryfall_json->set : '');
      $this->setSetName(isset($scryfall_json->set_name) ? $scryfall_json->set_name : '');
      $this->setPower(isset($scryfall_json->power) ? $scryfall_json->power : '');
      $this->setToughness(isset($scryfall_json->toughness) ? $scryfall_json->toughness : '');
      $this->setRulesText(isset($scryfall_json->oracle_text) ? $scryfall_json->oracle_text : '');
      $this->setCollectorNumber(isset($scryfall_json->collector_number) ? $scryfall_json->collector_number : '');
      $this->setTypeAndSubtype(isset($scryfall_json->type_line) ? $scryfall_json->type_line : '');
      $this->setColors(isset($scryfall_json->color_identity) ? $scryfall_json->color_identity : '');
      $this->setCost(isset($scryfall_json->mana_cost) ? $scryfall_json->mana_cost : '');
      $this->setCmc((isset($scryfall_json->cmc) ? $scryfall_json->cmc : ''), $this->isLand());
      $this->setRarity(isset($scryfall_json->rarity) ? $scryfall_json->rarity : '');
      $images = array();
      if (!isset($scryfall_json->image_uris) && is_array($scryfall_json->card_faces)) {
        foreach ($scryfall_json->card_faces as $card_face) {
          $images[] = $card_face->image_uris->border_crop;
        }
      } else {
        $images[] = $scryfall_json->image_uris->border_crop;
      }
      $this->setImageUrls($images);
      $formats = array();
      foreach ($scryfall_json->legalities as $legalityKey => $legalityVal) {
        if ($legalityVal == 'legal') {
          $formats[] = $legalityKey;
        }
      }
      sort($formats);
      $this->setFormats($formats);
      $this->setInfoUrl((isset($scryfall_json->related_uris->gatherer) ? $scryfall_json->related_uris->gatherer : ''), 'card');
      if (empty($this->getInfoUrl('card'))) {
        $this->setInfoUrl((isset($scryfall_json->scryfall_uri) ? $scryfall_json->scryfall_uri : ''), 'card');
      }
      $this->setInfoUrl((isset($scryfall_json->scryfall_set_uri) ? $scryfall_json->scryfall_set_uri : ''), 'set');
      $this->setPrice(isset($scryfall_json->prices->usd) ? $scryfall_json->prices->usd : null);
      return true;
    }

    /******************************************************************************/

    function attribRarityPoints() {
      if (array_key_exists($this->getRarity(), self::RARITY_POINTS)) {
        return self::RARITY_POINTS[$this->getRarity()];
      }
      return false;
    }

    /******************************************************************************/

    function markupColorSymbols($colors) {
      $markup = '';
      if (!is_array($colors)) {
        preg_match_all('/[a-z0-9]/i', $colors, $colors);
        $colors = $colors[0];
      }
      foreach ($colors as $color) {
        $markup .= '<span class="ms ms-' . strtolower($color) . '">' . $color . '</span>';
      }
      return $markup;
    }

    function markupAsTableRow($part = '') {
      $markup = '';
      if ($this->isPart($part)) {
        $markup .= '<td class="quantity">' . $this->getQuantity() . '</td>';
        $markup .= '<td class="name"><a text="' . $this->getRulesText() . '" href="' . $this->getInfoUrl('card') . '" target="info">' . $this->getName() . '</a></td>';
        $markup .= '<td class="type">' . $this->getTypes(' ') . (empty($this->getSubtypes()) ? '' : ' &ndash; ' . $this->getSubtypes(' ')) . '</td>';
        $markup .= '<td class="colors">' . $this->markupColorSymbols($this->getColors()) . '</td>';
        $markup .= '<td class="cost">' . $this->markupColorSymbols($this->getCost()) . '</td>';
        $markup .= '<td class="cmc">' . $this->getCmc() . '</td>';
        $markup .= '<td class="rarity">' . ucwords($this->getRarity()) . '</td>';
        $markup .= '<td class="set"><a title="' . $this->getSetName() . '" href="' . $this->getInfoUrl('set') . '" target="info">' . $this->getSetCode() . '</a></td>';
        $markup .= '<td class="price">$' . $this->getPrice() . '</td>';
        $markup .= '<td class="part">' . $this->getPart() . '</td>';
        $markup = '<tr class="' . strtolower($this->getPart()) . '">' . $markup . '</tr>';
      }
      return $markup;
    }
  }

  /**************************************************************************************************/

  class LorcanaCard extends Card {
    protected $lore;

    const RARITY_POINTS = array(
      'common' => 12,
      'uncommon' => 9,
      'rare' => 8,
      'super rare' => 3,
      'legendary' => 2,
      'enchanted' => 2,
      'promo' => 1
    );

    function __construct($name, $quantity = 1, $part = '', $set_code = '', $favorite = 0) {
      parent::__construct($name, $quantity, $part, $set_code, $favorite);
      $this->setLore(null);
      if ($this->setPropertiesFromApi('/data/lorcast/', (30 * 24 * 60 * 60)) === false) {
        // If initial API lookup failed, drop set_code and try again with only the generic card name
        if (!empty($this->getSetCode())) {
          $this->setSetCode('');
          $this->setPropertiesFromApi('/data/lorcast/', (30 * 24 * 60 * 60));
        }
      };
    }

    /******************************************************************************/

    protected function setNameAndVersion($name) {
      return $this->setNameAndSubname($name, ' - ');
    }

    function getNameAndVersion($hyphenated = true) {
      return $this->getNameAndSubname(($hyphenated ? ' - ' : ' '));
    }

    protected function setVersion($version) {
      return $this->setSubname($version);
    }

    function getVersion() {
      return $this->getSubname();
    }

    protected function setClassifications($classifications) {
      return $this->setSubtypes($classifications);
    }

    function getClassification() {
      return $this->getSubtypes(', ');
    }

    protected function setInk($ink) {
      return $this->setColors($ink);
    }

    function getInk() {
      return $this->getColors();
    }

    protected function setInkwell($inkwell) {
      return $this->setCabbage($inkwell);
    }

    function getInkwell() {
      return $this->getCabbage();
    }

    protected function setStrength($strength) {
      return $this->setPower($strength);
    }

    function getStrength() {
      return $this->getPower();
    }

    protected function setWillpower($willpower) {
      return $this->getWillpower($willpower);
    }

    function getWillpower() {
      return $this->getToughness();
    }

    protected function setLore($lore) {
      $lore = trim($lore);
      if (is_numeric($lore)) {
        $lore = abs(round($lore));
      } else {
        $lore = '';
      }
      $this->lore = $lore;
      return (!empty($lore));
    }

    function getLore() {
      return $this->lore;
    }

    /******************************************************************************/

    protected function setPropertiesFromApi($cache_dir, $cache_timeout) {
      $json_filename = $this->getNameAndVersion() . (empty($this->getSetCode()) ? '' : ' (' . $this->getSetCode() . ')') . '.json';
      $lorcast_json = $this->readApiCache($json_filename, $cache_dir, $cache_timeout);
      if (empty($lorcast_json)) {
        usleep(150 * 1000); // Delay per API rate limit, https://lorcast.com/docs/api
        $lorcast_url = 'https://api.lorcast.com/v0/cards/search?q=' . urlencode($this->getNameAndVersion(false)) . (empty($this->getSetCode()) ? '' : '+set:' . urlencode($this->getSetCode()));
        Utility::diagnostic('$lorcast_url', $lorcast_url);
        $http_context = stream_context_create(array('http' => array('header' => "User-Agent: LorcanaDecklistViewer/0.2;\nAccept: application/json;q=0.9,*/*;q=0.8;\n")));
        $lorcast_json = file_get_contents($lorcast_url, false, $http_context);
        if (empty($lorcast_json)) {
          return false;
        }
        $this->writeApiCache($json_filename, $lorcast_json, $cache_dir);
      }
      $lorcast_json = json_decode($lorcast_json);
      $lorcast_json = array_shift($lorcast_json->results);
      $this->setName(isset($lorcast_json->name) ? $lorcast_json->name : '');
      $this->setTypes(isset($lorcast_json->type) ? $lorcast_json->type : '');
      $this->setClassifications(isset($lorcast_json->classifications) ? $lorcast_json->classifications : '');
      $this->setInk(isset($lorcast_json->ink) ? $lorcast_json->ink : '');
      $this->setCost(isset($lorcast_json->cost) ? $lorcast_json->cost : '');
      $this->setInkwell(isset($lorcast_json->inkwell) ? $lorcast_json->inkwell : '');
      $this->setStrength(isset($lorcast_json->strength) ? $lorcast_json->strength : '');
      $this->setWillpower(isset($lorcast_json->willpower) ? $lorcast_json->willpower : '');
      $this->setLore(isset($lorcast_json->lore) ? $lorcast_json->lore : '');
      $this->setRulesText(isset($lorcast_json->text) ? $lorcast_json->text : '');
      $this->setSetCode(isset($lorcast_json->set->code) ? $lorcast_json->set->code : '');
      $this->setSetName(isset($lorcast_json->set->name) ? $lorcast_json->set->name : '');
      $this->setCollectorNumber(isset($lorcast_json->collector_number) ? $lorcast_json->collector_number : '');
      $this->setRarity(isset($lorcast_json->rarity) ? $lorcast_json->rarity : '');
      $this->setImageUrls(isset($lorcast_json->image_uris->digital->normal) ? $lorcast_json->image_uris->digital->normal : '');
      $this->setInfoUrl('https://lorcast.com/sets/' . $this->getSetCode(), 'set');
      $this->setInfoUrl('https://lorcast.com/cards/' . $this->getSetCode() . '/' . $this->getCollectorNumber() . '/' . str_replace(' ', '-', strtolower($this->getNameAndVersion(false))), 'card');
      $this->setPrice(isset($lorcast_json->prices->usd) ? $lorcast_json->prices->usd : null);
      return true;
    }

    /******************************************************************************/

    function attribRarityPoints() {
      if (array_key_exists($this->getRarity(), self::RARITY_POINTS)) {
        return self::RARITY_POINTS[$this->getRarity()];
      }
      return false;
    }

    /******************************************************************************/

    function markupInkwell() {
      $markup = ($this->getInkwell() ? '&check;' : '');
      return $markup;
    }

    function markupLore() {
      $markup = array();
      for ($k = 0; $k < $this->getLore(); $k++) {
        $markup[] = '&loz;';
      }
      $markup = implode('&thinsp;', $markup);
      return $markup;
    }

    function markupAsTableRow($part = '') {
      $markup = '';
      if ($this->isPart($part)) {
        $markup .= '<td class="quantity">' . $this->getQuantity() . '</td>';
        $markup .= '<td class="name"><a text="' . $this->getRulesText() . '" href="' . $this->getInfoUrl('card') . '" target="info">' . $this->getNameAndSubname() . '</a></td>';
        $markup .= '<td class="types">' . $this->getTypes(' ') . (empty($this->getSubtypes()) ? '' : ' &ndash; ' . $this->getSubtypes(' ')) . '</td>';
        $markup .= '<td class="colors">' . $this->markupColors($this->getColors()) . '</td>';
        $markup .= '<td class="cost">' . $this->getCost() . '</td>';
        $markup .= '<td class="inkwell">' . $this->markupInkwell() . '</td>';
        $markup .= '<td class="lore">' . $this->markupLore() . '</td>';
        $markup .= '<td class="rarity">' . ucwords($this->getRarity()) . '</td>';
        $markup .= '<td class="set"><a href="' . $this->getInfoUrl('set') . '" target="info">' . $this->getSetName() . '</a></td>';
        $markup .= '<td class="price">$' . $this->getPrice() . '</td>';
        $markup .= '<td class="part">' . $this->getPart() . '</td>';
        $markup = '<tr class="' . strtolower($this->getPart()) . '">' . $markup . '</tr>';
      }
      return $markup;
    }
  }

  /**************************************************************************************************/
}
