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

<h2><?php echo $this->translate("Surveys Plugin"); ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<p>
  <?php echo $this->translate("List of surveys."); ?>
</p>

<br />
    <?php if( count($this->paginator) ): ?>

<table class='admin_table'>
<thead>

  <tr>
    <th class='admin_table_short'><?php echo $this->translate("ID"); ?></th>
    <th><?php echo $this->translate("survey_Title"); ?></th>
    <th><?php echo $this->translate("Owner"); ?></th>
    <th><?php echo $this->translate("Views"); ?></th>
    <th><?php echo $this->translate("Date"); ?></th>
    <th><?php echo $this->translate("Options"); ?></th>
  </tr>

</thead>
<tbody>
        <?php foreach ($this->paginator as $item): ?>

          <tr>
            <td><?php echo $item->survey_id ?></td>
            <td class='admin_table_bold'>
              <a href="<?php echo $this->url(array('user_id' => $item->user_id, 'survey_id' => $item->survey_id), 'survey_view') ?>">
                <?php echo $item->title ?>
              </a>
            </td>
            <td class='admin_table_bold'>
              <?php echo $this->htmlLink($this->item('user', $item->user_id)->getHref(), $this->item('user', $item->user_id)->getTitle(), array('target' => '_blank')) ?>
            </td>
            <td><?php echo $item->view_count ?></td>
            <td><?php echo $this->timestamp($item->creation_date); ?></td>
            <td>
              <?php if($item->approved == 0):?>
                <?php if(_ENGINE_ADMIN_NEUTER): ?>
                  <a href="javascript:void(0);" title="<?php echo $this->translate('Click to approve this survey')?>"><?php echo $this->translate("survey_approve"); ?></a>
                <?php else: ?>
		        	  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'survey', 'controller' => 'surveys', 'action' => 'approve', 'survey_id' => $item->survey_id, 'page' => $this->page), $this->translate("survey_approve"), array('title'=> $this->translate('Click to approve this survey'))) ?>
                <?php endif; ?>
		          <?php else: ?>
              <?php if(_ENGINE_ADMIN_NEUTER): ?>
                <a href="javascript:void(0);" title="<?php echo $this->translate('Click to disapprove this survey')?>"><?php echo $this->translate("survey_disapprove"); ?></a>
                <?php else: ?>
							  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'survey', 'controller' => 'surveys', 'action' => 'approve', 'survey_id' => $item->survey_id, 'page' => $this->page), $this->translate("survey_disapprove"), array('title'=> $this->translate('Click to disapprove this survey'))) ?>
                <?php endif; ?>
		          <?php endif; ?>
              |
              <?php echo $this->htmlLink(
                array('route' => 'admin_default',
                  'module' => 'survey',
                  'controller' => 'surveys',
                  'action' => 'delete',
                  'id' => $item->survey_id),
                'delete', array(
                'class' => 'smoothbox',
              )) ?>
            </td>
          </tr>

            <?php endforeach; ?>
</tbody>
</table>
<br/>

<div class='browse_nextlast'>
  <?php echo $this->paginationControl($this->paginator); ?>
</div>

<?php else:?>
  <?php echo $this->translate("There are no surveys by your members yet."); ?>
<?php endif; ?>