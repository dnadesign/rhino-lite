<?php

/**
 * Provides build information so we can see at a glance what version a remote
 * installation is running.
 *
 * @package rhino
 */
class RhinoVersionExtension extends Extension {

	public function getRhinoVersion() {
		return RHINO_VERSION;
	}

	public function getRhinoBuild() {
		$base = BASE_PATH;
		
		return exec("cd $base && git rev-parse --short=5 HEAD 2>/dev/null");
	}

	public function IsLive() {
		return Director::isLive();
	}
}