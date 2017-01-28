<?php
/**
 * @version		15.9.266 libraries/eshiol/j2xml/table/category.php
 * @package		J2XML
 * @subpackage	lib_j2xml
 * @since		1.5.1
 *
 * @author		Helios Ciancio <info@eshiol.it>
 * @link		http://www.eshiol.it
 * @copyright	Copyright (C) 2010, 2017 Helios Ciancio. All Rights Reserved
 * @license		http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL v3
 * J2XML is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
 
// no direct access
defined('_JEXEC') or die('Restricted access.');

class eshTableCategory extends eshTable
{
	/**
	* @param database A database connector object
	*/
	function __construct(&$db)
	{
		parent::__construct('#__categories', 'id', $db);
	}

	/**
	 * Export item list to xml
	 *
	 * @access public
	 */
	function toXML($mapKeysToText = false)
	{
		if (class_exists('JPlatform') && version_compare(JPlatform::RELEASE, '12', 'ge'))
			$this->_aliases['tag']='SELECT t.path FROM #__tags t, #__contentitem_tag_map m WHERE type_alias = "'.$this->extension.'.category" AND t.id = m.tag_id AND m.content_item_id = '. (int)$this->id;

		return $this->_serialize();
	}
}
