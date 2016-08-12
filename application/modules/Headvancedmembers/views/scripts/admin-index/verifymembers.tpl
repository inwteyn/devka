<ul>
  <h3 style="margin-bottom: 10px">Members who verified this member</h3>
  <?php
if(count($this->items)>0){
  foreach($this->items as $item){
    $user = Engine_Api::_()->getItem('user', $item['verified_id']);
    ?>
  <li style="    border-bottom: 1px solid #999;">
    <div style="float: left"> <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon')) ?></div>
    <div class='browsemembers_results_info' style="float: left;width: 60%;line-height: 25px">
      <?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?>
    </div>
    <div style="clear: both"></div>
  </li>
  <?php
  }
}else{
echo 'users not found';
}
?>
  </ul>