<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagealbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2012-02-13 17:46 ulan T $
 * @author     Ulan T
 */
?>

<script type="text/javascript">
  en4.core.runonce.add(function(){

  <?php if( !$this->renderOne ): ?>
    var anchor = $('profile_pagealbums').getParent();
    $('profile_pagealbums_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
    $('profile_pagealbums_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

    $('profile_pagealbums_previous').removeEvents('click').addEvent('click', function(){
      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
        }
      }), {
        'element' : anchor
      })
    });

    $('profile_pagealbums_next').removeEvents('click').addEvent('click', function(){
      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
        }
      }), {
        'element' : anchor
      })
    });
    <?php endif; ?>
  });
</script>

<div id="profile_pagealbums" class="profile_pagealbums">
  <?php foreach( $this->paginator as $item ): ?>
  <?php
    if( $item['type'] == 'page')
      $album = Engine_Api::_()->getItem('pagealbum', $item['album_id']);
    else
      $album = Engine_Api::_()->getItem('album', $item['album_id']);
  ?>

  <div class="pagealbum_manage_item">
    <div class="pagealbum_manage_photo">
      <a class="thumb_photo"  href="<?php echo $album->getHref();?>">
        <span style="background-image: url(<?php echo $album->getPhotoUrl('thumb.normal'); ?>);"></span>
      </a>
    </div>

    <div class="pagealbum_manage_info">
      <?php echo $this->htmlLink($album, $this->string()->chunk($this->string()->truncate($album->getTitle(), 45), 10)) ?>
      <div class="info">
        <?php echo $this->translate('By');?>
        <?php echo $this->htmlLink($album->getOwner()->getHref(), $album->getOwner()->getTitle()); ?>
        <?php if( $item['type'] == 'page') : ?>
        <br />
        <?php echo $this->translate('On page ') ?>
        <?php echo $this->htmlLink($album->getPage()->getHref(), $album->getPage()->getTitle()) ?>
        <?php endif;?>
        <br/>
        <?php echo $this->translate(array('%s photo', '%s photos', $album->count()),
        $this->locale()->toNumber($album->count())) ?>
        -
        <?php echo $album->view_count;?>
        <?php echo $this->translate('views')?>
      </div>
    </div>

  </div>
  <?php endforeach;?>
</div>

<div>
  <div id="profile_pagealbums_previous" class="paginator_previous">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
    'onclick' => '',
    'class' => 'buttonlink icon_previous'
  )); ?>
  </div>
  <div id="profile_pagealbums_next" class="paginator_next">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
    'onclick' => '',
    'class' => 'buttonlink_right icon_next'
  )); ?>
  </div>
</div>