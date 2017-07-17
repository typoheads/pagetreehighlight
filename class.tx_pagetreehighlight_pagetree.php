<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2006 Bernhard Kraft <kraftb@kraftb.at>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Extended pagetree class which support highlighting of specific
 * data rows
 *
 * @author	Bernhard Kraft <kraftb@kraftb.at>
 * @author	Dev-Team Typoheads <dev@typoheads.at>
 */

include_once(PATH_typo3.'class.webpagetree.php');
require_once(t3lib_extMgm::extPath('pagetreehighlight').'class.tx_pagetreehighlight_cm1.php');

class tx_pagetreehighlight_pagetree extends webPageTree	{

	/**
	 * Getting the tree data: next entry
	 *
	 * @param	mixed		data handle
	 * @param	string		CSS class for sub elements (workspace related)
	 * @return	array		item data array OR FALSE if end of elements.
	 * @access private
	 * @see getDataInit()
	 */
	function getDataNext(&$res,$subCSSclass=''){
		if (is_array($this->data)) {
			if ($res<0) {
				$row=FALSE;
			} else {
				list(,$row) = each($this->dataLookup[$res][$this->subLevelID]);

					// Passing on default <td> class for subelements:
				if (is_array($row) && $subCSSclass!=='')	{
					$row['_CSSCLASS'] = $row['_SUBCSSCLASS'] = $subCSSclass;
				}
			}
			return $row;
		} else {
			$row = @$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			t3lib_BEfunc::workspaceOL($this->table, $row, $this->BE_USER->workspace);

				// Passing on default <td> class for subelements:
			if (is_array($row) && $subCSSclass!=='')	{

				if ($this->table==='pages' && $this->highlightPagesWithVersions && !isset($row['_CSSCLASS']) && count(t3lib_BEfunc::countVersionsOfRecordsOnPage($this->BE_USER->workspace, $row['uid'], TRUE)))	{
					$row['_CSSCLASS'] = 'ver-versions';
				}

				if (!isset($row['_CSSCLASS']))	{
					$row['_CSSCLASS'] = $subCSSclass;
				}
				if (!isset($row['_SUBCSSCLASS']))	{
					$row['_SUBCSSCLASS'] = $subCSSclass;
				}
			}
			if (is_array($row) && ($this->table=='pages') && $subCSSclass)	{
				$row['_CSSCLASS'] = $subCSSclass;
				$row['_SUBCSSCLASS'] = $subCSSclass;
			}
			if (is_array($row) && ($this->table=='pages') )	{
				if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['pagetreehighlight']['advancedHighlight'])	{
					$GLOBALS['SOBE']->doc->inDocStylesArray['tx-pagetreehighlight-hl'] = '
ul.tree ul li.active	{
	background-color: #fff;
	opacity: 0.7;
	filter: alpha(opacity=50);
	border: 1px solid #ccc;
}
ul.tree ul li.active div	{
	margin-left: -1px;
}
					';
				} else	{
					$GLOBALS['SOBE']->doc->inDocStylesArray['tx-pagetreehighlight-hl'] = '
.navFrameHL div	{
	background-color: #ebebeb;
}
					';
				}
				if (!$this->tx_pagetreehighlight_cm1)	{
					$this->tx_pagetreehighlight_cm1 = t3lib_div::makeInstance('tx_pagetreehighlight_cm1');
				}
				$this->tx_pagetreehighlight_cm1->tsConfig = t3lib_BEfunc::getModTSconfig($row['uid'], 'mod.tx_pagetreehighlight');
				list($pagepreset, $branchpreset) = $this->tx_pagetreehighlight_cm1->getStylePresets();
				if ( $GLOBALS['BE_USER']->uc['tx_pagetreehighlight']['page'][$row['uid']] || $GLOBALS['BE_USER']->uc['tx_pagetreehighlight']['branch'][$row['uid']] || $pagepreset || $branchpreset )	{
					$colors = $this->tx_pagetreehighlight_cm1->getSetColors('page', 1);
					if (($preset = $GLOBALS['BE_USER']->uc['tx_pagetreehighlight']['page'][$row['uid']]) && is_array($colors[$preset]))	{
						$row['_CSSCLASS'] = 'tx-pagetreehighlight-page-'.$preset;
						$GLOBALS['SOBE']->doc->inDocStylesArray['tx-pagetreehighlight-page-'.$preset] = $this->getRowStyle($colors[$preset], $preset, 'page');
					} elseif (is_array($pagepreset)&&count($pagepreset))	{
						$row['_CSSCLASS'] = 'tx-pagetreehighlight-page-preset-'.$row['uid'];
						$GLOBALS['SOBE']->doc->inDocStylesArray['tx-pagetreehighlight-page-preset-'.$row['uid']] = $this->getRowStyle($pagepreset, 'preset-'.$row['uid'], 'page');
					}
					$colors = $this->tx_pagetreehighlight_cm1->getSetColors('branch', 1);
					if (($preset = $GLOBALS['BE_USER']->uc['tx_pagetreehighlight']['branch'][$row['uid']]) && is_array($colors[$preset]))	{
						if (!$row['_CSSCLASS'])	{
							$row['_CSSCLASS'] = 'tx-pagetreehighlight-branch-'.$preset;
						}
						$row['_SUBCSSCLASS'] = 'tx-pagetreehighlight-branch-'.$preset;
						$GLOBALS['SOBE']->doc->inDocStylesArray['tx-pagetreehighlight-branch-'.$preset] = $this->getRowStyle($colors[$preset], $preset, 'branch');
					} elseif (is_array($branchpreset)&&count($branchpreset))	{
						if (!$row['_CSSCLASS'])	{
							$row['_CSSCLASS'] = 'tx-pagetreehighlight-branch-preset-'.$row['uid'];
						}
						$row['_SUBCSSCLASS'] = 'tx-pagetreehighlight-branch-preset-'.$row['uid'];
						$GLOBALS['SOBE']->doc->inDocStylesArray['tx-pagetreehighlight-branch-preset-'.$row['uid']] = $this->getRowStyle($branchpreset, 'preset-'.$row['uid'], 'branch');
					}
				}
			}
			return $row;
		}
	}


	function getRowStyle($def, $key, $type)	{
		if ($def['allstyles'])	{
			$styles = '
.tx-pagetreehighlight-'.$type.'-'.$key.'	{
	'.$def['allstyles'].'
}
.tx-pagetreehighlight-'.$type.'-'.$key.' ul {
						background: #eee url(\''.t3lib_extMgm::extRelPath('t3skin').'icons/gfx/ol/line.gif\') top left repeat-y !important;
	font-weight:normal !important;
					}
';
			if ($type == 'page') {
				$styles .= '
ul.tree ul li.tx-pagetreehighlight-'.$type.'-'.$key.' ul {
	background: #eee url(\''.t3lib_extMgm::extRelPath('t3skin').'icons/gfx/ol/line.gif\') top left repeat-y;
	font-weight:normal;
}
			';
			}
			
			if ($type == 'branch') {
				$styles .= '
.tx-pagetreehighlight-'.$type.'-'.$key.' ul {
	'.$def['allstyles'].'
}
			';
			}
			return $styles;
		} else	{
			$color = strtolower(trim($def['color']));
			if (preg_match('/^#?([0-9a-z]{3,6})$/', $color, $match))	{
				$cstyle = 'background-color: #'.$match[1].';';
			}
			$styles = $cstyle.trim($def['styles']);
			$return = '
.tx-pagetreehighlight-'.$type.'-'.$key.'	{
	'.$styles.'
}
.tx-pagetreehighlight-'.$type.'-'.$key.' ul, .expanded ul{
						background-color: black;
					}
';
			if ($type == 'page') {
				$return .= '
ul.tree ul li.tx-pagetreehighlight-'.$type.'-'.$key.' ul {
	background: #eee url(\''.t3lib_extMgm::extRelPath('t3skin').'icons/gfx/ol/line.gif\') top left repeat-y;
	font-weight:normal;
}
			';
			}
			return $return;
		}
	}

	/**
	 * Compiles the HTML code for displaying the structure found inside the ->tree array
	 *
	 * @param	array		"tree-array" - if blank string, the internal ->tree array is used.
	 * @return	string		The HTML code for the tree
	 */
	function printTree($treeArr='')	{
		
		$titleLen = intval($this->BE_USER->uc['titleLen']);
		if (!is_array($treeArr))	$treeArr = $this->tree;
		$out = '
			<!-- TYPO3 tree structure. -->
			<ul class="tree">
		';

			// -- evaluate AJAX request
			// IE takes anchor as parameter
		$PM = t3lib_div::_GP('PM');
		if(($PMpos = strpos($PM, '#')) !== false) { $PM = substr($PM, 0, $PMpos); }
		$PM = explode('_', $PM);
		if(($isAjaxCall = t3lib_div::_GP('ajax')) && is_array($PM) && count($PM)==4)	{
			if($PM[1])	{
				$expandedPageUid = $PM[2];
				$ajaxOutput = '';
				$invertedDepthOfAjaxRequestedItem = 0; // We don't know yet. Will be set later.
				$doExpand = true;
			} else	{
				$collapsedPageUid = $PM[2];
				$doCollapse = true;
			}
		}

		// we need to count the opened <ul>'s every time we dig into another level, 
		// so we know how many we have to close when all children are done rendering
		$closeDepth = array();
		$par_class = array();
		foreach($treeArr as $k => $v)	{
			$classAttr = $v['row']['_CSSCLASS'];
			$uid = $v['row']['uid'];
			$idAttr	= htmlspecialchars($this->domIdPrefix.$this->getId($v['row']).'_'.$v['bank']);
			$itemHTML = '';
			if($v['isFirst'] && !($doCollapse) && !($doExpand && $expandedPageUid == $uid))	{
				$itemHTML = '<ul>';
			}
			// add CSS classes to the list item
			if($v['hasSub']) { 
				if ($v['row']['_CSSCLASS'] && $v['row']['_SUBCSSCLASS']) {
					$par_class[] = $v['row']['_SUBCSSCLASS']; 
				}
				$classAttr = ($classAttr) ? 'expanded '.$classAttr : 'expanded'; 
			}
			
			if (count($par_class) > 0 && !strstr($classAttr,$par_class[count($par_class)-1])) {
				$classAttr .= " ".$par_class[count($par_class)-1];
				
			}
			if($v['isLast']) { 
				
				//only add class last if the existing class is 'expanded'
				$classAttr .= ($classAttr === 'expanded') ? ' last' : '';	 
			}
			$itemHTML .='
					<li id="'.$idAttr.'"'.($classAttr ? ' class="'.$classAttr.'"' : '').'>'.
					$v['HTML'].
					$this->wrapTitle($this->getTitleStr($v['row'],$titleLen),$v['row'],$v['bank'])."\n";

			if(!$v['hasSub']) {
				$itemHTML .= '</li>';
			}

			// we have to remember if this is the last one
			// on level X so the last child on level X+1 closes the <ul>-tag
			if($v['isLast'] && !($doExpand && $expandedPageUid == $uid)) { 
				$closeDepth[$v['invertedDepth']] = 1;
			}

			// if this is the last one and does not have subitems, we need to close
			// the tree as long as the upper levels have last items too
			if($v['isLast'] && !$v['hasSub'] && !$doCollapse && !($doExpand && $expandedPageUid == $uid)) {
				for ($i = $v['invertedDepth']; $closeDepth[$i] == 1; $i++) {
					$closeDepth[$i] = 0;
					$itemHTML .= '</ul></li>';
				}
			}
			
			// ajax request: collapse
			if($doCollapse && $collapsedPageUid == $uid) {
				$this->ajaxStatus = true;
				return $itemHTML;
			}

			// ajax request: expand
			if($doExpand && $expandedPageUid == $uid) {
				$ajaxOutput .= $itemHTML;
				$invertedDepthOfAjaxRequestedItem = $v['invertedDepth'];
			} elseif($invertedDepthOfAjaxRequestedItem) { 
				if($v['invertedDepth'] < $invertedDepthOfAjaxRequestedItem) {
					$ajaxOutput .= $itemHTML;
				} else {
					$this->ajaxStatus = true;
					return $ajaxOutput;
				}
			}
			$out .= $itemHTML;
			if($v['isLast']) {
				array_pop($par_class);
			}
		}

		if($ajaxOutput) {
			$this->ajaxStatus = true;
			return $ajaxOutput;
		}

		// finally close the first ul
		$out .= '</ul>';
		return $out;
	}
}


?>
