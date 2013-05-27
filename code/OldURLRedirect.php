<?php
/**
 * Class for storing redirects to old urls
 */

class OldURLRedirect extends DataObject {

        public static $singular_name = 'URL Redirect';
        public static $plural_name = 'URL Redirects';

        public static $db = array(
                'OldURL' => 'Varchar(255)',
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

                return $fields;
        }
}