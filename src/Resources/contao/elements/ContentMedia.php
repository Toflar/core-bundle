<?php

/*
 * This file is part of Contao.
 *
 * (c) Leo Feyer
 *
 * @license LGPL-3.0-or-later
 */

namespace Contao;


/**
 * Content element "mediaelement".
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class ContentMedia extends \ContentElement
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'ce_player';

	/**
	 * Files object
	 * @var Model\Collection|FilesModel
	 */
	protected $objFiles;


	/**
	 * Return if there are no files
	 *
	 * @return string
	 */
	public function generate()
	{
		if ($this->playerSRC == '')
		{
			return '';
		}

		$source = \StringUtil::deserialize($this->playerSRC);

		if (empty($source) || !\is_array($source))
		{
			return '';
		}

		$objFiles = \FilesModel::findMultipleByUuidsAndExtensions($source, array('mp4', 'm4v', 'mov', 'wmv', 'webm', 'ogv', 'm4a', 'mp3', 'wma', 'mpeg', 'wav', 'ogg'));

		if ($objFiles === null)
		{
			return '';
		}

		// Display a list of files in the back end
		if (TL_MODE == 'BE')
		{
			$return = '<ul>';

			while ($objFiles->next())
			{
				$objFile = new \File($objFiles->path);
				$return .= '<li>' . \Image::getHtml($objFile->icon, '', 'class="mime_icon"') . ' <span>' . $objFile->name . '</span> <span class="size">(' . $this->getReadableSize($objFile->size) . ')</span></li>';
			}

			return $return . '</ul>';
		}

		$this->objFiles = $objFiles;

		return parent::generate();
	}


	/**
	 * Generate the module
	 */
	protected function compile()
	{
		/** @var PageModel $objPage */
		global $objPage;

		$this->Template->poster = false;

		// Optional poster
		if ($this->posterSRC != '')
		{
			if (($objFile = \FilesModel::findByUuid($this->posterSRC)) !== null)
			{
				$this->Template->poster = $objFile->path;
			}
		}

		$objFiles = $this->objFiles;

		/** @var FilesModel $objFirst */
		$objFirst = $objFiles->current();

		// Pre-sort the array by preference
		if (\in_array($objFirst->extension, array('mp4', 'm4v', 'mov', 'wmv', 'webm', 'ogv')))
		{
			$this->Template->isVideo = true;
			$arrFiles = array('mp4'=>null, 'm4v'=>null, 'mov'=>null, 'wmv'=>null, 'webm'=>null, 'ogv'=>null);
		}
		else
		{
			$this->Template->isVideo = false;
			$arrFiles = array('m4a'=>null, 'mp3'=>null, 'wma'=>null, 'mpeg'=>null, 'wav'=>null, 'ogg'=>null);
		}

		$objFiles->reset();

		// Convert the language to a locale (see #5678)
		$strLanguage = str_replace('-', '_', $objPage->language);

		// Pass File objects to the template
		while ($objFiles->next())
		{
			$arrMeta = \StringUtil::deserialize($objFiles->meta);

			if (\is_array($arrMeta) && isset($arrMeta[$strLanguage]))
			{
				$strTitle = $arrMeta[$strLanguage]['title'];
			}
			else
			{
				$strTitle = $objFiles->name;
			}

			$objFile = new \File($objFiles->path);
			$objFile->title = \StringUtil::specialchars($strTitle);

			$arrFiles[$objFile->extension] = $objFile;
		}

		$size = \StringUtil::deserialize($this->playerSize);

		if (!\is_array($size) || empty($size[0]) || empty($size[1]))
		{
			if ($this->Template->isVideo)
			{
				$this->Template->size = ' width="640" height="360"';
			}
			else
			{
				$this->Template->size = ' width="400" height="40"';
			}
		}
		else
		{
			$this->Template->size = ' width="' . $size[0] . '" height="' . $size[1] . '"';
		}

		$this->Template->files = array_values(array_filter($arrFiles));
		$this->Template->autoplay = $this->autoplay;
	}
}
