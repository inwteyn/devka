<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Chat
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 9325 2011-09-27 00:11:15Z john $
 * @author     John
 */
?>

<?php if( $this->canChat ): ?>


  <script type="text/javascript">
    
    en4.core.runonce.add(function() {
        if( !$type(window._chatHandler) ) {
          chatHandler = new ChatHandler({
            'baseUrl' : en4.core.baseUrl,
            'basePath' : en4.core.basePath,
            //'identity' : <?php echo sprintf('%d', $this->viewer()->getIdentity()) ?>,
            'enableIM' : <?php echo $this->canIM ? 'true' : 'false' ?>,
            'enableChat' : <?php echo $this->canChat ? 'true' : 'false' ?>,
            'delay' : <?php echo sprintf('%d', Engine_Api::_()->getApi('settings', 'core')->getSetting('chat.general.delay', '5000')); ?>,
            'chatOptions' : {
              'operator' : <?php echo sprintf('%d', (int) $this->isOperator) ?>,
              'roomList' : <?php echo Zend_Json::encode($this->rooms) ?>,
              'container' : <?php echo ( $this->chatContainer ? "'" . $this->chatContainer . "'" : 'null' ) ?>
            }
          });
          chatHandler.start();
          window._chatHandler = chatHandler;
        }
        if( $type(window._chatHandler) ) {
          window._chatHandler.startChat({
            operator : <?php echo sprintf('%d', (int) $this->isOperator) ?>,
            roomList : <?php echo Zend_Json::encode($this->rooms) ?>
          });
        }
    });

  </script>

<?php else: ?>

  <div><?php echo $this->translate('The chat room has been disabled by the site admin.')?></div>
  
<?php endif; ?>

  