<?php
class Sedo_TinyQuattro_BbCode_Formatter_Wysiwyg extends XFCP_Sedo_TinyQuattro_BbCode_Formatter_Wysiwyg
{
	public function getTags()
	{
		$parentTags = parent::getTags();
		
		if(is_array($parentTags))
		{
			if(Sedo_TinyQuattro_Helper_Quattro::canUseQuattroBbCode('bcolor'))
			{
				$parentTags += array(
					'bcolor' => array(
						'hasOption' => true,
						'optionRegex' => '/^(rgb\(\s*\d+%?\s*,\s*\d+%?\s*,\s*\d+%?\s*\)|#[a-f0-9]{6}|#[a-f0-9]{3}|[a-z]+)$/i',
						'replace' => array('<span style="background-color: %s">', '</span>')
					)
				);			
			}

			if(Sedo_TinyQuattro_Helper_Quattro::canUseQuattroBbCode('justify'))
			{
				$parentTags += array(
					'justify' => array(
						'hasOption' => false,
						'callback' => array($this, 'renderTagAlign'),
						'trimLeadingLinesAfter' => 1,
					)
				);			
			
			}
		}
		
		return $parentTags;
	}
	
	public function filterFinalOutput($output)
	{
		$parent = parent::filterFinalOutput($output);

		if(!XenForo_Application::get('options')->get('quattro_parser_bb_to_wysiwyg'))
		{
			return $parent;
		}

		$emptyParaText = (XenForo_Visitor::isBrowsingWith('ie') ? '&nbsp;' : '<br />');

		//Fix Pacman effect with ol/ul with RTE editing
		$parent = preg_replace('#(</(ul|ol)>)\s</p>#', '$1<p>' . $emptyParaText . '</p>', $parent);

		//Fix for tabs (From DB to RTE editor && from Bb Code editor to rte Editor)
		$parent = preg_replace('#\t#', '&nbsp;&nbsp;&nbsp;&nbsp;', $parent);

		return $parent;
	}

	public function renderTagAlign(array $tag, array $rendererStates)
	{
		$parentOuput = parent::renderTagAlign($tag, $rendererStates);
		
		if(strtolower($tag['tag']) == 'justify' && Sedo_TinyQuattro_Helper_Quattro::canUseQuattroBbCode('justify'))
		{
			$text = $this->renderSubTree($tag['children'], $rendererStates);
			return $this->_wrapInHtml('<p style="text-align: ' . $tag['tag'] . '">', '</p>', $text) . "<break-start />\n";
		}
		
		return $parentOuput;
	}
}
//Zend_Debug::dump($parent);
