<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagemusic
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2012-02-20 15:35 Ulan T $
 * @author     Ulan T
 */
?>
<?php

$this->headScript()
  ->appendFile($this->layout()->staticBaseUrl . 'externals/swfobject/swfobject.js')
  ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Pagemusic/externals/scripts/music.js')
  ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Pagemusic/externals/standalone/audio-player.js');
$modulesTbl = Engine_Api::_()->getDbTable('modules', 'core');
$coreItem = $modulesTbl->getModule('core')->toArray();

if(version_compare($coreItem['version'], '4.8.10')>=0){
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/flowplayer/flowplayer-3.2.18.min.js');

}else{
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/flowplayer/flashembed-1.0.1.pack.js');
}
?>
<script type="text/javascript">
  AudioPlayer.setup("<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Pagemusic/externals/standalone/player.swf", {
    width: 290,
    initialvolume: 100,
    transparentpagebg: "yes",
    leftbg: "339BCB",
    lefticon: "FFFFFF",
    righticon: "FFFFFF",
    righticonhover: "000000",
    voltrack: "FFFFFF",
    volslider: "000000",
    rightbg: "339BCB",
    rightbghover: "339BCB",
    loader: "339BCB"
  });
</script>

<script type="text/javascript">
  en4.core.runonce.add(function () {

    <?php if( !$this->renderOne ): ?>
    var anchor = $('profile_pagemusic').getParent();
    $('profile_pagemusic_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
    $('profile_pagemusic_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

    $('profile_pagemusic_previous').removeEvents('click').addEvent('click', function () {
      en4.core.request.send(new Request.HTML({
        url: en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        data: {
          format: 'html',
          subject: en4.core.subject.guid,
          page: <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
        }
      }), {
        'element': anchor
      })
    });

    $('profile_pagemusic_next').removeEvents('click').addEvent('click', function () {
      en4.core.request.send(new Request.HTML({
        url: en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        data: {
          format: 'html',
          subject: en4.core.subject.guid,
          page: <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
        }
      }), {
        'element': anchor
      })
    });
    <?php endif; ?>
  });
</script>

<div id="profile_pagemusic">
  <?php foreach ($this->paginator as $item) : ?>
    <?php
    if ($item['type'] == 'music')
      $playlist = Engine_Api::_()->getItem('music_playlist', $item['playlist_id']);
    else
      $playlist = Engine_Api::_()->getItem('playlist', $item['playlist_id']);
    ?>
    <div class="page-music-view-songs">
      <table cellpadding="0" cellspacing="0" class="tracklist">
        <thead>
        <tr class="thead" id="my_playlist_<?php echo $item['type'] . '_' . $item['playlist_id']; ?>">
          <td class="number" width="1%">#</td>
          <td class="title">
            <?php echo $this->htmlLink($playlist->getHref(), $playlist->getTitle()); ?>
          </td>
          <td width="300px">Player</td>
        </tr>
        </thead>
        <tbody>
        <?php $number = 0; ?>
        <?php foreach ($playlist->getSongs() as $song) : ?>
          <?php $number++; ?>
          <tr class="song">
            <td class="number" width="1%"><span class="misc_info"><?php echo $number; ?>.</span></td>
            <td class="title"><?php echo $song->getTitle(); ?></td>
            <td class="listen" width="300px" align="right">
              <div id="song_wrapper_<?php echo $item['type'] . '_' . $song->getIdentity(); ?>"
                   style="margin-bottom: -2px">
                <div id="song_<?php echo $item['type'] . '_' . $song->getIdentity(); ?>"></div>
              </div>

              <script type="text/javascript">
                en4.core.runonce.add(function () {
                  AudioPlayer.embed("song_<?php echo $item['type'].'_'.$song->getIdentity(); ?>", {
                    soundFile: "<?php echo $this->storage->get($song->file_id)->map(); ?>",
                    titles: "<?php echo $song->getTitle(); ?>"
                  });
                });
              </script>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>

      </table>
    </div>
  <?php endforeach; ?>
</div>

<div>
  <div id="profile_pagemusic_previous" class="paginator_previous">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
      'onclick' => '',
      'class' => 'buttonlink icon_previous'
    )); ?>
  </div>
  <div id="profile_pagemusic_next" class="paginator_next">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
      'onclick' => '',
      'class' => 'buttonlink_right icon_next'
    )); ?>
  </div>
</div>
<object type="application/x-shockwave-flash" name="audioplayer_1" style="outline: none"
        data="http://wpaudioplayer.com/wp-content/plugins/audio-player/assets/player.swf?ver=2.0.4.6" width="290"
        height="24" id="audioplayer_1">
  <param name="bgcolor" value="#FFFFFF">
  <param name="wmode" value="transparent">
  <param name="menu" value="false">
  <param name="flashvars"
         value="animation=yes&amp;encode=yes&amp;initialvolume=60&amp;remaining=no&amp;noinfo=no&amp;buffer=5&amp;checkpolicy=no&amp;rtl=no&amp;bg=E5E5E5&amp;text=333333&amp;leftbg=CCCCCC&amp;lefticon=333333&amp;volslider=666666&amp;voltrack=FFFFFF&amp;rightbg=B4B4B4&amp;rightbghover=999999&amp;righticon=333333&amp;righticonhover=FFFFFF&amp;track=FFFFFF&amp;loader=009900&amp;border=CCCCCC&amp;tracker=DDDDDD&amp;skip=666666&amp;soundFile=aHR0cDovL3dwYXVkaW9wbGF5ZXIuY29tL3dwLWNvbnRlbnQvdXBsb2Fkcy8yMDA4LzA2L2FkYnVzdGVyczEubXAzA&amp;playerID=audioplayer_1">
</object>