<?php
/**
 * @package		J2XML
 * @subpackage	lib_j2xml
 *
 * @author		Helios Ciancio <info@eshiol.it>
 * @link		http://www.eshiol.it
 * @copyright	Copyright (C) 2010 - 2019 Helios Ciancio. All Rights Reserved
 * @license		http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL v3
 * J2XML is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

namespace eshiol\J2XML\Table;

defined('JPATH_PLATFORM') or die;

use eshiol\J2XML\Table\Category;
use eshiol\J2XML\Table\Image;
use eshiol\J2XML\Table\Table;
use eshiol\J2XML\Table\Tag;
use eshiol\J2XML\Table\User;



//use Joomla\Database\DatabaseDriver;
use Joomla\Registry\Registry;

\JLoader::import('eshiol.j2xml.Table.Category');
\JLoader::import('eshiol.j2xml.Table.Image');
\JLoader::import('eshiol.j2xml.Table.Table');
\JLoader::import('eshiol.j2xml.Table.Tag');
\JLoader::import('eshiol.j2xml.Table.User');

/**
 * Contact Table
 * 
 * @author		Helios Ciancio
 *
 * @version		18.8.301
 * @since 		15.9.261
 */
class Contact extends Table
{
	/**
	 * Constructor
	 *
	 * @param	\JDatabaseDriver  $db  A database connector object
	 *
	 * @since	15.9.261
	 */
	public function __construct(\JDatabaseDriver $db)
	{
		\JLog::add(new \JLogEntry(__METHOD__, \JLog::DEBUG, 'lib_j2xml'));

		parent::__construct('#__contact_details', 'id', $db);

		$this->type_alias = 'com_contact.contact';
	}

	/**
	 * Export item list to xml
	 *
	 * @access public
	 * @since 15.9.261
	 */
	function toXML($mapKeysToText = false)
	{
		\JLog::add(new \JLogEntry(__METHOD__, \JLog::DEBUG, 'lib_j2xml'));

		// $this->_aliases['user_id']='SELECT username FROM #__users WHERE id = '.(int)$this->user_id;
		$this->_aliases['user_id'] = (string) $this->_db->getQuery(true)
			->select($this->_db->qn('username'))
			->from($this->_db->qn('#__users'))
			->where($this->_db->qn('id') . ' = ' . (int) $this->user_id);
		\JLog::add(new \JLogEntry($this->_aliases['user_id'], \JLog::DEBUG, 'lib_j2xml'));

		// $this->_aliases['tag']='SELECT t.path FROM #__tags t, #__contentitem_tag_map m WHERE type_alias = "com_contact.contact" AND t.id = m.tag_id AND m.content_item_id = '. (int)$this->id;
		$this->_aliases['tag'] = (string) $this->_db->getQuery(true)
			->select($this->_db->qn('t.path'))
			->from($this->_db->qn('#__tags', 't'))
			->from($this->_db->qn('#__contentitem_tag_map', 'm'))
			->where($this->_db->qn('type_alias') . ' = ' . $this->_db->q($this->type_alias))
			->where($this->_db->qn('t.id') . ' = ' . $this->_db->qn('m.tag_id'))
			->where($this->_db->qn('m.content_item_id') . ' = ' . $this->_db->q((string) $this->id));
		\JLog::add(new \JLogEntry($this->_aliases['tag'], \JLog::DEBUG, 'lib_j2xml'));

		return parent::_serialize();
	}

	/**
	 * Export data
	 *
	 * @param	int					$id		the id of the item to be exported
	 * @param	\SimpleXMLElement	$xml	xml
	 * @param	array	$options
	 *
	 * @throws
	 * @return	void
	 * @access	public
	 *
	 * @since	18.8.310
	 */
	public static function export($id, &$xml, $options)
	{
		\JLog::add(new \JLogEntry(__METHOD__, \JLog::DEBUG, 'lib_j2xml'));
		\JLog::add(new \JLogEntry('id: ' . $id, \JLog::DEBUG, 'lib_j2xml'));
		\JLog::add(new \JLogEntry('options: ' . print_r($options, true), \JLog::DEBUG, 'lib_j2xml'));

		if ($xml->xpath("//j2xml/contact/id[text() = '" . $id . "']"))
		{
			return;
		}

		$db			= \JFactory::getDbo();
		$item		= new Contact($db);
		if (!$item->load($id))
		{
			return;
		}

		$doc		= dom_import_simplexml($xml)->ownerDocument;
		$fragment	= $doc->createDocumentFragment();

		$fragment->appendXML($item->toXML());
		$doc->documentElement->appendChild($fragment);

		if ($options['users'])
		{
			if ($item->created_by)
			{
				User::export($item->created_by, $xml, $options);
			}
			if ($item->modified_by)
			{
				User::export($item->modified_by, $xml, $options);
			}
		}

		if ($options['images'])
		{
			if (isset($item->image))
			{
				Image::export($item->image, $xml, $options);
			}
		}

		$htags = new \JHelperTags();
		$itemtags = $htags->getItemTags('com_contact.contact', $id);
		foreach ($itemtags as $itemtag)
		{
			Tag::export($itemtag->tag_id, $xml, $options);
		}

		if ($options['categories'] && ($item->catid > 0))
		{
			Category::export($item->catid, $xml, $options);
		}
	}
}