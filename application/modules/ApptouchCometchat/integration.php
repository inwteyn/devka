<?php

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/* ADVANCED */

define('SET_SESSION_NAME','');			// Session name
define('SWITCH_ENABLED','1');
define('INCLUDE_JQUERY','1');
define('FORCE_MAGIC_QUOTES','0');

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/* DATABASE */

define('_ENGINE', true);
if(!file_exists(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'settings'.DIRECTORY_SEPARATOR.'database.php')) {
	echo "Please check if cometchat is installed in the correct directory.<br /> The 'cometchat' folder should be placed at <SOCIALNENGINE_HOME_DIRECTORY>/cometchat";
	exit;
}

$db = include(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'settings'.DIRECTORY_SEPARATOR.'database.php');
// DO NOT EDIT DATABASE VALUES BELOW
// DO NOT EDIT DATABASE VALUES BELOW
// DO NOT EDIT DATABASE VALUES BELOW

define('DB_SERVER',				$db['params']['host']		);
define('DB_PORT',				'3306'				);
define('DB_USERNAME',				$db['params']['username']	);
define('DB_PASSWORD',				$db['params']['password']	);
define('DB_NAME',				$db['params']['dbname']		);
if(defined('USE_CCAUTH') && USE_CCAUTH == '0'){
define('TABLE_PREFIX',				$db['tablePrefix']		);
define('DB_USERTABLE',				'users'				);
define('DB_USERTABLE_NAME',			'displayname'			);
define('DB_USERTABLE_USERID',                   'user_id'			);
define('DB_AVATARTABLE',                        " left join ".TABLE_PREFIX."storage_files on file_id = ".TABLE_PREFIX.DB_USERTABLE.".photo_id" );
define('DB_AVATARFIELD',                        " (select storage_path from ".TABLE_PREFIX."storage_files where parent_file_id is null and file_id = ".TABLE_PREFIX.DB_USERTABLE.".photo_id)");

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/* FUNCTIONS */


	function getUserID() {
	    $userid = 0;
	    if (!empty($_SESSION['basedata']) && $_SESSION['basedata'] != 'null') {
	            $_REQUEST['basedata'] = $_SESSION['basedata'];
	    }

	    if (!empty($_REQUEST['basedata'])) {

	            if (function_exists('mcrypt_encrypt') && defined('ENCRYPT_USERID') && ENCRYPT_USERID == '1') {
	                    $key = "";
				if( defined('KEY_A') && defined('KEY_B') && defined('KEY_C') ){
					$key = KEY_A.KEY_B.KEY_C;
				}
	                    $uid = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode(rawurldecode($_REQUEST['basedata'])), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
	                    if (intval($uid) > 0) {
	                            $userid = $uid;
	                    }
	            } else {
	                    $userid = $_REQUEST['basedata'];
	            }
	    }

	    if (!empty($_COOKIE['PHPSESSID'])) {
	        $sql = ("SELECT data from ".TABLE_PREFIX."core_session where id = '".mysqli_real_escape_string($GLOBALS['dbh'],$_COOKIE['PHPSESSID'])."'");
	        $result = mysqli_query($GLOBALS['dbh'],$sql);
	        $row = mysqli_fetch_assoc($result);
	        $_SESSION['Zend_Auth']['storage'] = 0;
	        session_decode($row['data']);
	        if (filter_var($_SESSION['Zend_Auth']['storage'], FILTER_VALIDATE_EMAIL)) {
	            $email = $_SESSION['Zend_Auth']['storage'];
	            $sql = ("SELECT user_id FROM ".TABLE_PREFIX.DB_USERTABLE." WHERE email = '".mysqli_real_escape_string($GLOBALS['dbh'],$email)."'");
	            $result = mysqli_query($GLOBALS['dbh'], $sql);
	            $row = mysqli_fetch_assoc($result);
	            $userid = $row['user_id'];
	        } elseif (filter_var($_SESSION['Zend_Auth']['storage'], FILTER_VALIDATE_INT)) {
	            $userid = $_SESSION['Zend_Auth']['storage'];
	        }
	    }
	    $userid = intval($userid);
	    return $userid;
	}

	function chatLogin($userName,$userPass) {
		$userid = 0;
		global $guestsMode;
		if(!empty($_REQUEST['guest_login']) && $userPass == "CC^CONTROL_GUEST" && $guestsMode == 1) {
			$sql = ("INSERT INTO `cometchat_guests` (`name`, `lastactivity`) VALUES('".mysqli_real_escape_string($GLOBALS['dbh'], $userName)."','".getTimeStamp()."')");
			$query = mysqli_query($GLOBALS['dbh'], $sql);
			$userid = mysqli_insert_id($GLOBALS['dbh']);

			if (isset($_REQUEST['callbackfn']) && $_REQUEST['callbackfn'] == 'mobileapp') {
                $sql = ("insert into cometchat_status (userid,isdevice) values ('".mysqli_real_escape_string($GLOBALS['dbh'],$userid)."','1') on duplicate key update isdevice = '1'");
                mysqli_query($GLOBALS['dbh'], $sql);
            }
		} else {
			if (filter_var($userName, FILTER_VALIDATE_EMAIL)) {
				$sql = ("SELECT * FROM ".TABLE_PREFIX.DB_USERTABLE." WHERE email = '".mysqli_real_escape_string($GLOBALS['dbh'],$userName)."'");
			} else {
				$sql = ("SELECT * FROM ".TABLE_PREFIX.DB_USERTABLE." WHERE username = '".mysqli_real_escape_string($GLOBALS['dbh'],$userName)."'");
			}
			$result = mysqli_query($GLOBALS['dbh'],$sql);
			$row = mysqli_fetch_assoc($result);
			$sql1 = ("SELECT * FROM `".TABLE_PREFIX."core_settings` WHERE name = 'core.secret'");
			$result1 = mysqli_query($GLOBALS['dbh'],$sql1);
			$row1 = mysqli_fetch_assoc($result1);
			$salted_password = md5($row1['value'].$userPass.$row['salt']);
			if($row['password'] == $salted_password) {
				$userid = $row['user_id'];
	            if (isset($_REQUEST['callbackfn']) && $_REQUEST['callbackfn'] == 'mobileapp') {
	                $sql = ("insert into cometchat_status (userid,isdevice) values ('".mysqli_real_escape_string($GLOBALS['dbh'],$userid)."','1') on duplicate key update isdevice = '1'");
	                mysqli_query($GLOBALS['dbh'], $sql);
	            }
			}
		}
		if($userid && function_exists('mcrypt_encrypt') && defined('ENCRYPT_USERID') && ENCRYPT_USERID == '1') {
			$key = "";
				if( defined('KEY_A') && defined('KEY_B') && defined('KEY_C') ){
					$key = KEY_A.KEY_B.KEY_C;
				}
			$userid = rawurlencode(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $userid, MCRYPT_MODE_CBC, md5(md5($key)))));
		}

		return $userid;
	}

	function getFriendsList($userid,$time) {
		global $hideOffline;
		$offlinecondition = '';
		$sql = ("select ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." userid, ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_NAME." username, ".TABLE_PREFIX.DB_USERTABLE.".username link, ".DB_AVATARFIELD." avatar, cometchat_status.lastactivity lastactivity, cometchat_status.status, cometchat_status.message, cometchat_status.isdevice from  ".TABLE_PREFIX."user_membership join ".TABLE_PREFIX.DB_USERTABLE."  on ".TABLE_PREFIX."user_membership.user_id = ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." left join cometchat_status on ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." = cometchat_status.userid ".DB_AVATARTABLE." where ".TABLE_PREFIX."user_membership.resource_id = '".mysqli_real_escape_string($GLOBALS['dbh'],$userid)."' and active = 1 order by username asc");

		if ((defined('MEMCACHE') && MEMCACHE <> 0) || DISPLAY_ALL_USERS == 1) {
			if ($hideOffline) {
				$offlinecondition = "where ((cometchat_status.lastactivity > (".mysqli_real_escape_string($GLOBALS['dbh'],$time)."-".((ONLINE_TIMEOUT)*2).")) OR cometchat_status.isdevice = 1) and (cometchat_status.status IS NULL OR cometchat_status.status <> 'invisible' OR cometchat_status.status <> 'offline') ";
			}
			$sql = ("select ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." userid, ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_NAME." username, ".TABLE_PREFIX.DB_USERTABLE.".username link, ".DB_AVATARFIELD." avatar, cometchat_status.lastactivity lastactivity, cometchat_status.status, cometchat_status.message, cometchat_status.isdevice from  ".TABLE_PREFIX.DB_USERTABLE."  left join cometchat_status on ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." = cometchat_status.userid ".DB_AVATARTABLE." ".$offlinecondition." order by username asc");
		}

		return $sql;
	}

	function getFriendsIds($userid) {

		$sql = ("select ".TABLE_PREFIX."user_membership.user_id friendid from ".TABLE_PREFIX."user_membership where ".TABLE_PREFIX."user_membership.resource_id = '".mysqli_real_escape_string($GLOBALS['dbh'],$userid)."' and active = 1");

		return $sql;
	}

	function getUserDetails($userid) {
		$sql = ("select ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." userid, ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_NAME." username, ".TABLE_PREFIX.DB_USERTABLE.".username link, ".DB_AVATARFIELD." avatar, cometchat_status.lastactivity lastactivity, cometchat_status.status, cometchat_status.message, cometchat_status.isdevice from ".TABLE_PREFIX.DB_USERTABLE." left join cometchat_status on ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." = cometchat_status.userid ".DB_AVATARTABLE." where ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." = '".mysqli_real_escape_string($GLOBALS['dbh'],$userid)."'");

		return $sql;
	}

	function updateLastActivity($userid) {
		$sql = ("insert into cometchat_status (userid,lastactivity) values ('".mysqli_real_escape_string($GLOBALS['dbh'],$userid)."','".getTimeStamp()."') on duplicate key update lastactivity = '".getTimeStamp()."'");
		return $sql;
	}

	function getUserStatus($userid) {
		 $sql = ("select ".TABLE_PREFIX.DB_USERTABLE.".status message, cometchat_status.status from ".TABLE_PREFIX.DB_USERTABLE." left join cometchat_status on ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." = cometchat_status.userid where ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." = '".mysqli_real_escape_string($GLOBALS['dbh'],$userid)."'");

		 return $sql;
	}

	function fetchLink($link) {
		$cc_url = (defined('CC_SITE_URL') ? CC_SITE_URL : BASE_URL);
		return $cc_url."../profile/".$link;
	}

	function getAvatar($image) {
		$cc_url = (defined('CC_SITE_URL') ? CC_SITE_URL : BASE_URL);
		if (is_file(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.''.$image)) {
			return $cc_url."../".$image;
		} else {
			return $cc_url."../application/modules/User/externals/images/nophoto_user_thumb_icon.png";
		}
	}

	function getTimeStamp() {
		return time();
	}

	function processTime($time) {
		return $time;
	}

	if (!function_exists('getLink')) {
	  	function getLink($userid) { return fetchLink($userid); }
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/* HOOKS */

	function hooks_statusupdate($userid,$statusmessage) {
		$sql = ("update ".TABLE_PREFIX.DB_USERTABLE." set status = '".mysqli_real_escape_string($GLOBALS['dbh'],$statusmessage)."', status_date = '".date("Y-m-d H:i:s",getTimeStamp())."' where ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." = '".mysqli_real_escape_string($GLOBALS['dbh'],$userid)."'");
	 	$query = mysqli_query($GLOBALS['dbh'],$sql);
	}

	function hooks_forcefriends() {

	}

	function hooks_activityupdate($userid,$status) {

	}

	function hooks_message($userid,$to,$unsanitizedmessage) {

	}

	function hooks_updateLastActivity($userid) {

	}
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/* LICENSE */

include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'license.php');
$x = "\x62a\x73\x656\x34\x5fd\x65c\157\144\x65";
eval($x('JHI9ZXhwbG9kZSgnLScsJGxpY2Vuc2VrZXkpOyRwXz0wO2lmKCFlbXB0eSgkclsyXSkpJHBfPWludHZhbChwcmVnX3JlcGxhY2UoIi9bXjAtOV0vIiwnJywkclsyXSkpOw'));

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
