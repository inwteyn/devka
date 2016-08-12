<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page Documents
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2011-09-01 13:17:53 kirill $
 * @author     Kirill
 */

/**
 * @category   Application_Extensions
 * @package    Page Documents
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
require_once 'Google/vendor/autoload.php';
class Pagedocument_Plugin_Google
{

    public function getListDocuments(){

        $client = $this->settingsApi();
        $credentialsPath = $this->expandHomeDirectory(CREDENTIALS_PATH);

        if (file_exists($credentialsPath)) {
            $accessToken = file_get_contents($credentialsPath);

        }
        $client->setAccessToken($accessToken);

        $service = new Google_Service_Drive($client);

        $optParams = array(
            'pageSize' => 10,
            'fields' => "nextPageToken, files(id, name,)"
        );

        $results = $service->files->listFiles($optParams);

        if (count($results->getFiles()) == 0) {
            return 0;
        } else {
            return $results->getFiles();
        }

    }




    function settingsApi(){
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $client = new Google_Client();
        $client->setClientId($settings->getSetting('pagedocument.api.key'));
        $client->setClientSecret($settings->getSetting('pagedocument.secret.key'));
        $client->setRedirectUri($settings->getSetting('pagedocument.redirect.uri'));
        $client->setScopes(array('https://www.googleapis.com/auth/drive'));
        return $client;
    }



    function getClientUrl() {
        $client = $this->settingsApi();
        $authUrl = $client->createAuthUrl();
        return $authUrl;
    }



    function getClientToken(){
        $client = $this->settingsApi();
        $credentialsPath = $this->expandHomeDirectory(CREDENTIALS_PATH);
        if (file_exists($credentialsPath)) {
            $accessToken = file_get_contents($credentialsPath);
            $client->setAccessToken($accessToken);
        }
        if ($client->isAccessTokenExpired()) {
            $token = true;
        }else{
            $token = false;
        }
        return $token;
    }





    function uploadFile($name,$file_upload){

        $file_upload = explode("?", $file_upload);

        $client = $this->settingsApi();



        $credentialsPath = $this->expandHomeDirectory(CREDENTIALS_PATH);
        if (file_exists($credentialsPath)) {
            $accessToken = file_get_contents($credentialsPath);
            $client->setAccessToken($accessToken);
        }

        $service = new Google_Service_Drive($client);

        $fileMetadata = new Google_Service_Drive_DriveFile(array(
            'name' => $name,
            'mimeType' => 'application/vnd.google-apps.document'));



        $content = file_get_contents($file_upload[0]);

        $file = $service->files->create($fileMetadata, array(
            'data' => $content,
            'uploadType' => 'multipart',
            'fields' => 'id',

        ));

        //print_die($accessToken);
        $permission = new Google_Service_Drive_Permission();
        $permission->setRole( 'reader' );
        $permission->setType( 'anyone' );
        $service->permissions->create( $file->id, $permission );

        return($file->id);
    }

    function codeApi($key){
        if(!isset($key)||$key==''){
            return;
        }
        $client = $this->settingsApi();
        $credentialsPath = $this->expandHomeDirectory(CREDENTIALS_PATH);

        if (file_exists($credentialsPath)) {
            $accessToken = file_get_contents($credentialsPath);
        } else {
            $accessToken = $client->authenticate($key);
            if(!file_exists(dirname($credentialsPath))) {
                mkdir(dirname($credentialsPath), 0700, true);
            }
            file_put_contents($credentialsPath, $accessToken);
        }
        $client->setAccessToken($accessToken);

        if ($client->isAccessTokenExpired()) {
            $token = $client->authenticate($key);
            $client->refreshToken(json_decode($token)->access_token);
            file_put_contents($credentialsPath, $token);
            $client->setAccessToken($token);
        }

        return $client;
    }


    function expandHomeDirectory($path) {
        $homeDirectory = getenv('HOME');
        if (empty($homeDirectory)) {
            $homeDirectory = getenv("HOMEDRIVE") . getenv("HOMEPATH");
        }
        return str_replace('~', realpath($homeDirectory), $path);
    }


}