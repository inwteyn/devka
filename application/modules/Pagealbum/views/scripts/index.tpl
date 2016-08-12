<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagealbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-09-06 17:53 idris $
 * @author     Idris
 */
?>
<?php if( $this->albums->getTotalItemCount() > 0 ): ?>
  <ul class="pagealbum_thumbs">
    <?php foreach( $this->albums as $album ): ?>
        <li>
            <div class="thumbs_photo">
                <a class="thumbs_photo" href="<?php echo $album->getHref(); ?>" onclick="page_album.view(<?php echo $album->getIdentity(); ?>); return false;" >
                    <span style="background-image: url(<?php echo $album->getPhotoUrl('thumb'); ?>);"></span>
                </a>

                <div class="caption">
                    <div class="content">
                    </div>
                </div>

                <div class="hover-caption">
                    <div class="content">
                        <div class="title">
                            <?php if ($album->getType() == 'pagealbum') { ?>
                                <?php echo $this->translate('On'); ?>
                                <?php echo $this->htmlLink($album->getPage()->getHref(), $album->getPage(),array('class' => 'thumbs_author')) ?>
                            <?php } else { ?>
                                <?php echo $this->translate('By'); ?>
                                <?php echo $this->htmlLink($album->getOwner()->getHref(), $album->getOwner()->getTitle(), array('class' => 'thumbs_author')) ?>
                            <?php } ?>
                        </div>
                        <div class="info">
                            <div class="photo-count">
                                <i class="hei hei-picture"></i>
                                <?php echo $album->count(); ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

    <span class="thumbs_title">
      <?php echo $this->htmlLink($album, $this->string()->chunk($this->string()->truncate($album->getTitle(), 45), 10)) ?>
    </span>
        </li>
    <?php endforeach; ?>
  </ul>

  <?php if( $this->albums->count() > 1 ) : ?>
    <br />
    <?php echo $this->paginationControl($this->albums, null, array("pagination.tpl","pagealbum"), array(
      'page' => $this->pageObject
    ));?>
  <?php endif; ?>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('Nobody has created an album yet.');?>
      <?php if ($this->isAllowedPost): // @todo check if user is allowed to create an album ?>
        <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="javascript:void(0)" onClick="page_album.create();">', '</a>'); ?>
      <?php endif; ?>
    </span>
  </div>
<?php endif; ?>