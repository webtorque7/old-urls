<?php
/**
 * Model admin for managing old urls
 */
class OldURLAdmin extends ModelAdmin
{
    private static $managed_models = array('OldURLRedirect');

    private static $menu_title = 'Old URLs';

    private static $url_segment = 'old-urls';

    private static $model_importers = array('OldURLRedirect' => 'OldURLCsvBulkLoader');
}
