<?php

class OldURLCsvBulkLoader extends CsvBulkLoader
{
	protected function processRecord($record, $columnMap, &$results, $preview = false) {
		$siteTreeID = null;

		if (!empty($record['NewURL'])) { //redirect to an existing page
			if ($page = SiteTree::get_by_link($record['NewURL'])) {
				$siteTreeID = $page->ID;
			} else { //custom url redirect
				$redirect = $this->createRedirect(array(
					'OldURL' => $record['OldURL'],
					'RedirectType' => 'Custom',
					'RedirectTo' => $record['NewURL']
				));
				$results->addCreated($redirect);
				return $redirect->ID;
			}
		} else if (!empty($record['PageID'])) { //pass in page id directly
			$siteTreeID = $record['PageID'];
		} else {
			return false;
		}

		//check for an existing record
		$existing = OldURLRedirect::get()->filter(array(
			'OldURL' => $record['OldURL'],
			'PageID' => $siteTreeID
		))->first();

		if (!$existing) {
			$redirect = $this->createRedirect(array(
				'OldURL' => $record['OldURL'],
				'PageID' => $siteTreeID
			));

			$results->addCreated($redirect);

			return $redirect->ID;
		} else {
			$results->addUpdated($existing);

			return $existing->ID;
		}
	}

	protected function createRedirect($data) {
		$redirect = OldURLRedirect::create();
		$redirect->update($data);
		$redirect->write();

		return $redirect;
	}
}