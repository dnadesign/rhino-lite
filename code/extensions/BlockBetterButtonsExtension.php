<?php

class BlockBetterButtonsExtension extends DataExtension {

	private static $better_buttons_enabled = false;

	public function onAfterWrite() {
		if (Versioned::current_stage() == 'Stage') {
			$this->owner->publish('Stage', 'Live');
		}
	}
}