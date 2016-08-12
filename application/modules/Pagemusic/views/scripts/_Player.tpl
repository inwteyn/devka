<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: _Player.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Steve
 */
?>
<?php
$playlist = $this->playlist;
$songs = (isset($this->songs) && !empty($this->songs))
  ? $this->songs
  : $playlist->getSongs();

$storage = Engine_Api::_()->storage();
// this forces every playlist to have a unique ID, so that a playlist can be displayed twice on the same page
$random = '';
for ($i = 0; $i < 6; $i++) {
  $d = rand(1, 30) % 2;
  $random .= ($d ? chr(rand(65, 90)) : chr(rand(48, 57)));
}
?>


<?php if (count($songs)) : ?>
  <script>
    // Setup the player to autoplay the next track
    var a = audiojs.createAll({
      trackEnded: function () {
        $('player-next').click();
        /*var next = $$('.playing')[0].getNext();

         if (!next) next = $$('.music_item')[0];

         $$('.music_item').each(function (el) {
         if (el.hasClass('playing')) {
         el.removeClass('playing');
         }
         });
         next.addClass('playing');//.siblings().removeClass('playing');

         audio.load($$('.music_item a')[0].get('data-src'));
         audio.play();*/
      }
    });

    // Load in the first track
    var audio = a[0];
    first = $$('.music_item a')[0].get('data-src');
    $$('.music_item')[0].addClass('playing');
    audio.load(first);

    $$('.music_item a').addEvent('click', function (e) {
      e.preventDefault();

      if (e.target.hasClass('smoothbox')) {
        return;
      }
      $$('.music_item').each(function (el) {
        if (el.hasClass('playing')) {
          el.removeClass('playing');
        }
      });

      $(this).getParent().addClass('playing');//.siblings().removeClass('playing');
      audio.load($(this).get('data-src'));
      audio.play();
    });

    $('player-prev').addEvent('click', function () {
      var mItems = $$('li.music_item');
      var items = $$('li.playing');
      if (items.length <= 0) return;
      var prev = items[0].getPrevious();
      if (!prev) {
        prev = mItems[mItems.length - 1];
      }
      prev.getElement('a').click();
    });
    $('player-next').addEvent('click', function () {
      var mItems = $$('li.music_item');
      var items = $$('li.playing');
      if (items.length <= 0) return;
      var next = items[0].getNext();
      if (!next) {
        next = mItems[0];
      }
      next.getElement('a').click();
    });
    $('player-play').addEvent('click', function () {
      audio.play();
    });
    $('player-pause').addEvent('click', function () {
      audio.pause();
    });
  </script>

  <div id="wrapper">
    <audio preload></audio>
    <ol>
      <?php foreach ($songs as $song): ?>
        <li class="music_item">
          <a href="#" data-src="<?php echo $storage->get($song->file_id)->map(); ?>">
            <?php echo $song->getTitle(); ?>
          </a>

          <div class="pagemusic-item-share">
            <a class="smoothbox music_item_share"
               href="<?php echo $this->url(
                 array('module' => 'activity',
                   'controller' => 'index',
                   'action' => 'share',
                   'type' => $song->getType(),
                   'id' => $song->getIdentity(),
                   'format' => 'smoothbox'), 'default', true);
               ?>">
              <?php echo $this->translate('Share'); ?>
            </a>
          </div>


        </li>
      <?php endforeach; ?>
    </ol>
  </div>
<?php else : ?>
  <br/>
  <div class="tip"><span><?php echo $this->translate('pagemusic_NO_SONGS_TEXT'); ?></span></div>
<?php endif; ?>

