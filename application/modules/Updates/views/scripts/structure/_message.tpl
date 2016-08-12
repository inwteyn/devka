<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: module.tpl 2010-07-02 18:53 mirlan $
 * @author     Mirlan
 */
?>


<div style="width: 100%;" align="center">
  <div id="background" style="display: inline-block;background:<?php echo $this->backgroundColor ?>;">
	<table width="680px" cellpadding="0" cellspacing="0" class="fontcolors"
         style="padding:20px 10px; font-size:11px;color:<?php echo $this->fontColor; ?>;font-family:tahoma,arial,verdana,sans-serif;text-align:left;">

	<?php foreach($this->contents as $parent_cont=>$pos):?>

	<?php if ( array_key_exists('left', $pos) && array_key_exists('middle', $pos) && array_key_exists('right', $pos)): ?>
		<tr>
      <td>
        <table class="fontcolors" style="font-size:11px;color:<?php echo $this->fontColor; ?>;">
          <tr>
            <td valign="top" width="160px" style="overflow: hidden; display: inline-block;">
              <?php echo $pos['left'] ?>
            </td>

            <td valign="top" width="280px">
              <?php echo $pos['middle'] ?>
            </td>

            <td valign="top" width="160px" style="overflow: hidden; display: inline-block;">
              <?php echo $pos['right'] ?>
            </td>
          </tr>
        </table>
		  </td>
    </tr>

	<?php elseif ( array_key_exists('left', $pos)	&& array_key_exists('middle', $pos)	&& !array_key_exists('right', $pos)): ?>
		<tr>
      <td>
        <table class="fontcolors" style="font-size:11px;color:<?php echo $this->fontColor; ?>;">
          <tr>
            <td valign="top" width="160px" style="overflow: hidden; display: inline-block;">
              <?php echo $pos['left'] ?>
            </td>

            <td valign="top" width="500px">
              <?php echo $pos['middle'] ?>
            </td>
          </tr>
        </table>
      </td>
    </tr>

		<?php elseif ( !array_key_exists('left', $pos) && array_key_exists('middle', $pos) && array_key_exists('right', $pos)): ?>
			<tr>
        <td>
          <table class="fontcolors" style="font-size:11px;color:<?php echo $this->fontColor; ?>;">
            <tr>
              <td valign="top" width="500px">
                <?php echo $pos['middle'] ?>
              </td>

              <td valign="top" width="160px" style="overflow: hidden; display: inline-block;">
                <?php echo $pos['right'] ?>
              </td>
            </tr>
          </table>
        </td>
      </tr>

		<?php else: ?>
			<tr>
				<td valign="top">
          <?php echo $pos['middle'] ?>
				</td>
			</tr>
		<?php endif; ?>

	<?php endforeach; ?>

	<?php $mail = Engine_Api::_()->getApi('settings', 'core')->core_mail; ?>
		<tr>
			<td class="mainrows" style="font-size:12px;padding-top:20px;">
				<?php echo $this->translate('UPDATES_This email was sent to [email].') ?>&nbsp;|&nbsp;

        <?php if ($this->mailService != 'mailchimp'): ?>
          <!-- demoadmin -->
					<a href="http://<?php echo $_SERVER['HTTP_HOST'].$this->baseUrl()?>/updates/ajax/unsubscribe/[email]" style="text-decoration: none; color:<?php echo $this->linkColor; ?>" class="msgLink">
						<?php echo $this->translate('UPDATES_Unsubscribe') ?>
					</a>&nbsp;|&nbsp;
        <?php endif; ?>

					<a href="mailto:<?php echo $mail['from'] . '?subject=' . $mail['name'] . ':' . $this->translate('UPDATES_about Newsletter Updates Plugin')?>" style="text-decoration: none; color:<?php echo $this->linkColor ?>" class="msgLink">
						<?php echo $this->translate('UPDATES_Contact') ?>
					</a>
			</td>
		</tr>

	</table>
  </div>
</div>