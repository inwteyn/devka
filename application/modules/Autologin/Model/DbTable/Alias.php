<?php

/**
 * Class Articles_Model_DbTable_Articles
 */
class Autologin_Model_DbTable_Alias extends Engine_Db_Table
{

    public function getAlias($module)
    {
      switch($module){
        case 'wall':
        $module = 'new-wall-plugin';
        break;
        case 'hecontest':
        $module = 'contest-plugin';
        break;
        case 'heloginpopup':
        $module = 'login-popup-plugin';
        break;
        case 'pinfeed':
        $module = 'pin-feed-plugin';
        break;
        case 'heevent':
        $module = 'advanced-events-plugin';
        break;
        case 'advancedsearch':
        $module = 'advanced-search-plugin';
        break;
        case 'hashtag':
        $module = 'hashtags-plugin';
        break;
        case 'highlights':
        $module = 'highlight-members-plugin';
        break;
        case 'advancedalbum':
        $module = 'advanced-photo-albums-plugin';
        break;
        case 'headvancedalbum':
        $module = 'advanced-photo-albums-plugin';
        break;
        case 'badges':
        $module = 'badges-plugin';
        break;
        case 'hebadge':
        $module = 'badges-plugin';
        break;
        case 'hetips':
        $module = 'free-tips-plugin';
        break;
        case 'hegift':
        $module = 'virtual-gifts-plugin';
        break;
        case 'credit':
        $module = 'credits-plugin';
        break;
        case 'donation':
        $module = 'social-donations-plugin';
        break;
        case 'daylogo':
        $module = 'day-logo-plugin';
        break;
        case 'timeline':
        $module = 'timeline-plugin';
        break;
        case 'offers':
        $module = 'offers-coupons-plugin';
        break;
        case 'checkin':
        $module = 'check-in-plugin';
        break;
        case 'page_document':
        $module = 'page-documents-plugin';
        break;
        case 'page_faq':
        $module = 'page-faq-plugin';
        break;
        case 'store':
        $module = 'store-plugin';
        break;
        case 'page_contact':
        $module = 'page-contact-plugin';
        break;
        case 'suggest':
        $module = 'suggestion-recommendation-plugin';
        break;
        case 'page_events':
        $module = 'page-events-plugin';
        break;
        case 'page_discussions':
        $module = 'page-discussions-plugin';
        break;
        case 'weather':
        $module = 'weather-plugin';
        break;
        case 'page_music':
        $module = 'page-music-plugin';
        break;
        case 'questions':
        $module = 'advanced-questions-plugin';
        break;
        case 'hequestion':
        $module = 'advanced-questions-plugin';
        break;
        case 'inviter':
        $module = 'friends-inviter-facebook-plugin';
        break;
        case 'page_blogs':
        $module = 'page-blogs-plugin';
        break;
        case 'page_videos':
        $module = 'page-videos-plugin';
        break;
        case 'page_albums':
        $module = 'page-albums-plugin';
        break;
        case 'pages':
        $module = 'pages-se4-plugin';
        break;
        case 'page':
        $module = 'pages-se4-plugin';
        break;

        case 'likes':
        $module = 'likes-plugin';
        break;
        case 'like':
        $module = 'likes-plugin';
        break;
        case 'updates':
        $module = 'newsletter-updates-plugin';
        break;
        case 'quiz':
        $module = 'quiz-se4-plugin';
        break;
        case 'rate':
        $module = 'rates-plugin';
        break;
        case 'hecore':
        $module = 'hire-experts-core-plugin';
        break;
        case 'welcome':
        $module = 'welcome-plugin';
        break;
        case 'usernotes':
        $module = 'user-notes-se4-plugin';
        break;
        case 'usernotes':
        $module = 'user-notes-se4-plugin';
        break;
        case 'apptouch':
        $module = 'touch-mobile-plugin';
        break;
        default:
        break;
      }
    return $module;
    }

}