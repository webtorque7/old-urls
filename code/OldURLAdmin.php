<?php

class OldURLAdmin extends ModelAdmin {
	private static $managed_models = array('OldURLRedirect');

	private static $menu_title = 'Old URLs';

	private static $url_segment = 'old-urls';
} 