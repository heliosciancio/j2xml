<?php
/**
 * @version		17.1.296 libraries/eshiol/j2xml/table/module.php
 * @package		J2XML
 * @subpackage	lib_j2xml
 * @since		17.1.296
 *
 * @author		Helios Ciancio <info@eshiol.it>
 * @link		http://www.eshiol.it
 * @copyright	Copyright (C) 2010, 2018 Helios Ciancio. All Rights Reserved
 * @license		http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL v3
 * J2XML is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
* Menu Table class
* 
* @since 17.1.296
*/
class eshTableModule extends eshTable
{
	/**
	 * Constructor
	 * 
	 * @param object $db	Database connector
	 * 
	 * @since 17.1.294
	 */
	function __construct(&$db) {
		parent::__construct('#__modules', 'id', $db);
	}

	/**
	 * 
	 * {@inheritDoc}
	 * @see eshTable::toXML()
	 */
	function toXML($mapKeysToText = false)
	{
		JLog::add(new JLogEntry(__METHOD__, JLOG::DEBUG, 'lib_j2xml'));
		$this->_aliases['menu'] = "SELECT CONCAT(m.menutype, '/', m.path) FROM `#__modules_menu` mm INNER JOIN `#__menu` m ON mm.menuid = m.id WHERE mm.moduleid = ".(int)$this->id;

		return parent::_serialize();
	}
}
