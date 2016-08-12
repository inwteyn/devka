
<?php

 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: message.tpl 2010-07-02 17:53 michael $
 * @author     Michael
 */

?>

<ul class="form-<?php if ($this->result):?>notices<?php else: ?>errors<?php endif;?>">
  <li><?php echo $this->message?></li>
</ul>
