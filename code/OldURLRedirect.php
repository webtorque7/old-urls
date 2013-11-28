<?php
/**
 * Class for storing redirects to old urls
 */

class OldURLRedirect extends DataObject {

        public static $singular_name = 'URL Redirect';
        public static $plural_name = 'URL Redirects';

        public static $db = array(
                'OldURL' => 'Varchar(255)',
                'Anchor' => 'Varchar(50)',
                'Action' => 'Varchar(100)'
        );

        public static $summary_fields = array(
                'OldURL' => 'Old URL',
                'Page.Link' => 'New URL'
        );

        public static $has_one = array(
                'Page' => 'SiteTree'
        );

        public function getCMSFields() {
                $fields = parent::getCMSFields();

                $fields->removeByName('PageID');

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
}
