<?php $href = $this->url(array('action' => 'browse'), 'offers_general', true); ?>
<script type="text/javascript">
  offers_manager.url = "<?php echo $href; ?>";
</script>

<?php
if ($this->pageCount > 1): ?>
  <ul class="paginationControl offers_pagination">
    <?php if (isset($this->next)): ?>
    <li>
      <a href="<?php echo $href .'/'. $this->next; ?>" onclick="offers_manager.setPage(<?php echo $this->next;?>,<?php echo $this->pageCount;?>); return false;">
        <div class="offer_viewmoreButton" id="offer_viewmoreButton" style="text-align: center">
          <?php echo $this->translate('View more');?>
          </div>
        <div class="offer_navigation_loader_new hidden" id="offer_navigation_loader_new"  style="text-align: center">
          <?php echo $this->htmlImage($this->baseUrl().'/application/modules/Offers/externals/images/loader.gif'); ?>
        </div>
      </a>
    </li>
    <?php endif; ?>
  </ul>
<?php endif; ?>
