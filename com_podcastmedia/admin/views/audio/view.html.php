<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmedia
 *
 * @copyright   Copyright (C) 2011-2012 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * HTML View class for the Podcast Media component
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmedia
 * @since       1.6
 */
class PodcastMediaViewAudio extends JView
{
	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @since   1.6
	 */
	function display($tpl = null)
	{
		$medmanparams = JComponentHelper::getParams('com_media');
		$lang = JFactory::getLanguage();

		/*
		 * Display form for FTP credentials?
		 * Don't set them here, as there are other functions called before this one if there is any file write operation
		 */
		$ftp = !JClientHelper::hasCredentials('ftp');

		$this->session = JFactory::getSession();
		$this->medmanparams = $medmanparams;
		$this->state = $this->get('state');
		$this->folderList = $this->get('folderList');
		$this->assign('require_ftp', $ftp);

		JHtml::_('behavior.framework', true);
		JHtml::script('administrator/components/com_podcastmedia/media/js/popup-audiomanager.js', false, false);
		JHtml::_('stylesheet', 'media/popup-imagemanager.css', array(), true);

		if ($lang->isRTL())
		{
			JHtml::_('stylesheet', 'media/popup-imagemanager_rtl.css', array(), true);
		}

		if ($medmanparams->get('enable_flash', 1))
		{
			$fileTypes = 'mp3,m4a,mov,mp4,m4v';
			$types = explode(',', $fileTypes);
			$displayTypes = ''; // this is what the user sees
			$filterTypes = ''; // this is what controls the logic
			$firstType = true;

			foreach ($types AS $type)
			{
				if (!$firstType)
				{
					$displayTypes .= ', ';
					$filterTypes .= '; ';
				}
				else
				{
					$firstType = false;
				}

				$displayTypes .= '*.' . $type;
				$filterTypes .= '*.' . $type;
			}

			$typeString = '{ \'' . JText::_('COM_PODCASTMEDIA_FILES', 'true') . ' (' . $displayTypes . ')\': \'' . $filterTypes . '\' }';

			JHtml::_(
				'behavior.uploader', 'upload-flash', array(
															'onBeforeStart' => 'function(){ Uploader.setOptions({url: document.id(\'uploadForm\').action + \'&folder=\' + document.id(\'imageForm\').folderlist.value}); }',
															'onComplete' => 'function(){ window.frames[\'audioframe\'].location.href = window.frames[\'audioframe\'].location.href; }',
															'targetURL' => '\\document.id(\'uploadForm\').action',
															'typeFilter' => $typeString,
															'fileSizeMax' => (int) ($medmanparams->get('upload_maxsize', 0) * 1024 * 1024)
														)
			);
		}

		parent::display($tpl);
	}
}
