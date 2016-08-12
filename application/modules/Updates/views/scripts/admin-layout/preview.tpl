<script src="application/modules/Updates/externals/scripts/mooRainbow.js" type="text/javascript"></script>
<script src="application/modules/Updates/externals/scripts/preview.js" type="text/javascript"></script>
<link type="text/css" href="application/modules/Updates/externals/styles/mooRainbow.css" rel="stylesheet"></link>

<?php $this->headLink()
  ->prependStylesheet($this->baseUrl().'/application/css.php?request=application/modules/Updates/externals/styles/preview.css');
?>

<div class="admin_home_middle" style="width: 100%; width: 100%; margin: 10pt 0pt 0pt 0pt; padding: 0pt;" align="center">
<div 	style='width: 720px'>

  <div class="admin_layoutbox_menu">
    <ul style="margin:0pt; padding:0pt;">
        <li>
        	<a href="javascript://" id='background_color' style="padding-left: 3px;margin:2px;">
        		<div id='background_box' style='-moz-border-radius:4px 4px 4px 4px; float:left; width:13px; height:13px; margin-right:4px; border:1px solid;'>&nbsp;</div>
        		<?php echo $this->translate('UPDATES_Background'); ?>
        	</a>
        </li>
        <li>
        	<a href="javascript://" id='font_color' style="padding-left:3px; margin:2px;">
        		<div id='font_box' style='-moz-border-radius:4px 4px 4px 4px; float:left; width:13px; height:13px; margin-right:4px; border:1px solid;'>&nbsp;</div>
        		<?php echo $this->translate('UPDATES_Fonts'); ?>
        	</a>
        </li>
        <li>
        	<a href="javascript://" id='titles_color' style="padding-left: 3px;margin:2px;">
        		<div id='titles_box' style='-moz-border-radius:4px 4px 4px 4px; float:left; width:13px; height:13px; margin-right:4px; border:1px solid;'>&nbsp;</div>
        		<?php echo $this->translate('UPDATES_Titles'); ?>
        	</a>
        </li>
        <li>
        	<a href="javascript://" id='links_color' style="padding-left: 3px;margin:2px;">
        		<div id='links_box' style='-moz-border-radius:4px 4px 4px 4px; float:left; width:13px; height:13px; margin-right:4px; border:1px solid;'>&nbsp;</div>
        		<?php echo $this->translate('UPDATES_Links'); ?>
        	</a>
        </li>
        <li style="float:right;">
        	<div style="margin:5px;">
						<?php echo $this->form; ?>
					</div><div style="clear: both">
				</div>
        </li>
  	</ul>
 	</div>
 </div>
<?php echo $this->message; ?>
</div>