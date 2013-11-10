<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 7
 * Date: 27/05/13
 * Time: 11:39 AM
 * To change this template use File | Settings | File Templates.
 */

class OldURL_ErrorPage extends ErrorPage
{
        private static $singular_name = 'Old URL Error Page';
        private static $plural_name = 'Old URL Error Pages';

        public function requireDefaultRecords() {
                parent::requireDefaultRecords();

                //change 404 page so we can redirect if necessary
                if ($errorPage = ErrorPage::get()->filter('ErrorCode', 404)->first()) {
                        $errorPage->ClassName = 'OldURL_ErrorPage';
                        $errorPage->write();
                        $errorPage->publish('Stage', 'Live');
                }
        }
}

class OldURL_ErrorPage_Controller extends ErrorPage_Controller
{
        public function init() {
                parent::init();


                $url = $this->request->getURL() ? $this->request->getURL() : (!empty($_GET['url']) ? $_GET['url'] : '');

                if ($url) {
                        if (strpos($url, '/') !== 0)
                                $url = '/' . $url;

                        $oldPage = OldURLRedirect::get()->filter('OldURL', $url)->first();

                        if ($oldPage) {
                                return $oldPage->redirection($this);
                        }
                }
        }

}
