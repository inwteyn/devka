<?php

class Pagevideo_Plugin_Job_Encode extends Core_Plugin_Job_Abstract
{
  protected function _execute()
  {
    // Get job and params
    $job = $this->getJob();

    // No pagevideo id?
    if( !($pagevideo_id = $this->getParam('pagevideo_id')) ) {
      $this->_setState('failed', 'No pagevideo identity provided.');
      $this->_setWasIdle();
      return;
    }

    // Get pagevideo object
    $pagevideo = Engine_Api::_()->getItem('pagevideo', $pagevideo_id);

    if( !$pagevideo || !($pagevideo instanceof Pagevideo_Model_Pagevideo) ) {
      $this->_setState('failed', 'pagevideo is missing.');
      $this->_setWasIdle();
      return;
    }

    // Check pagevideo status
    if( 0 != $pagevideo->status ) {
      $this->_setState('failed', 'pagevideo has already been encoded, or has already failed encoding.');
      $this->_setWasIdle();
      return;
    }

    // Process
    try {
      $this->_process($pagevideo);
      $this->_setIsComplete(true);
    } catch( Exception $e ) {
      $this->_setState('failed', 'Exception: ' . $e->getMessage());
    }
  }

  protected function _process($pagevideo)
  {
    // Make sure FFMPEG path is set
    $ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->video_ffmpeg_path;
    if( !$ffmpeg_path ) {
      throw new Exception('Ffmpeg not configured');
    }
    // Make sure FFMPEG can be run
    if( !@file_exists($ffmpeg_path) || !@is_executable($ffmpeg_path) ) {
      $output = null;
      $return = null;
      exec($ffmpeg_path . ' -version', $output, $return);
      if( $return > 0 ) {
        throw new Exception('Ffmpeg found, but is not executable');
      }
    }

    // Check we can execute
    if( !function_exists('shell_exec') ) {
      throw new Exception('Unable to execute shell commands using shell_exec(); the function is disabled.');
    }

    // Check the pagevideo temporary directory
    $tmpDir = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary' .
      DIRECTORY_SEPARATOR . 'pagevideo';
    if( !is_dir($tmpDir) ) {
      if( !mkdir($tmpDir, 0777, true) ) {
        throw new Exception('pagevideo temporary directory did not exist and could not be created.');
      }
    }
    if( !is_writable($tmpDir) ) {
      throw new Exception('pagevideo temporary directory is not writable.');
    }

    // Get the pagevideo object
    if( is_numeric($pagevideo) ) {
      $pagevideo = Engine_Api::_()->getItem('pagevideo', $pagevideo);
    }

    if( !($pagevideo instanceof Pagevideo_Model_Pagevideo) ) {
      throw new Exception('Argument was not a valid pagevideo');
    }

    // Update to encoding status
    $pagevideo->status = 2;
    $pagevideo->type = 3;
    $pagevideo->save();

    // Prepare information
    $owner = $pagevideo->getOwner();
    $filetype = $pagevideo->code;

    // Pull video from storage system for encoding
    $storageObject = Engine_Api::_()->getItem('storage_file', $pagevideo->file_id);

    if( !$storageObject ) {
      throw new Video_Model_Exception('Video storage file was missing');
    }

    $originalPath = $storageObject->temporary();
    if( !file_exists($originalPath) ) {
      throw new Video_Model_Exception('Could not pull to temporary file');
    }

//    $originalPath  = $tmpDir . DIRECTORY_SEPARATOR . $pagevideo->getIdentity() . '.' . $filetype;

    $outputPath    = $tmpDir . DIRECTORY_SEPARATOR . $pagevideo->getIdentity() . '_vconverted.flv';
    $thumbPath     = $tmpDir . DIRECTORY_SEPARATOR . $pagevideo->getIdentity() . '_vthumb.jpg';

    $picPath       = $tmpDir . DIRECTORY_SEPARATOR . $pagevideo->getIdentity() . '_vpic.jpg';
    $thumbMiniPath = $tmpDir . DIRECTORY_SEPARATOR . $pagevideo->getIdentity() . '_vthumbmini.jpg';
    $thumbIconPath = $tmpDir . DIRECTORY_SEPARATOR . $pagevideo->getIdentity() . '_vthumbicon.jpg';


    $pagevideoCommand = $ffmpeg_path . ' '
      . '-i ' . escapeshellarg($originalPath) . ' '
      . '-ab 64k' . ' '
      . '-ar 44100' . ' '
      . '-qscale 5' . ' '
      . '-vcodec flv' . ' '
      . '-f flv' . ' '
      . '-r 25' . ' '
      . '-s 480x386' . ' '
      . '-v 2' . ' '
      . '-y ' . escapeshellarg($outputPath) . ' '
      . '2>&1'
      ;

    // Prepare output header
    $output  = PHP_EOL;
    $output .= $originalPath . PHP_EOL;
    $output .= $outputPath . PHP_EOL;
    $output .= $picPath . PHP_EOL;


    // Prepare logger
    $log = null;
    if( APPLICATION_ENV == 'development' ) {
      $log = new Zend_Log();
      $log->addWriter(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/pagevideo.log'));
    }

    // Execute pagevideo encode command
    $pagevideoOutput = $output .
      $pagevideoCommand . PHP_EOL .
      shell_exec($pagevideoCommand);

    // Log
    if( $log ) {
      $log->log($pagevideoOutput, Zend_Log::INFO);
    }

    // Check for failure
    $success = true;

    // Unsupported format
    if( preg_match('/Unknown format/i', $pagevideoOutput) ||
        preg_match('/Unsupported codec/i', $pagevideoOutput) ||
        preg_match('/patch welcome/i', $pagevideoOutput) ||
        preg_match('/Audio encoding failed/i', $pagevideoOutput) ||
        !is_file($outputPath) ||
        filesize($outputPath) <= 0 ) {

      $success = false;
      $pagevideo->status = 3;
    }

    // This is for audio files
    else if( preg_match('/pagevideo:0kB/i', $pagevideoOutput) ) {
      $success = false;
      $pagevideo->status = 5;
    }

    // Failure
    if( !$success ) {

      $exceptionMessage = '';

      $db = $pagevideo->getTable()->getAdapter();
      $db->beginTransaction();
      try {
        $pagevideo->save();

        // notify the owner
        $translate = Zend_Registry::get('Zend_Translate');
        $language = ( !empty($owner->language) && $owner->language != 'auto' ? $owner->language : null );
        $notificationMessage = '';

        if( $pagevideo->status == 3 ) {
          $exceptionMessage ='pagevideo format is not supported by FFMPEG.';
          $notificationMessage = $translate->translate(sprintf(
            'pagevideo conversion failed. pagevideo format is not supported by FFMPEG. Please try %1$sagain%2$s.',
            '',
            ''
          ), $language);
        } else if( $pagevideo->status == 5 ) {
          $exceptionMessage = 'Audio-only files are not supported.';
          $notificationMessage = $translate->translate(sprintf(
            'pagevideo conversion failed. Audio files are not supported. Please try %1$sagain%2$s.',
            '',
            ''
          ), $language);
        } else {
          $exceptionMessage = 'Unknown encoding error.';
        }

        Engine_Api::_()->getDbtable('notifications', 'activity')
          ->addNotification($owner, $owner, $pagevideo, 'pagevideo_processed_failed', array(
            'message' => $notificationMessage,
            'message_link' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'page_video', true),
          ));

        $db->commit();
      } catch( Exception $e ) {
        $pagevideoOutput .= PHP_EOL . $e->__toString() . PHP_EOL;
        if( $log ) {
          $log->write($e->__toString(), Zend_Log::ERR);
        }
        $db->rollBack();
      }

      // Write to additional log in dev
      if( APPLICATION_ENV == 'development' ) {
        file_put_contents($tmpDir . '/' . $pagevideo->pagevideo_id . '.txt', $pagevideoOutput);
      }

      throw new Exception($exceptionMessage);
    }

    // Success
    else
    {
      // Get duration of the pagevideo to caculate where to get the thumbnail
      if( preg_match('/Duration:\s+(.*?)[.]/i', $pagevideoOutput, $matches) ) {
        list($hours, $minutes, $seconds) = preg_split('[:]', $matches[1]);
        $duration = ceil($seconds + ($minutes * 60) + ($hours * 3600));
      } else {
        $duration = 0; // Hmm
      }

      // Log duration
      if( $log ) {
        $log->log('Duration: ' . $duration, Zend_Log::INFO);
      }

      // Fetch where to take the thumbnail
      $thumb_splice = $duration / 2;

      // Thumbnail proccess command
      $thumbCommand = $ffmpeg_path . ' '
      . '-i ' . escapeshellarg($outputPath) . ' '
      . '-f image2' . ' '
      . '-ss '. $thumb_splice . ' '
      . '-v 2' . ' '
      . '-y ' . escapeshellarg($picPath) . ' '
      . '2>&1'
      ;

      // Process thumbnail
      $thumbOutput = $output .
        $thumbCommand . PHP_EOL .
        shell_exec($thumbCommand);

      // Log thumb output
      if( $log ) {
        $log->log($thumbOutput, Zend_Log::INFO);
      }

      // Check output message for success
      $thumbSuccess = true;
      if( preg_match('/pagevideo:0kB/i', $thumbOutput) ) {
        $thumbSuccess = false;
      }

      // Resize thumbnail
      if( $thumbSuccess ) {
        $image = Engine_Image::factory();
        $image->open($picPath)
          ->resize(120, 240)
          ->write($thumbPath)
          ->destroy();

        $image = Engine_Image::factory();
        $image->open($picPath)
          ->resize(34, 34)
          ->write($thumbMiniPath)
          ->destroy();

        $image = Engine_Image::factory();
        $image->open($picPath)
          ->resize(48, 48)
          ->write($thumbIconPath)
          ->destroy();
      }

      // Save pagevideo and thumbnail to storage system
      $params = array(
        'parent_id' => $pagevideo->getIdentity(),
        'parent_type' => $pagevideo->getType(),
        'user_id' => $pagevideo->user_id
      );

      $db = $pagevideo->getTable()->getAdapter();
      $db->beginTransaction();

      try {
        $pagevideoFileRow = Engine_Api::_()->storage()->create($outputPath, $params);
        if( $thumbSuccess ) {
          $thumbFileRow = Engine_Api::_()->storage()->create($thumbPath, $params);
          $thumbMiniFileRow = Engine_Api::_()->storage()->create($thumbMiniPath, $params);
          $thumbIconFileRow = Engine_Api::_()->storage()->create($thumbIconPath, $params);

          $thumbFileRow->bridge($thumbMiniFileRow, 'thumb.mini');
          $thumbFileRow->bridge($thumbIconFileRow, 'thumb.icon');
        }
        $db->commit();

      } catch( Exception $e ) {
        $db->rollBack();

        // delete the files from temp dir
        unlink($originalPath);
        unlink($outputPath);
        if( $thumbSuccess ) {
          unlink($picPath);
          unlink($thumbPath);
          unlink($thumbMiniPath);
          unlink($thumbIconPath);
        }

        $pagevideo->status = 7;
        $pagevideo->save();

        // notify the owner
        $translate = Zend_Registry::get('Zend_Translate');
        $notificationMessage = '';
        $language = ( !empty($owner->language) && $owner->language != 'auto' ? $owner->language : null );
        if( $pagevideo->status == 7 ) {
          $notificationMessage = $translate->translate(sprintf(
            'pagevideo conversion failed. You may be over the site upload limit.  Try %1$suploading%2$s a smaller file, or delete some files to free up space.',
            '',
            ''
          ), $language);
        }
        Engine_Api::_()->getDbtable('notifications', 'activity')
          ->addNotification($owner, $owner, $pagevideo, 'pagevideo_processed_failed', array(
            'message' => $notificationMessage,
            'message_link' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'page_video', true),
          ));

        throw $e; // throw
      }

      // pagevideo processing was a success!
      // Save the information
      $pagevideo->file_id = $pagevideoFileRow->file_id;
      if ( $thumbSuccess ) {
        $pagevideo->photo_id = $thumbFileRow->file_id;
      }
      $pagevideo->duration = $duration;
      $pagevideo->status = 1;
      $pagevideo->save();

      // delete the files from temp dir
      unlink($originalPath);
      unlink($outputPath);
      unlink($thumbPath);

      // insert action in a seperate transaction if pagevideo status is a success
      $actionsTable = Engine_Api::_()->getDbtable('actions', 'activity');
      $db = $actionsTable->getAdapter();
      $db->beginTransaction();

      try {
        // new action
        $action = $actionsTable->addActivity($owner, $pagevideo->getPage(), 'pagevideo_new');
        if( $action ) {
          $actionsTable->attachActivity($action, $pagevideo);
        }

        // notify the owner
        Engine_Api::_()->getDbtable('notifications', 'activity')
          ->addNotification($owner, $owner, $pagevideo, 'pagevideo_processed');

        $db->commit();

      } catch( Exception $e ) {
        $db->rollBack();
        throw $e; // throw
      }
    }
  }
}