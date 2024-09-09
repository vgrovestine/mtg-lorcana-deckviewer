<!DOCTYPE html>
<html>
<?php
/*
**
** Copyright 2024 Vincent Grovestine (vincent@grovestine.com) -- All rights reserved.
**
*/

use DeckViewer\Utility;

include('./includes/deckviewer.php');
$mtg_box = new DeckViewer\MtgBox('./decks/mtg/');
$lorcana_box = new DeckViewer\LorcanaBox('./decks/lorcana/');
switch (basename($_SERVER['SCRIPT_FILENAME'])) {
  case 'lorcana.php':
    $deck = $lorcana_box->getDeckFromUrl();
    break;
  case 'mtg.php':
  default:
    $deck = $mtg_box->getDeckFromUrl();
    break;
}
$title = (empty($deck) ? 'Deck Viewer' : $deck->getName());
?>

<head>
  <title><?= $title; ?></title>
  <link rel="stylesheet" href="includes/deckviewer.css">
</head>

<body>
  <div id="deckviewer">
    <header>
      <h1><?= $title; ?></h1>
    </header>

    <?php if (!empty($deck)): ?>
      <div class="flex_wrapper" id="body_wrapper">

        <aside>

          <section class="deck_info">
            <h2 class="invisible">Deck Information</h2>
            <?php
            $favorite_cards = $deck->getFavoriteCards();
            for ($k = 0; $k < count($favorite_cards) && $k < 2; $k++) {
              echo $favorite_cards[$k]->markupAsImage(false, false);
            }
            unset($favorite_cards);
            echo $deck->markupAttributes();
            ?>
          </section>

        </aside>

        <main>
          <section class="deck_table">
            <h2 class="invisible">Card Details</h2>
            <?php
            foreach ($deck->getParts() as $part) {
              echo $deck->markupAsTable(count($deck->getParts()) > 1 ? $part : '');
            }
            ?>
          </section>

          <section class="deck_images">
            <h2 class="invisible">Card Images</h2>
            <div class="flex_wrapper">
              <?= $deck->markupAsImages('', (count($deck->getParts()) > 1)); ?>
              <?php
              for ($h = 0; $h < 8; $h++) { // Hack to keep cards on last flex row same size as others
                echo '<div class="card hidden"></div>';
              }
              ?>
            </div>
          </section>

          <h2 class="invisible">Plain-text Decklist</h2>
          <section class="deck_text"><?= $deck->prepareDecklistAsText(); ?></section>
        </main>

      </div>
    <?php endif; ?>

    <footer>
      <h2 class="invisible">Browse Decks</h2>
      <div class="flex_wrapper">
        <div class="pseudocolumn">
          <h3>Magic: the Gathering</h3>
          <?= $mtg_box->markupNavigation('mtg.php'); ?>
        </div>
        <div class="pseudocolumn">
          <h3>Lorcana</h3>
          <?= $lorcana_box->markupNavigation('lorcana.php'); ?>
        </div>
      </div>
    </footer>
  </div>
</body>

</html>