<?php

/**
 * Class for storing redirects to old urls
 */
class OldURLRedirect extends DataObject
{

	private static $singular_name = 'URL Redirect';
	private static $plural_name = 'URL Redirects';

	private static $db = array(
		'RedirectType' => 'Enum("Internal,Custom", "Internal")',
		'OldURL' => 'Varchar(255)',
		'Anchor' => 'Varchar(50)',
		'Action' => 'Varchar(100)',
		'DontRedirect' => 'Boolean',
		'RedirectTo' => 'Varchar(255)'
	);

	private static $summary_fields = array(
		'OldURL' => 'Old URL',
		'RedirectionLink' => 'New URL',
		'DontRedirect' => 'Dont Redirect'
	);

	private static $has_one = array(
		'Page' => 'SiteTree'
	);

	public function getCMSFields() {
		$fields = FieldList::create(
			TabSet::create(
				"Root",
				Tab::create(
					"Main",
					TextField::create('OldURL', 'URL to redirect')->setDescription(
						'Don\'t include domain, e.g. /old/link/'
					),
					DropdownField::create(
						'RedirectType',
						'Redirect Type',
						$this->dbObject('RedirectType')->enumValues()
					),
					CompositeField::create(
						array(
							HeaderField::create('InternalHeader', 'Internal Redirect', 3),
							TreeDropdownField::create('PageID', 'Page', 'SiteTree'),
							TextField::create('Action')->setDescription(
								'Action part of the url your are redirecting to e.g. /checkout/options (options is the Action)'
							),
						//	CheckboxField::create('DontRedirect', 'Don\'t redirect (load on old page url)')
						)
					)->addExtraClass('internal-fields'),
					CompositeField::create(
						array(
							HeaderField::create('CustomHeader', 'Custom Redirect', 3),
							TextField::create('RedirectTo', 'Redirect To')
						)
					)->addExtraClass('custom-fields'),
					TextField::create('Anchor')->setDescription(
						'Anchor on the page to redirect to e.g. for anchor #bottom enter bottom (don\'t enter the hash)'
					)

				)
			)
		);

		Requirements::javascript(OLD_URLS_DIR . '/javascript/OldURLs.js');

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
	 * Get the link to redirect to
	 *
	 * @param Controller $controller
	 */
	public function getRedirectionLink() {
		$link = $this->RedirectType === 'Custom' ? $this->RedirectTo : $this->getInternalLink();

		if ($this->Anchor) {
			$link .= '#' . $this->Anchor;
		}

		return $link;
	}

	public function getInternalLink() {
		return Controller::join_links($this->Page()->Link(), $this->Action);
	}

	/**
	 * Lookup an OldURLRedirect page which matches the url
	 *
	 * @param $url
	 * @return DataObject|null
	 */
	public static function get_from_url($url) {
		$url = $url ? $url : (!empty($_GET['url']) ? $_GET['url'] : '');

		if ($url) {
			if (strpos($url, '/') !== 0) {
				$url = '/' . $url;
			}

			$SQL_url = Convert::raw2sql($url);
			$filter = <<<SQL
("OldURL" = '{$SQL_url}' AND "RedirectType" = 'Custom')
OR
("OldURL" = '{$SQL_url}' AND "RedirectType" = 'Internal' AND "PageID" <> 0)
SQL;


			$oldPage = OldURLRedirect::get()->where($filter)->first();

			if ($oldPage && $url == strtolower($oldPage->OldURL)) {
				return $oldPage;
			}
		}

		return null;
	}
}
