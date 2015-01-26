<?php
class Sedo_TinyQuattro_Helper_BbCodes
{
    static $tableTag = null;


    public static function renderTagChildrenHelper(XenForo_BbCode_Formatter_Base $caller, array &$tagInfos, array $tag, array $rendererStates)
    {
        if (empty($tagInfos[$tag['tag']]))
        {
            // unknown tag, use default rendering
            $content = $caller->renderSubTree($tag['children'], $rendererStates);
        }
        else
        {
            $tagInfo = $tagInfos[$tag['tag']];   
            $wrapperPrefix = null;
            $wrapperSuffix = null;
            $content = '';
            foreach($tag['children'] as $row)
            {
                if (!isset($row['tag']))
                {
                    // raw content, wrap content and dump as a row
                    if (trim($row) == '')
                        continue;
                    if ($wrapperPrefix == null)
                    {
                        list($wrapperPrefix, $wrapperSuffix) = Sedo_TinyQuattro_Helper_BbCodes::getTagWrapper($tag['tag'], $tagInfos);
                    }
                    $content .= $wrapperPrefix . $row .  $wrapperSuffix ;
                }
                else if (!isset($tagInfo['allowedChildren']) || isset($tagInfo['allowedChildren'][$row['tag']]))
                {
                    $content .= $caller->renderSubTree(array($row), $rendererStates);    
                }
                else
                {
                    if ($wrapperPrefix == null)
                    {
                        list($wrapperPrefix, $wrapperSuffix) = Sedo_TinyQuattro_Helper_BbCodes::getTagWrapper($tag['tag'], $tagInfos);
                    }                
                    // tag is known to be in the wrong spot, wrap content and dump as a row
                    $content .=  $wrapperPrefix . $caller->renderTagUnparsed($row, $rendererStates) .  $wrapperSuffix ;
                }
            }
        } 
        return $content;
    }    

    protected static function getTagWrapperStack($limit, $tagName, array &$tagInfos, array &$tagStack)
    {
        if ($limit > 0 && isset($tagInfos[$tagName]['allowedChildren']))
        {
            reset($tagInfos[$tagName]['allowedChildren']);
            $firstChild = key($tagInfos[$tagName]['allowedChildren']);
            $tagStack[] = $firstChild;
            self::getTagWrapperStack($limit -1, $firstChild, $tagInfos, $tagStack);
        }
    }    
    
    public static function getTagWrapper($tagName, array &$tagInfos)
    {
        $tagStack = array();
        self::getTagWrapperStack(20, $tagName,$tagInfos, $tagStack);
        $prefix = '';
        $suffix = '';
        foreach($tagStack as $default)
        {
            $prefix = $prefix. '['.$default.']';
            $suffix = '[/'.$default.']'. $suffix;
        }
        return array($prefix,$suffix);
    }
    
	public static function getTableTags(XenForo_BbCode_Formatter_Base $caller, $tableTag = null)
	{
        if ($tableTag == null)
        {
            if (self::$tableTag === null)
            {
                $tableTag = Sedo_TinyQuattro_Helper_BbCodes::getQuattroBbCodeTagName('xtable');
            }
            else
            {
                $tableTag = self::$tableTag;
            }
        }
        return array(
					$tableTag => array(
						'callback' => array($caller, 'renderTagSedoXtable'),
						'stopLineBreakConversion' => false,
						'trimLeadingLinesAfter' => 2,
                        'allowedChildren' => array('thead' => 1, 'tbody' => 1, 'tfoot' => 1, 'colgroup' => 1, 'caption' => 1, 'tr' => 1),
					),
                    'thead' => array(
                        'callback'  => array($caller, 'renderTagSedoXtableSlaveTags'),
                        'allowedParents' => array($tableTag => 1),
                        'allowedChildren' => array('tr' => 1),
                    ),
                    'tbody' => array(
                        'callback'  => array($caller, 'renderTagSedoXtableSlaveTags'),
                        'allowedParents' => array($tableTag => 1),
                        'allowedChildren' => array('tr' => 1),
                    ),
                    'tfoot' => array(
                        'callback'  => array($caller, 'renderTagSedoXtableSlaveTags'),
                        'allowedParents' => array($tableTag => 1),
                        'allowedChildren' => array('tr' => 1),
                    ),
                    'colgroup' => array(
                        'callback'  => array($caller, 'renderTagSedoXtableSlaveTags'),
                        'allowedParents' => array($tableTag => 1),
                        'allowedChildren' => array('col' => 1),
                    ),
                    'caption' => array(
                        'callback'  => array($caller, 'renderTagSedoXtableSlaveTags'),
                        'allowedParents' => array($tableTag => 1),
                    ),
                    'tr' => array(
                        'callback'  => array($caller, 'renderTagSedoXtableSlaveTags'),
                        'allowedParents' => array($tableTag => 1, 'thead' => 1, 'tbody' => 1, 'tfoot' => 1),
                        'allowedChildren' => array('td' => 1, 'th' => 1),
                    ),
                    'col' => array(
                        'callback'  => array($caller, 'renderTagSedoXtableSlaveTags'),
                        'allowedParents' => array('colgroup' => 1),
                        'allowedChildren' => null
                    ),
                    'td' => array(
                        'callback'  => array($caller, 'renderTagSedoXtableSlaveTags'),
                        'allowedParents' => array('tr' => 1),
                        'allowedChildren' => null,
                    ),
                    'th' => array(
                        'callback'  => array($caller, 'renderTagSedoXtableSlaveTags'),
                        'allowedParents' => array('tr' => 1),
                        'allowedChildren' => null,
                    )
                );
    }


	/*
	 * Check and get custom tagNames for Quattro BbCodes
	 */
	public static function getQuattroBbCodeTagName($expectedTagName)
	{
		$xenOptions = XenForo_Application::get('options');
		
		switch($expectedTagName)
		{
			case "bcolor":
				$selectedTagName = $xenOptions->quattro_extra_bbcodes_bcolor_tag;
				break;
			case "table":
				$selectedTagName = $xenOptions->quattro_extra_bbcodes_xtable_tag;
				break;
			case "sub":
				$selectedTagName = $xenOptions->quattro_extra_bbcodes_sub_tag;
				break;
			case "sup":
				$selectedTagName = $xenOptions->quattro_extra_bbcodes_sup_tag;
				break;
			case "hr":
				$selectedTagName = $xenOptions->quattro_extra_bbcodes_hr_tag;
				break;
			case "anchor":
				$selectedTagName = $xenOptions->quattro_extra_bbcodes_anchor_tag;
				break;
			default:
				$selectedTagName = '';
		}
		
		if( 	strlen($selectedTagName) == 0
			||
			($expectedTagName == $selectedTagName)
			||
			preg_match('/[^a-z0-9_-]/i', $selectedTagName)
		)
		{
			return $expectedTagName;
		}
		
		return 	$selectedTagName;
	}

	/*
	 * Get XenForo options used for Mce Table
	 */
	public static function getMceTableXenOptions()
	{
		$xenOptions = XenForo_Application::get('options');
		return array(
			'tagName' => self::getQuattroBbCodeTagName('xtable'),
			'size' => array(
				'px' => array(
					'maxWidth' => $xenOptions->quattro_extra_bbcodes_xtable_maxwidth_px,
					'minWidth' => $xenOptions->quattro_extra_bbcodes_xtable_minwidth_px,
					'maxHeight' => $xenOptions->quattro_extra_bbcodes_xtable_maxheight_px,
					'minHeight' => $xenOptions->quattro_extra_bbcodes_xtable_minheight_px		
				),
				'percent' => array(
					'maxWidth' => $xenOptions->quattro_extra_bbcodes_xtable_maxwidth_percent,
					'minWidth' => $xenOptions->quattro_extra_bbcodes_xtable_minwidth_percent,
					'maxHeight' => $xenOptions->quattro_extra_bbcodes_xtable_maxheight_percent,
					'minHeight' => $xenOptions->quattro_extra_bbcodes_xtable_minheight_percent
				)
			),
			'cell' => array(
				'maxCellpadding'  => $xenOptions->quattro_extra_bbcodes_xtable_cellpadding_max,
				'maxCellspacing'  => $xenOptions->quattro_extra_bbcodes_xtable_cellspacing_max
			),
			'border' => array(
				'max'  => $xenOptions->quattro_extra_bbcodes_xtable_border_max
			)
		);
	}

	/*
	 * Map of allowed attributes & CSS by tag = NOT USED
	 */
	public static function getTableOptionsMap($parentTagName = 'xtable', $mergeCss = false)
	{
		$map = array(
			$parentTagName => array(
				'attributes' => array('align', 'bgcolor', 'border', 'cellpadding', 'cellspacing', 'width', 'height'),
				'css' => array('width', 'height', 'float', 'bgcolor', 'marginleft', 'marginright')
			),
			'thead' => array(
				'attributes' => array('align', 'valign'),
				'css' => array()
			),
			'tbody' => array(
				'attributes' => array('align', 'valign'),
				'css' => array()
			),
			'tfoot' => array(
				'attributes' => array('align', 'valign'),
				'css' => array()
			),
			'colgroup' => array(
				'attributes' => array('width', 'height', 'align', 'valign'),
				'css' => array('width', 'height', 'bgcolor')
			),
			'col' => array(
				'attributes' => array('width', 'height', 'align', 'valign', 'span'),
				'css' => array('width', 'height', 'bgcolor')
			),
			'caption' => array(
				'attributes' => array('align'),
				'css' => array('width', 'height', 'bgcolor', 'textalign')
			),
			'tr' => array(
				'attributes' => array('align', 'valign', 'bcolor'),
				'css' => array('width', 'height', 'bgcolor', 'textalign')
			),
			'th' => array(
				'attributes' => array('width', 'height', 'align', 'valign', 'bgcolor', 'colspan', 'nowrap', 'rowspan', 'scope'),
				'css' => array('width', 'height', 'bgcolor', 'textalign')
			),
			'td' => array(
				'attributes' => array('width', 'height', 'align', 'valign', 'bgcolor', 'colspan', 'nowrap', 'rowspan', 'scope'),
				'css' => array('width', 'height', 'bgcolor', 'textalign')
			)
		);
		
		if($mergeCss == false)
		{
			return $map;
		}
		
		foreach($map as &$tag)
		{
			$tag = array_merge($tag['attributes'], $tag['css']);
		}
		
		return $map;
	}
}
