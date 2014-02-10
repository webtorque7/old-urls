<?php
/**
 * Class for storing redirects to old urls
 */

class OldURLRedirect extends DataObject {

        private static $singular_name = 'URL Redirect';
        private static $plural_name = 'URL Redirects';

        private static $db = array(
                'OldURL' => 'Varchar(255)',
                'Anchor' => 'Varchar(50)',
                'Action' => 'Varchar(100)',
		'DontRedirect' => 'Boolean'
        );

        private static $summary_fields = array(
                'OldURL' => 'Old URL',
                'Page.Link' => 'New URL',
		'DontRedirect' => 'Dont Redirect'
        );

        private static $has_one = array(
                'Page' => 'SiteTree'
        );

        public function getCMSFields() {
                $fields = parent::getCMSFields();

                //$fields->removeByName('PageID');

                $fields->addFieldsToTab('Root.Main', array(
                        TextField::create('Action')->setDescription('Action part of the url your are redirecting to e.g. /checkout/options (options is the Action)'),
                        TextField::create('Anchor')->setDescription('Anchor on the page to redirect to e.g. for anchor #bottom enter bottom (don\'t enter the hash)')
                ));

                return $fields;
        }

	public function canView($member = null) {
		return true;
	}

	public function canEdit($member = null) {
		return true;
	}

	public function canCreate($member = null) {
		return true;
	}

	public function canDelete($member = null) {
		return true;
	}


        /**
         * @param Controller $controller
         */
        public function redirection(Controller $controller) {
                $redirect = Controller::join_links($this->Page()->Link(), $this->Action, $this->Anchor ? '#' . $this->Anchor : '' );

                return $controller->redirect($redirect);
        }

	public static function get_from_url($url) {
		$url = $url ? $url : (!empty($_GET['url']) ? $_GET['url'] : '');

		if ($url) {
			if (strpos($url, '/') !== 0)
				$url = '/' . $url;

			$oldPage = OldURLRedirect::get()->filter('OldURL', $url)->exclude('PageID', 0)->first();

			if ($oldPage && $url == $oldPage->OldURL) {
				return $oldPage;
			}
		}
	}
}
