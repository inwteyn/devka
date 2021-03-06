
<?php $counter = 1; ?>
<?php if( $this->donations->getTotalItemCount() > 0 ): ?>
<ul class="projects">
  <?php foreach($this->donations as $donation):?>
  <?php if($counter == 3): ?>
      <li class="third">
      <?php $counter = 0; ?>
    <?php else: ?>
      <li>
    <?php endif; ?>
  <div class="photo">
    <?php echo $this->htmlLink($donation->getHref(),$this->itemPhoto($donation,'thumb.donation')); ?>
  </div>
  <div class="donation_info">
    <div class="donation_title">
      <h3><?php echo $this->htmlLink($donation, $this->string()->chunk($this->string()->truncate($donation->getTitle(), 25), 10)) ?></h3>
    </div>
    <div class="donation_options">
      <div class="raised">
        <label><?php echo $this->translate('DONATION_Raised:'); ?></label>
        <span><?php echo $this->locale()->toCurrency((double)$donation->raised_sum, $this->currency) ?></span>
      </div>
      <div class="raised">
        <label><?php echo $this->translate('DONATION_Target:'); ?></label>
        <span><?php echo $this->locale()->toCurrency((double)$donation->target_sum, $this->currency) ?></span>
      </div>
      <?php if (strtotime($donation->expiry_date)):?>
      <div class="raised">
        <label><?php echo $this->translate('DONATION_Limited:'); ?></label>
        <?php
        $left = Engine_Api::_()->getApi('core', 'donation')->datediff(new DateTime($donation->expiry_date), new DateTime(date("Y-m-d H:i:s")));
        $month = (int)$left->format('%m');
        $day = (int)$left->format('%d');
        ?>
        <span>
            <?php if($month > 0): ?>
          <?php echo $this->translate(array("%s month", "%s months", $month), $month);?>
          <?php endif;?>
          <?php echo $this->translate(array("%s day left", "%s days left", $day), $day);?>
          </span>
      </div>
      <?php endif; ?>
    </div>
    <button name="submit" type="submit"
            onclick="window.open('<?php echo $this->url(array('object' => $donation->getType(),'object_id' => $donation->getIdentity()),'donation_donate',true); ?>')">
      <?php echo $this->translate('DONATION_Donate'); ?>
    </button>
  </div>
  <div class="progress_cont">
    <div class="progress">
      <?php $status =  round(100 * $donation->getRaised() / $donation->getTargetSum(), 0);?>
      <div class="bar" style="width: <?php echo $status > 100 ? 100 : $status;?>%"></div>
    </div>
    <span><?php echo $status > 100 ? 100 : $status;?>%</span>
    <br />
  </div>
  <?php if($this->like_count[$donation->getIdentity()]):?>
    <div class="label">
      <?php
      echo $this->translate(array("%s supporter:", "%s supporters:", $this->like_count[$donation->getIdentity()]), $this->locale()->toNumber($this->like_count[$donation->getIdentity()]));
      ?>
    </div>
    <div class="supporters">
      <?php foreach($this->supporters[$donation->getIdentity()] as $supporter): ?>
      <?php
      if(null!=$item = Engine_Api::_()->getItem('user',$supporter['user_id']))
      {
        echo $this->htmlLink($item->getHref(),$this->itemPhoto($item,'thumb.icon'));
      }
      ?>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
      </li>
      <?php $counter++; ?>
  <?php endforeach; ?>
</ul>
<?php
  if($this->donations->count() > 1){
    echo $this->paginationControl($this->donations, null, array("pagination.tpl","donation"), array(
      'page' => $this->subject,
      'type' => 'project'
    ));
  }
?>
<?php else: ?>
<br/>
  <div class="tip">
<?php
  $create_project = Engine_Api::_()->authorization()->isAllowed('donation', $viewer, 'create_project');
  if($create_project==1){
?>
     <span>
        <?php echo $this->translate('DONATION_Nobody has created a donation yet.');?>
        <?php if($this->subject->getDonationPrivacy('project')):?>
          <?php echo $this->translate('DONATION_Be the first to %1$screate%2$s one!',
            '<a href="'.$this->url(array('action' => 'create','controller' => 'project','page_id' => $this->subject->getIdentity()),
            'donation_extended',true).'">', '</a>'); ?>
        <?php endif; ?>
      </span>
<?php  }else{?>
    <span>
       <?php echo $this->translate('DONATION_Nobody has created a donation yet.');?>
    </span>
  <?php }?>
  </div>
<?php endif; ?>