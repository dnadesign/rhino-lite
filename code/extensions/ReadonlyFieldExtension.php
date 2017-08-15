<?php

/**
 * Readonly field has file in it's extraClass text, so we can test that it's there
 * and then update the template to link to the file rather than showing the url
 *
 * @package rhino
 */
class ReadonlyFieldExtension extends Extension {

	public function wasFile() {
		if (strpos($this->owner->extraClass(), 'file') !== false) {
			return true;
		}
		return false;
	}

	public function hasFile() {
		if (strpos($this->owner->Value(), '<i>(none)</i>') === false) {
			return true;
		}
		return false;
	}
}