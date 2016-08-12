<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: products.tpl  17.09.11 11:57 TeaJay $
 * @author     Taalay
 */
?>
<script type="text/javascript">
  function changeImage(el) {
    var src = $(el).get('src');
    $('quick-preview').set('src', src);
  }
</script>
<div style="float: left;width:57%;">
  <?php
  $paginator = $this->product->getCollectiblesPaginator();
  $paginator->setItemCountPerPage(100);
  $cnt = $paginator->getTotalItemCount();
  ?>
  <?php if ($cnt): ?>
    <div style="text-align: center; overflow:hidden; height: 200px;">
      <img id="quick-preview" src="<?php echo $this->product->getPhotoUrl(); ?>">
    </div>

    <div style="overflow-x: scroll;">
      <table>
        <tr>
          <?php foreach ($paginator as $key => $photo): ?>
            <td>
              <div>
                <a style="text-align: left; "
                   title="<?php echo ($photo->title) ? '<b>' . $photo->title . '</b>: ' . $photo->description : ''; ?>"
                   href="javascript://">
                  <img onclick="changeImage(this);"height="100" class="photo-preview" src="<?php echo $photo->getPhotoUrl('thumb.normal'); ?>" class="thumbs">
                </a>
              </div>
            </td>
          <?php endforeach; ?>
        </tr>
      </table>
    </div>


  <?php else : ?>
    <div style="text-align: center;">
      <img width="200" src="<?php echo $this->product->getPhotoUrl('thumb.profile'); ?>">
    </div>
  <?php endif; ?>
</div>

<div style="float: right; width: 40%;">

</div>
<div style="clear: both;"></div>