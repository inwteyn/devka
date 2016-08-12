<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Survey
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-07-02 17:53 idris $
 * @author     Idris
 */
?>

<h2><?php echo $this->translate('Survey Plugin'); ?></h2>

<?php if( count($this->navigation) ): ?>
<div class='tabs'>
    <?php
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>

<div class='clear'>
  <div class='settings'>
    <form class="global_form">
      <div>
      <h3><?php echo $this->translate('Survey Categories'); ?></h3>
      <p class="description">
        <?php echo $this->translate('You can categorize surveys as you want.'); ?>
      </p>

      <table class='admin_table'>
        <thead>

          <tr>
            <th><?php echo $this->translate('survey_Category Name'); ?></th>
            <th><?php echo $this->translate('Options'); ?></th>
          </tr>

        </thead>
        <tbody>
          <?php foreach ($this->categories as $category): ?>

          <tr>
            <td><?php echo $category->category_name?></td>
            <td>
              <?php echo $this->htmlLink(
                array('route' => 'admin_default',
                  'module' => 'survey',
                  'controller' => 'categories',
                  'action' => 'edit-category',
                  'id' =>$category->category_id),
                $this->translate('edit'), array(
                'class' => 'smoothbox',
              )) ?>
              |
              <?php echo $this->htmlLink(
                array('route' => 'admin_default',
                  'module' => 'survey',
                  'controller' => 'categories',
                  'action' => 'delete-category',
                  'id' =>$category->category_id),
                $this->translate('delete'),
                array(
                'class' => 'smoothbox',
              )) ?>
            </td>
          </tr>
          
          <?php endforeach; ?>
        </tbody>
      </table>
      <br/>
      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'survey', 'controller' => 'categories', 'action' => 'add-category'), $this->translate('survey_Add New Category'), array(
      'class' => 'smoothbox buttonlink',
      'style' => 'background-image: url(application/modules/Core/externals/images/admin/new_category.png);')) ?>
      </div>
    </form>
  </div>
</div>
