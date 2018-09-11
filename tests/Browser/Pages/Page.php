<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Page as BasePage;

abstract class Page extends BasePage {
	/**
	 * Get the global element shortcuts for the site.
	 *
	 * @return array
	 */
	public static function siteElements() {
		return [
			'@chatMessages' => '.chat__messages',
			'@firstChatMessage' => '.chat__messages .chat__message:first-child',
			'@ownMessage' => '.chat__message--own',
			'@onlineList' => '.users',
		];
	}
}
