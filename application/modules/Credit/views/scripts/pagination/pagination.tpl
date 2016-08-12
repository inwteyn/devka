<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: pagination.tpl  30.01.12 19:38 TeaJay $
 * @author     Taalay
 */
?>

<script type="text/javascript">
  var pagination = function (page, id, cl) {
    var main = $$(cl)[0];
    var loader = main.getElement('.credit_loader_browse');
    var parent = loader.getParent();

    loader.removeClass('hidden');

    if (parent) {
      parent.setStyle('opacity', 0.2);
    }
    new Request.HTML({
      url: en4.core.baseUrl + 'widget/index/content_id/' + id,
      data: {
        format: 'html',
        page: page
      },
      onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
        var el = $$(cl);
        var tElement = new Element('div', {'html': responseHTML});
        el[0].innerHTML = tElement.getElement(cl).innerHTML;
        loader.addClass('hidden');
        if (parent) {
          parent.setStyle('opacity', 1);
        }
      }
    }).post();
  };
</script>

<?php if ($this->pageCount > 1): ?>
  <ul class="paginationControl">
    <?php if (isset($this->previous)): ?>
      <li>
        <a href="javascript:void(0)" onclick="pagination(<?php echo $this->previous;?>, '<?php echo $this->identity?>', '<?php echo $this->class?>')"><?php echo $this->translate('&#171; Previous');?></a>
      </li>
    <?php endif; ?>

    <?php foreach ($this->pagesInRange as $page): ?>
      <li class="<?php if ($page == $this->current): ?>selected<?php endif; ?>" >
        <a onclick="pagination(<?php echo $page;?>, '<?php echo $this->identity?>', '<?php echo $this->class?>')" href="javascript:void(0)"><?php echo $this->locale()->toNumber($page); ?></a>
      </li>
    <?php endforeach; ?>

    <?php if (isset($this->next)): ?>
      <li>
        <a href="javascript:void(0)" onclick="pagination(<?php echo $this->next;?>, '<?php echo $this->identity?>', '<?php echo $this->class?>')"><?php echo $this->translate('Next &#187;');?></a>
      </li>
    <?php endif; ?>
  </ul>
<?php endif; ?>