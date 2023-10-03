<?php

/*
 * This file is part of Contao.
 *
 * (c) Leo Feyer
 *
 * @license LGPL-3.0-or-later
 */

use Patchwork\Utf8;

/**
 * Add a log entry
 *
 * @param string $strMessage
 * @param string $strLog
 *
 * @deprecated Deprecated since Contao 4.0, to be removed in Contao 5.0.
 *             Use the logger service instead.
 */
function log_message($strMessage, $strLog=null)
{
	@trigger_error('Using log_message() has been deprecated and will no longer work in Contao 5.0. Use the logger service instead.', E_USER_DEPRECATED);

	if ($strLog === null)
	{
		$strLog = 'prod-' . date('Y-m-d') . '.log';
	}

	$strLogsDir = null;

	if (($container = System::getContainer()) !== null)
	{
		$strLogsDir = $container->getParameter('kernel.logs_dir');
	}

	if (!$strLogsDir)
	{
		$strLogsDir = TL_ROOT . '/var/logs';
	}

	error_log(sprintf("[%s] %s\n", date('d-M-Y H:i:s'), $strMessage), 3, $strLogsDir . '/' . $strLog);
}

/**
 * Scan a directory and return its files and folders as array
 *
 * @param string  $strFolder
 * @param boolean $blnUncached
 *
 * @return array
 */
function scan($strFolder, $blnUncached=false)
{
	global $arrScanCache;

	// Add a trailing slash
	if (substr($strFolder, -1, 1) != '/')
	{
		$strFolder .= '/';
	}

	// Load from cache
	if (!$blnUncached && isset($arrScanCache[$strFolder]))
	{
		return $arrScanCache[$strFolder];
	}

	$arrReturn = array();

	// Scan directory
	foreach (scandir($strFolder) as $strFile)
	{
		if ($strFile == '.' || $strFile == '..')
		{
			continue;
		}

		$arrReturn[] = $strFile;
	}

	// Cache the result
	if (!$blnUncached)
	{
		$arrScanCache[$strFolder] = $arrReturn;
	}

	return $arrReturn;
}

/**
 * Convert special characters to HTML entities and make sure that
 * entities are never double converted.
 *
 * @param string  $strString
 * @param boolean $blnStripInsertTags
 *
 * @return string
 */
function specialchars($strString, $blnStripInsertTags=false)
{
	@trigger_error('Using specialchars() has been deprecated and will no longer work in Contao 5.0. Use StringUtil::specialchars() instead.', E_USER_DEPRECATED);

	if ($blnStripInsertTags)
	{
		$strString = strip_insert_tags($strString);
	}

	// Use ENT_COMPAT here (see #4889)
	return htmlspecialchars($strString, ENT_COMPAT, $GLOBALS['TL_CONFIG']['characterSet'], false);
}

/**
 * Standardize a parameter (strip special characters and convert spaces)
 *
 * @param string  $strString
 * @param boolean $blnPreserveUppercase
 *
 * @return string
 */
function standardize($strString, $blnPreserveUppercase=false)
{
	@trigger_error('Using standardize() has been deprecated and will no longer work in Contao 5.0. Use StringUtil::standardize() instead.', E_USER_DEPRECATED);

	$arrSearch = array('/[^\pN\pL \.\&\/_-]+/u', '/[ \.\&\/-]+/');
	$arrReplace = array('', '-');

	$strString = html_entity_decode($strString, ENT_QUOTES, $GLOBALS['TL_CONFIG']['characterSet']);
	$strString = strip_insert_tags($strString);
	$strString = preg_replace($arrSearch, $arrReplace, $strString);

	if (is_numeric(substr($strString, 0, 1)))
	{
		$strString = 'id-' . $strString;
	}

	if (!$blnPreserveUppercase)
	{
		$strString = Patchwork\Utf8::strtolower($strString);
	}

	return trim($strString, '-');
}

/**
 * Remove Contao insert tags from a string
 *
 * @param string $strString
 *
 * @return string
 */
function strip_insert_tags($strString)
{
	@trigger_error('Using strip_insert_tags() has been deprecated and will no longer work in Contao 5.0. Use StringUtil::stripInsertTags() instead.', E_USER_DEPRECATED);

	$count = 0;

	do
	{
		$strString = preg_replace('/\{\{[^\{\}]*\}\}/', '', $strString, -1, $count);
	}
	while ($count > 0);

	return $strString;
}

/**
 * Return an unserialized array or the argument
 *
 * @param mixed   $varValue
 * @param boolean $blnForceArray
 *
 * @return mixed
 */
function deserialize($varValue, $blnForceArray=false)
{
	@trigger_error('Using deserialize() has been deprecated and will no longer work in Contao 5.0. Use StringUtil::deserialize() instead.', E_USER_DEPRECATED);

	// Already an array
	if (\is_array($varValue))
	{
		return $varValue;
	}

	// Null
	if ($varValue === null)
	{
		return $blnForceArray ? array() : null;
	}

	// Not a string
	if (!\is_string($varValue))
	{
		return $blnForceArray ? array($varValue) : $varValue;
	}

	// Empty string
	if (trim($varValue) == '')
	{
		return $blnForceArray ? array() : '';
	}

	// Potentially including an object (see #6724)
	if (preg_match('/[OoC]:\+?[0-9]+:"/', $varValue))
	{
		trigger_error('The deserialize() function does not allow serialized objects', E_USER_WARNING);

		return $blnForceArray ? array($varValue) : $varValue;
	}

	$varUnserialized = @unserialize($varValue, array('allowed_classes' => false));

	if (\is_array($varUnserialized))
	{
		$varValue = $varUnserialized;
	}
	elseif ($blnForceArray)
	{
		$varValue = array($varValue);
	}

	return $varValue;
}

/**
 * Split a string into fragments, remove whitespace and return fragments as array
 *
 * @param string $strPattern
 * @param string $strString
 *
 * @return array
 */
function trimsplit($strPattern, $strString)
{
	@trigger_error('Using trimsplit() has been deprecated and will no longer work in Contao 5.0. Use StringUtil::trimsplit() instead.', E_USER_DEPRECATED);

	global $arrSplitCache;

	$strKey = md5($strPattern.$strString);

	// Load from cache
	if (isset($arrSplitCache[$strKey]))
	{
		return $arrSplitCache[$strKey];
	}

	// Split
	if (\strlen($strPattern) == 1)
	{
		$arrFragments = array_map('trim', explode($strPattern, $strString));
	}
	else
	{
		$arrFragments = array_map('trim', preg_split('/'.$strPattern.'/ui', $strString));
	}

	// Empty array
	if (\count($arrFragments) < 2 && !\strlen($arrFragments[0]))
	{
		$arrFragments = array();
	}

	$arrSplitCache[$strKey] = $arrFragments;

	return $arrFragments;
}

/**
 * Convert all ampersands into their HTML entity (default) or unencoded value
 *
 * @param string  $strString
 * @param boolean $blnEncode
 *
 * @return string
 */
function ampersand($strString, $blnEncode=true)
{
	return preg_replace('/&(amp;)?/i', ($blnEncode ? '&amp;' : '&'), $strString);
}

/**
 * Replace line breaks with HTML5-style <br> tags
 *
 * @param string  $str
 * @param boolean $xhtml
 *
 * @return string
 */
function nl2br_html5($str, $xhtml=false)
{
	return nl2br($str, $xhtml);
}

/**
 * Replace line breaks with XHTML-style <br /> tags
 *
 * @param string $str
 *
 * @return string
 */
function nl2br_xhtml($str)
{
	return nl2br($str);
}

/**
 * Replace line breaks with <br> tags preserving preformatted text
 *
 * @param string  $str
 * @param boolean $xhtml
 *
 * @return string
 */
function nl2br_pre($str, $xhtml=false)
{
	$str = $xhtml ? nl2br_xhtml($str) : nl2br_html5($str);

	if (stripos($str, '<pre') === false)
	{
		return $str;
	}

	$chunks = array();
	preg_match_all('/<pre[^>]*>.*<\/pre>/Uis', $str, $chunks);

	foreach ($chunks as $chunk)
	{
		$str = str_replace($chunk, str_ireplace(array('<br>', '<br />'), '', $chunk), $str);
	}

	return $str;
}

/**
 * Compare two file names using a case insensitive "natural order" algorithm
 *
 * @param string $a
 * @param string $b
 *
 * @return integer
 */
function basename_natcasecmp($a, $b)
{
	return strnatcasecmp(basename($a), basename($b));
}

/**
 * Compare two file names using a case insensitive, reverse "natural order" algorithm
 *
 * @param string $a
 * @param string $b
 *
 * @return integer
 */
function basename_natcasercmp($a, $b)
{
	return -strnatcasecmp(basename($a), basename($b));
}

/**
 * Sort an array by keys using a case insensitive "natural order" algorithm
 *
 * @param array $arrArray
 *
 * @return array
 */
function natcaseksort($arrArray)
{
	$arrBuffer = array_flip($arrArray);
	natcasesort($arrBuffer);
	$arrBuffer = array_flip($arrBuffer);

	return $arrBuffer;
}

/**
 * Compare two values based on their length (ascending)
 *
 * @param integer $a
 * @param integer $b
 *
 * @return integer
 */
function length_sort_asc($a, $b)
{
	return \strlen($a) - \strlen($b);
}

/**
 * Compare two values based on their length (descending)
 *
 * @param integer $a
 * @param integer $b
 *
 * @return integer
 */
function length_sort_desc($a, $b)
{
	return \strlen($b) - \strlen($a);
}

/**
 * Insert a parameter or array into an existing array at a particular index
 *
 * @param array   $arrCurrent
 * @param integer $intIndex
 * @param mixed   $arrNew
 */
function array_insert(&$arrCurrent, $intIndex, $arrNew)
{
	if (!\is_array($arrCurrent))
	{
		$arrCurrent = $arrNew;

		return;
	}

	if (\is_array($arrNew))
	{
		$arrBuffer = array_splice($arrCurrent, 0, $intIndex);
		$arrCurrent = array_merge_recursive($arrBuffer, $arrNew, $arrCurrent);

		return;
	}

	array_splice($arrCurrent, $intIndex, 0, $arrNew);
}

/**
 * Duplicate a particular element of an array
 *
 * @param array   $arrStack
 * @param integer $intIndex
 *
 * @return array
 *
 * @deprecated Deprecated since Contao 4.3, to be removed in Contao 5.0.
 */
function array_duplicate($arrStack, $intIndex)
{
	@trigger_error('Using array_duplicate() has been deprecated and will no longer work in Contao 5.0.', E_USER_DEPRECATED);

	$arrBuffer = $arrStack;
	$arrStack = array();

	for ($i=0; $i<=$intIndex; $i++)
	{
		$arrStack[] = $arrBuffer[$i];
	}

	for ($i=$intIndex, $c=\count($arrBuffer); $i<$c; $i++)
	{
		$arrStack[] = $arrBuffer[$i];
	}

	return $arrStack;
}

/**
 * Move an array element one position up
 *
 * @param array   $arrStack
 * @param integer $intIndex
 *
 * @return array
 *
 * @deprecated Deprecated since Contao 4.3, to be removed in Contao 5.0.
 */
function array_move_up($arrStack, $intIndex)
{
	@trigger_error('Using array_move_up() has been deprecated and will no longer work in Contao 5.0.', E_USER_DEPRECATED);

	if ($intIndex > 0)
	{
		$arrBuffer = $arrStack[$intIndex];
		$arrStack[$intIndex] = $arrStack[($intIndex-1)];
		$arrStack[($intIndex-1)] = $arrBuffer;
	}
	else
	{
		$arrStack[] = $arrStack[$intIndex];
		array_shift($arrStack);
	}

	return $arrStack;
}

/**
 * Move an array element one position down
 *
 * @param array   $arrStack
 * @param integer $intIndex
 *
 * @return array
 *
 * @deprecated Deprecated since Contao 4.3, to be removed in Contao 5.0.
 */
function array_move_down($arrStack, $intIndex)
{
	@trigger_error('Using array_move_down() has been deprecated and will no longer work in Contao 5.0.', E_USER_DEPRECATED);

	if (($intIndex+1) < \count($arrStack))
	{
		$arrBuffer = $arrStack[$intIndex];
		$arrStack[$intIndex] = $arrStack[($intIndex+1)];
		$arrStack[($intIndex+1)] = $arrBuffer;
	}
	else
	{
		array_unshift($arrStack, $arrStack[$intIndex]);
		array_pop($arrStack);
	}

	return $arrStack;
}

/**
 * Delete a particular element of an array
 *
 * @param array   $arrStack
 * @param integer $intIndex
 *
 * @return array
 *
 * @deprecated Deprecated since Contao 4.3, to be removed in Contao 5.0.
 */
function array_delete($arrStack, $intIndex)
{
	@trigger_error('Using array_delete() has been deprecated and will no longer work in Contao 5.0.', E_USER_DEPRECATED);

	unset($arrStack[$intIndex]);

	return array_values($arrStack);
}

/**
 * Return true if an array is associative
 *
 * @param array $arrArray
 *
 * @return boolean
 */
function array_is_assoc($arrArray)
{
	return \is_array($arrArray) && array_keys($arrArray) !== range(0, \count($arrArray) - 1);
}

/**
 * Replace line breaks with <br> tags (to be used with preg_replace_callback)
 *
 * @param array $matches
 *
 * @return string
 *
 * @deprecated Deprecated since Contao 4.0, to be removed in Contao 5.0.
 */
function nl2br_callback($matches)
{
	@trigger_error('Using nl2br_callback() has been deprecated and will no longer work in Contao 5.0.', E_USER_DEPRECATED);

	return str_replace("\n", '<br>', $matches[0]);
}
