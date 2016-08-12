<ul class="page-random-photos" id="PageRandomPhotos">
    <?php foreach ($this->photos as $photo): ?>
        <li>
            <a href="<?php echo $photo->getHref(); ?>" class="aimg">
                <div class="page-random-photos-photo"
                     style="background-image: url('<?php echo $photo->getPhotoUrl(); ?>')">
                </div>
            </a>
        </li>
    <?php endforeach; ?>
</ul>

<script type="text/javascript">
    window.setTimeout(function () {
        $('PageRandomPhotos').getElements('li').each(function (elem) {
            elem.setStyle('height', elem.getComputedSize().width);
        });
    }, 1000);
</script>