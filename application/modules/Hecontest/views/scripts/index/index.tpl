

<?php if ($this->contests) : ?>
  <div class="active-contest-wrapper">

    <?php if ($this->contests && $this->contests->getTotalItemCount()): ?>
      <ul>

        <?php foreach ($this->contests as $participant): $t = $participant->getHref(); ;?>

          <li style="overflow: hidden;">
            <a class="hecontest-item hecontest-item-content"
               href="<?php echo $participant->getHref() ?>"
               onclick="clickIfHash('<?php echo $participant->getIdentity(); ?>');"
               style="background-image: url('<?php echo $participant->getPhotoUrl(); ?>');       background-size: cover;  ">
            </a>

            <div class="hecontest-items-info" style="  background-color: rgba(0,0,0,0.5);height: 45px;color: #fff;padding: 5px;">
              <a href="<?php echo $participant->getHref() ?>" style="text-decoration: none"><?php echo $participant->getTitle() ?></a><br>
              <a href="<?php echo $participant->getHref() ?>" style="text-decoration: none"><?php echo $participant->date_end ?></a><br>
             
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php else : ?>
      <div class="tip">
        <span><?php echo $this->translate("HECONTEST_No participants"); ?></span>
      </div>
    <?php endif; ?>
  </div>
<?php else : ?>
  <div class="tip">
    <span><?php echo $this->translate("HECONTEST_No recent contest"); ?></span>
  </div>
<?php endif; ?>
