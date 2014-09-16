<?php
	
	/**
	 * Defines which version of the Mango Framework should be
	 * used.
	 *
	 * This can either be set to a specific version like '1.0'
	 * or to a system version like 'latest' or 'current'.
	 *
	 * This value is used when locating the library folder
	 * and other symbolic link names you create can also
	 * be used.
	 */
	$MANGO_VERSION = 'current';
	
	// import packaging system
	require_once("library/system/$MANGO_VERSION/packages.php");
	
?>