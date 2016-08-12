<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecontest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 19.10.13 08:20 jungar $
 * @author     Jungar
 */
?>
<?php if (count($this->navigation)): ?>
    <div class='tabs'>
        <?php
        // Render the menu
        //->setUlClass()
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>

<script type="text/javascript">
    function deleteSelected() {
        var form = $('contests_form');
        return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete the selected contests?")) ?>');
    }

    function selectAll() {
        var form = $('contests_form');
        var inputs = form.elements;
        for (var i = 1; i < inputs.length - 1; i++) {
            if (!inputs[i].disabled) {
                inputs[i].checked = inputs[0].checked;
            }
        }
    }
</script>
<div class='admin_results'>
    <div>
        <?php echo $this->paginationControl($this->paginator, null, null, array(
            'pageAsQuery' => true,
            'query' => $this->formValues,
        )); ?>
    </div>
</div>
<div class="admin_table_form">
    <form id='contests_form' method="post" action="<?php echo $this->url(array('action' => 'delete-selected')); ?>"
          onSubmit="deleteSelected();">
        <table class='admin_table'>
            <thead>
            <tr>
                <th style='width: 1%;'><input onclick="selectAll()" type='checkbox' class='checkbox'></th>
                <th><?php echo $this->translate("Title") ?></th>
                <th><?php echo $this->translate("Date begin") ?></th>
                <th><?php echo $this->translate("Date End") ?></th>
                <th><?php echo $this->translate("Sponsor") ?></th>
                <th><?php echo $this->translate("Participants") ?></th>
                <th style='width: 1%;' class='admin_table_options'><?php echo $this->translate("Options") ?></th>
            </tr>
            </thead>
            <tbody>
            <?php if (count($this->paginator)): ?>
                <?php foreach ($this->paginator as $item):
                    ?>
                    <tr>
                        <td><input name='contest_<?php echo $item->getIdentity(); ?>'
                                   value=<?php echo $item->getIdentity(); ?> type='checkbox' class='checkbox'>
                        </td>
                        <td class='admin_table_bold'>
                            <?php echo $this->htmlLink($item->getAdminHref(),
                                $this->string()->truncate($item->getTitle(), 20),
                                array())?>
                        </td>
                        <td><?php echo $item->date_begin; ?></td>
                        <td><?php echo $item->date_end; ?></td>
                        <td><?php echo $item->getSponsor(); ?></td>
                        <td><?php echo $item->getParticipantsCount(); ?></td>

                        <td class='admin_table_options'>
                            <?php if($item->is_active) : ?>
                                <a class='smoothbox' href='<?php echo $this->url(array('action' => 'activate', 'activate' => '2', 'hecontest_id' => $item->getIdentity())); ?>'>
                                    <?php echo strtolower($this->translate('HECONTEST_Deactivate')); ?>
                                </a>
                            <?php else: ?>
                                <a class='smoothbox' href='<?php echo $this->url(array('action' => 'activate', 'activate' => '1', 'hecontest_id' => $item->getIdentity())); ?>'>
                                    <?php echo strtolower($this->translate('HECONTEST_Activate')); ?>
                                </a>
                            <?php endif; ?>
                            |
                            <a class=''
                               href='<?php echo $this->url(array('action' => 'edit', 'hecontest_id' => $item->getIdentity())); ?>'>
                                <?php echo $this->translate("edit") ?>
                            </a>
                            |
                            <a class='smoothbox'
                               href='<?php echo $this->url(array('action' => 'delete', 'hecontest_id' => $item->getIdentity())); ?>'>
                                <?php echo $this->translate("delete") ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
        <br/>

        <div class='buttons'>
            <button type='submit' name="submit_button" value="delete"
                    style="float: right;"><?php echo $this->translate("Delete Selected") ?></button>
        </div>
    </form>
</div>