<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 21/09/2015
 * Time: 08:15
 */
class Rate_Widget_RatePopularProfilesController extends Engine_Content_Widget_Abstract{
public function indexAction(){
    $this->view->maxRate = 5;
    $this->view->item_type = $item_type = 'user';
    $table = Engine_Api::_()->getDbtable('rates', 'rate');
    $settings = Engine_Api::_()->getApi('settings', 'core');

    $front_router = Zend_Controller_Front::getInstance()->getRouter();
    $this->view->assign('rate_url', $front_router->assemble(array('module' => 'rate'), 'widget_rate'));

    //$this->view->maxRate = $settings->getSetting('rate.' . $item_type . '.max.rate', 5);
    $this->view->maxRate = 5; // todo change stars count
    $maxItems = $settings->getSetting('rate.' . $item_type . '.max.items', 5);
    $minVotes = $settings->getSetting('rate.' . $item_type . '.min.votes', 1);
    $this->view->period = $period = $settings->getSetting('rate.' . $item_type . '.period_enabled', true);
    $mostRatedItems = $table->fetchMostRated($item_type, $maxItems, $minVotes);

    if (empty($mostRatedItems)) {
        return $this->setNoRender();
    }
    $this->view->all_rates = $this->_prepareRates($mostRatedItems);

    if ($period) {
        $this->view->month_rates = $this->_prepareRates($table->fetchMostRated($item_type, $maxItems, $minVotes, 'month'));
        $this->view->week_rates = $this->_prepareRates($table->fetchMostRated($item_type, $maxItems, $minVotes, 'week'));
    }

    $usersTbl = Engine_Api::_()->getDbTable('users','user');
    $select = $usersTbl->select()->where('user_id IN (?)', $this->item_ids);
    $items = $usersTbl->fetchAll($select);
    $this->view->items = array();
    foreach ($items as $item) {
        $this->view->items[$item->getIdentity()] = $item;
    }
    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('rate');
    $path = dirname($path) . '/views/scripts';
    $this->view->addScriptPath($path);

    $this->getElement()->setAttrib('class', 'rate_widget_theme_' . $this->view->activeTheme());
}

    private function _prepareRates($rates)
    {
        $can_rate = $this->_getParam('can_rate', true);
        $error_msg = $this->_getParam('error_msg', '');


        if (!$rates) {
            return array();
        }

        $items = array();

        foreach ($rates as $rate) {
            $rate['uid'] = 'rate_uid_'.$rate['object_id'];
            $items[$rate['object_id']] = $rate;
            $items[$rate['object_id']]['item_score'] = ($rate['total_score'] && $rate['rate_count'])
                ? $rate['total_score'] / $rate['rate_count']
                : 0;

            $this->item_ids[] = $rate['object_id'];



                }
//        print_r('<pre>');
//        print_r($items[7]);
//        print_r('</pre>');
//        print_die(1);





        //  $settings = Engine_Api::_()->getApi('settings', 'core');
        //  $this->view->maxRate = $settings->getSetting('rate.' . $subject . '.max.rate', 5);
         // todo edit stars count






        $this->view->can_rate = Zend_Json::encode(array('can_rate' => $can_rate, 'error_msg' => $error_msg));



        return $items;
       }
}