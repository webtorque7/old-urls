<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 7
 * Date: 27/05/13
 * Time: 11:18 AM
 * To change this template use File | Settings | File Templates.
 */

class OldURL_Extension extends DataExtension {

        private static $has_many = array(
                'OldURLRedirects' => 'OldURLRedirect'
        );

        public function updateSettingsFields($fields) {
                $fields->addFieldToTab('Root.OldUrls', new GridField('OldURLRedirects', 'Old URLs', $this->owner->OldURLRedirects(), GridFieldConfig_RelationEditor::create()));
        }
}
