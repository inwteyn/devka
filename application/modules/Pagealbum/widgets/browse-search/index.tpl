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
  //<![CDATA[
  window.addEvent('domready', function() {
    $('sort').addEvent('change', function(){
      $(this).getParent('form').submit();
    });

    $('view').addEvent('change', function(){
      $(this).getParent('form').submit();
    });

    var category_id = $('category_id');
    if( category_id != null ){
      category_id.addEvent('change', function(){
        $(this).getParent('form').submit();
      });
    }
  })
  //]]>
</script>
<?php echo $this->searchForm->render($this);?>