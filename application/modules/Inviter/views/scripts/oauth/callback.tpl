<?php if ($this->exit): ?>
  <script type="text/javascript">
    en4.core.runonce.add(function () {
      window.close();
    });
  </script>
<?php endif; ?>

<?php if ($this->contact_count > 0) : ?>

  <?php if ($this->signup_page) : ?>
    <script type="text/javascript">
      en4.core.runonce.add(function () {
        var $parent = window.opener;
        var $form = $parent.$('invite_friends');
        $form.submit();
        window.close();
      });
    </script>
  <?php else : ?>
    <script type="text/javascript">
      en4.core.runonce.add(function () {
        var $parent = window.opener;
        var $form = $parent.$('invite_friends');

        if (!$form) {
          $parent.location.href = $parent.location.href;
          window.close();
          return;
        }

        $form.submit();
        window.close();
      });
    </script>
  <?php endif; ?>

<?php else : ?>

  <script type="text/javascript">
    en4.core.runonce.add(function () {
      var $parent = window.opener;
      $parent.he_show_message("There are no contacts.");
      window.close();
    });
  </script>

<?php endif; ?>