<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2012-07-25 12:44 ratbek $
 * @author     Ratbek
 */
?>

<h2><?php echo $this->translate("OFFERS_Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

  <div class='clear'>
    <div class='settings'>
    <form class="global_form">
      <div>
        <h3> <?php echo $this->translate("OFFERS_Categories") ?> </h3>
        <p class="description">
          <?php echo $this->translate("OFFERS_CATEGORIES_DESCRIPTION") ?>
        </p>
          <?php if(count($this->categories)>0):?>

         <table class='admin_table'>
          <thead>
            <tr>
              <th><?php echo $this->translate("Category Name") ?></th>
<?php //              <th># of Times Used</th>?>
              <th><?php echo $this->translate("Options") ?></th>
            </tr>

          </thead>
          <tbody>
            <?php foreach ($this->categories as $category): ?>
              <tr>
                <td><?php echo $category->title?></td>
                <?php if ($category->category_id != 1): ?>
                <td>
                  <?php echo $this->htmlLink(
                          array('route' => 'admin_default', 'module' => 'offers', 'controller' => 'categories', 'action' => 'edit-category', 'id' =>$category->category_id),
                          $this->translate('edit'),
                          array('class' => 'smoothbox',
                  )) ?>
                  |
                  <?php echo $this->htmlLink(
                          array('route' => 'admin_default', 'module' => 'offers', 'controller' => 'categories', 'action' => 'delete-category', 'id' =>$category->category_id),
                          $this->translate('delete'),
                          array('class' => 'smoothbox',
                  )) ?>

                </td>
                <?php endif; ?>
              </tr>

            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else:?>
      <br/>
      <div class="tip">
      <span><?php echo $this->translate("There are currently no categories.") ?></span>
      </div>
      <?php endif;?>
        <br/>

      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'offers', 'controller' => 'categories', 'action' => 'add-category'), $this->translate('OFFERS_Add New Category'), array(
        'class' => 'smoothbox buttonlink',
        'style' => 'background-image: url(' . $this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/admin/new_category.png);')) ?>

    </div>
    </form>
    </div>
  </div>
     