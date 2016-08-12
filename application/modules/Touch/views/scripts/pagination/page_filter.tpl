<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: page_filter.tpl 2011-04-26 11:18:13 ulan $
 * @author     Ulan
 */

?>

<?php
  if( !empty($this->query) && ( is_string($this->query) || is_array($this->query)) ) {
    $query = $this->query;

    if (is_array($query)) {
      unset($query['page']);
      $query = http_build_query($query);
    }
    if( $query ) $query = '?' . $query;
  } else {
    $query = '';
  }

// Add params
  $params = ( !empty($this->params) && is_array($this->params) ? $this->params : array() );
  unset($params['page']);
$filterOptions = htmlspecialchars(Zend_Json::encode($this->filterOptions));

?>

<div class="global_form_box" id="filter_form">
	<div class="form-elements">
		<table class="search"><tbody><tr>

			<td class="filter_text" valign="middle">
        <table width="100%" class="filter_text_container"><tr><td width="100%">
          <input type="text"
                 onchange="Touch.subNavFilter($(this), '<?php echo $this->filterUrl ?>', false, <?php echo $filterOptions?>)"
                 class="<?php if(Engine_String::strlen(trim($this->search)) == 0):?>filter_default_value<?php endif; ?>"
                 onblur="Touch.blur($(this), 'filter_default_value', '<?php echo $this->filter_default_value; ?>')"
                 onfocus="Touch.focus($(this), 'filter_default_value')"
                 value="<?php echo (Engine_String::strlen(trim($this->search)) == 0)? $this->filter_default_value: $this->search; ?>" id="search" name="search">
        </td><td>
          <a href="javascript:void(0);" onclick="Touch.focus($('search'), 'filter_default_value');Touch.subNavFilter($('search'), '<?php echo $this->filterUrl ?>', false, <?php echo $filterOptions?>);"></a>
        </td></table>
			</td>
			<td class="filter_submit"  valign="middle">
						<table><tr>
<?php
if($this->layout()->orientation == 'right-to-left'){
  ?>
    <td class="filter_previous" valign="middle">
        <?php if( $this->previous ): ?>
             <?php

             if (isset($this->pageUrlParams) && $this->pageUrlParams){

               $params = $this->pageUrlParams;
               $query = (isset($this->pageUrlQuery)) ? $this->pageUrlQuery : '';

               if (isset($params['route'])){
                 $route = $params['route'];
                 unset($params['route']);
               } else {
                 $route = 'default';
               }

               if (isset($params['reset'])){
                 $reset = $params['reset'];
                 unset($params['reset']);
               } else {
                 $reset = null;
               }

               $url = $this->url(array_merge($params, array('page' => $this->previous)), $route, $reset) . $query;
             } else {
               $url = $this->url(array('page' => $this->previous)) . $query;
             }

             ?>
        <a href="<?php echo $url; ?>" onclick="Touch.subNavFilter($(this), false, '<?php echo $this->search; ?>', <?php echo $filterOptions?>); return false;">
          <img src="application/modules/Touch/themes/<?php echo $this->touchActiveTheme(); ?>/images/next.png" alt="<?php echo $this->translate('Prev'); ?>"/>
        </a>
        <?php else: ?>
          <span>
              <img src="application/modules/Touch/themes/<?php echo $this->touchActiveTheme(); ?>/images/next_disabled.png" alt="<?php echo $this->translate('Prev') ?>" /></span>
        <?php endif; ?>
      </td>

    <td class="filter_next" valign="middle">
               <?php if ( $this->next): ?>
             <?php

             if (isset($this->pageUrlParams) && $this->pageUrlParams){

               $params = $this->pageUrlParams;
               $query = (isset($this->pageUrlQuery)) ? $this->pageUrlQuery : '';

               if (isset($params['route'])){
                 $route = $params['route'];
                 unset($params['route']);
               } else {
                 $route = 'default';
               }

               if (isset($params['reset'])){
                 $reset = $params['reset'];
                 unset($params['reset']);
               } else {
                 $reset = null;
               }

               $url = $this->url(array_merge($params, array('page' => $this->next)), $route, $reset) . $query;
             } else {
               $url = $this->url(array('page' => $this->next)) . $query;
             }

             ?>
          <a href="<?php echo $url; ?>" onclick="Touch.subNavFilter($(this), false, '<?php echo $this->search; ?>', <?php echo $filterOptions?>); return false">
            <img src="application/modules/Touch/themes/<?php echo $this->touchActiveTheme(); ?>/images/prev.png" alt="<?php echo $this->translate('Next'); ?>"/>
          </a>
        <?php else: ?>
          <span><img src="application/modules/Touch/themes/<?php echo $this->touchActiveTheme(); ?>/images/prev_disabled.png" alt="<?php echo $this->translate('Next') ?>"/></span>
        <?php endif; ?>
      </td>
  <?php } else {?>
							<td class="filter_previous" valign="middle">
								<?php if( $this->previous ): ?>
                <?php

                if (isset($this->pageUrlParams) && $this->pageUrlParams){

                  $params = $this->pageUrlParams;
                  $query = (isset($this->pageUrlQuery)) ? $this->pageUrlQuery : '';

                  if (isset($params['route'])){
                    $route = $params['route'];
                    unset($params['route']);
                  } else {
                    $route = 'default';
                  }

                  if (isset($params['reset'])){
                    $reset = $params['reset'];
                    unset($params['reset']);
                  } else {
                    $reset = null;
                  }

                  $url = $this->url(array_merge($params, array('page' => $this->previous)), $route, $reset) . $query;
                } else {
                  $url = $this->url(array('page' => $this->previous)) . $query;
                }

                ?>
								<a href="<?php echo $url; ?>" onclick="Touch.subNavFilter($(this), false, '<?php echo $this->search; ?>', <?php echo $filterOptions?>); return false;">
									<img src="application/modules/Touch/themes/<?php echo $this->touchActiveTheme(); ?>/images/prev.png" alt="<?php echo $this->translate('Prev'); ?>"/>
								</a>
								<?php else: ?>
									<span>
											<img src="application/modules/Touch/themes/<?php echo $this->touchActiveTheme(); ?>/images/prev_disabled.png" alt="<?php echo $this->translate('Prev') ?>" /></span>
								<?php endif; ?>
							</td>

							<td class="filter_next" valign="middle">
                  <?php if ( $this->next): ?>
                <?php

                if (isset($this->pageUrlParams) && $this->pageUrlParams){

                  $params = $this->pageUrlParams;
                  $query = (isset($this->pageUrlQuery)) ? $this->pageUrlQuery : '';

                  if (isset($params['route'])){
                    $route = $params['route'];
                    unset($params['route']);
                  } else {
                    $route = 'default';
                  }

                  if (isset($params['reset'])){
                    $reset = $params['reset'];
                    unset($params['reset']);
                  } else {
                    $reset = null;
                  }

                  $url = $this->url(array_merge($params, array('page' => $this->next)), $route, $reset) . $query;
                } else {
                  $url = $this->url(array('page' => $this->next)) . $query;
                }

                ?>
									<a href="<?php echo $url; ?>" onclick="Touch.subNavFilter($(this), false, '<?php echo $this->search; ?>', <?php echo $filterOptions?>); return false">
										<img src="application/modules/Touch/themes/<?php echo $this->touchActiveTheme(); ?>/images/next.png" alt="<?php echo $this->translate('Next'); ?>"/>
									</a>
								<?php else: ?>
									<span><img src="application/modules/Touch/themes/<?php echo $this->touchActiveTheme(); ?>/images/next_disabled.png" alt="<?php echo $this->translate('Next') ?>"/></span>
								<?php endif; ?>
							</td>
  <?php }?>
						</tr></table>
					</div>
			</td>

		</tr></tbody></table>
	</div>
</div>

<div id="filter_loading" style="display:none">
	<a class="loader"><?php echo $this->translate("Loading ..."); ?></a>
</div>