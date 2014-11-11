<?php

	// Force APC in CLI mode
	ini_set('apc.enabled', 1);
	ini_set('apc.enable_cli', 1);

	// Make sure we have enough time to finish
	set_time_limit(0);
	ini_set('max_execution_time', 0);

	if (!defined('HC_SKIP_LOCK_CHECK')) {
		define('HC_SKIP_LOCK_CHECK', true);
	}

	if (!defined('HC_VERSION')) {
        require_once __DIR__ . '/../core/HydraCore.php';
	}

    $cwd = getcwd();
    chdir(__DIR__);
	$process = new HC\Hooks\PreReceive();
	$process->run();
    chdir($cwd);