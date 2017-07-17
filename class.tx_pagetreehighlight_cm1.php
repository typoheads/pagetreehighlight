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
 * Addition of an item to the clickmenu
 *
 * @author	Bernhard Kraft <kraftb@kraftb.at>
 * @author	Dev-Team Typoheads <dev@typoheads.at>
 */


class tx_pagetreehighlight_cm1 {
	var $ckey = 1;

	function main(&$backRef,$menuItems,$table,$uid)	{
		global $BE_USER,$TCA,$LANG;
	
		$localItems = Array();
		$this->backRef = &$backRef;
		$LL = $this->includeLL();

		$subname = t3lib_div::_GP('subname');
		$set = trim(t3lib_div::_GP('set'));
		$sub = '';
			
			// Adds the regular item:
		$LL = $this->includeLL();
		$this->tsConfig = t3lib_BEfunc::getModTSconfig($this->backRef->rec['uid'], 'mod.tx_pagetreehighlight');
			
		switch ($subname)	{
			case 'moreoptions_tx_pagetreehighlight_cm1_page':
				$sub = 'page';
			break;
			case 'moreoptions_tx_pagetreehighlight_cm1_branch':
				$sub = 'branch';
			break;
		}

		$localItemsArr = array();
		$localItemsArr['page'] = $this->getSetColors('page');
		$localItemsArr['branch'] = $this->getSetColors('branch');

		$reloadPageTree = false;
		if ($set&&$sub&&is_array($localItemsArr[$sub])&&($setItem = $localItemsArr[$sub][$set]))		{
			$GLOBALS['BE_USER']->uc['tx_pagetreehighlight'][$sub][$this->backRef->rec['uid']] = $set;
			$GLOBALS['BE_USER']->writeUC();
			$reloadPageTree = true;
		}
		if ($sub&&$set=='__unset__')		{
			unset($GLOBALS['BE_USER']->uc['tx_pagetreehighlight'][$sub][$this->backRef->rec['uid']]);
			$GLOBALS['BE_USER']->writeUC();
			$reloadPageTree = true;
		}
		if ($reloadPageTree)	{
			$GLOBALS['SOBE']->content .= '
<reloadcode >top.content.nav_frame.location.href = "alt_db_navframe.php";</reloadcode>
			';
			return array();
		}

		if (!$backRef->cmLevel)	{
			
				// Returns directly, because the clicked item was not from the pages table 
			if ($table!="pages")	return $menuItems;

			foreach (array('page', 'branch') as $csub)	{
				if (count($localItemsArr[$csub])>1)	{
					if (!count($localItems))	{
						$localItems[] = 'spacer';
					}
					$localItems['moreoptions_tx_pagetreehighlight_cm1_'.$csub] = $backRef->linkItem(
						$GLOBALS['LANG']->getLLL('cm1_'.$csub.'_title_activate', $LL),
						$backRef->excludeIcon('<img src="'.t3lib_extMgm::extRelPath("pagetreehighlight").'cm1/cm_'.$csub.'_icon_activate.gif" width="16" height="16" border="0" align="top" />'),
						'top.loadTopMenu(\''.t3lib_div::linkThisScript().'&cmLevel=1&subname=moreoptions_tx_pagetreehighlight_cm1_'.$csub.'\');return false;',
						0,
						1
					);
				} elseif (count($localItemsArr[$csub])||$GLOBALS['BE_USER']->uc['tx_pagetreehighlight'][$csub][$this->backRef->rec['uid']])	{
					if (!count($localItems))	{
						$localItems[]="spacer";
					}
					if ($GLOBALS['BE_USER']->uc['tx_pagetreehighlight'][$csub][$this->backRef->rec['uid']])	{
						$localItems[] = $this->backRef->linkItem(
							$GLOBALS["LANG"]->getLLL('cm1_label_clear_'.$csub, $LL),
							$backRef->excludeIcon('<img src="'.t3lib_extMgm::extRelPath("pagetreehighlight").'cm1/cm_icon_clear.gif" width="16" height="16" border="0" align="top" />'),
							'top.loadTopMenu(\''.t3lib_div::linkThisScript().'&cmLevel=1&subname=moreoptions_tx_pagetreehighlight_cm1_'.$csub.'&set=__unset__\');return false;'
						);
					} else	{
						$lItem = reset($localItemsArr[$csub]);
						if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['pagetreehighlight']['labelSingle'])	{
							$lItem[1] = $GLOBALS["LANG"]->getLLL('cm1_'.$csub.'_title_activate_single',$LL).': '.$lItem[1];
						} else	{
							$lItem[1] = $GLOBALS["LANG"]->getLLL('cm1_'.$csub.'_title_activate_single',$LL);
						}
						if (!$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['pagetreehighlight']['colorSingle'])	{
							$lItem[2] = $backRef->excludeIcon('<img src="'.t3lib_extMgm::extRelPath("pagetreehighlight").'cm1/cm_'.$csub.'_icon_activate.gif" width="16" height="16" border="0" align="top" />');
						}
						$localItems['moreoptions_tx_pagetreehighlight_cm1_'.$csub.'_clear'] = $lItem;
					}
				}
			}

			// Simply merges the two arrays together and returns ...
			$menuItems=array_merge($menuItems,$localItems);
		} else {
			$localItems = $localItemsArr[$sub];

			if ($sub)	{
				$clearMarking = array();
				$clearMarking['__clear__'] = $this->backRef->linkItem(
						$GLOBALS["LANG"]->getLLL('cm1_label_clear', $LL),
						$backRef->excludeIcon('<img src="'.t3lib_extMgm::extRelPath("pagetreehighlight").'cm1/cm_icon_clear.gif" width="16" height="16" border="0" align="top" />'),
						"top.loadTopMenu('".t3lib_div::linkThisScript()."&cmLevel=1&subname=moreoptions_tx_pagetreehighlight_cm1_".$sub."&set=__unset__');return false;"
					);
				$localItems = t3lib_div::array_merge_recursive_overrule($clearMarking, $localItems);
			}
			
			#$menuItems=array_merge($localItems,$menuItems);
			
			#################
			# changed to this because of troubles with other menu options offering a second menu level (e.g. "more options")
			#################
			$menuItems=t3lib_div::array_merge_recursive_overrule($localItems,$menuItems,0,false);
		}
		return $menuItems;
	}


	function getStylePresets()	{
		$pconf = array_merge(is_array($bc = $this->getConf($this->tsConfig['properties']['presets.']['both.']))?$bc:array(), is_array($pc = $this->getConf($this->tsConfig['properties']['presets.']['page.']))?$pc:array());
		$bconf = array_merge(is_array($bc = $this->getConf($this->tsConfig['properties']['presets.']['both.']))?$bc:array(), is_array($pc = $this->getConf($this->tsConfig['properties']['presets.']['branch.']))?$pc:array());
		return array(is_array($pconf)?$pconf:false, is_array($bconf)?$bconf:false);
	}

	function getSetColors($type, $retArray = 0)	{
		global $BE_USER, $LANG;
		$conf = array_merge(is_array($bc = $this->getConf($this->tsConfig['properties']['setup.']['both.']))?$bc:array(), is_array($sc = $this->getConf($this->tsConfig['properties']['setup.'][$type.'.']))?$sc:array());
		$colors = array();
		if (is_array($conf))	{
			foreach ($conf as $ckey => $carr)	{
				$cstyle = '';
				$styles = '';
				$label = $this->getLabel($carr, 'label', $LANG->getLL('cm1_label_color').' '.$this->ckey++);
				$color = strtolower(trim($carr['color']));
				if (preg_match('/^#?([0-9a-z]{3,6})$/', $color, $match))	{
					$cstyle = 'background-color: #'.$match[1].' !important;';
				}
				if (!$cstyle)	{
					$vcolor = strtolower(trim($carr['vcolor']));
					if (preg_match('/^#?([0-9a-z]{3,6})$/', $vcolor, $match))	{
						$cstyle = 'background-color: #'.$match[1].';';
					}
				}
				$styles = $cstyle.trim($carr['styles']);
				if (strlen($styles))	{
					if ($retArray)	{
						$pkey = preg_replace('/\.$/', '', $ckey);
						$colors[$pkey] = array(
							'label' => $label,
							'backstyle' => $cstyle,
							'allstyles' => $styles,
						);
					} else	{
						$pkey = preg_replace('/\.$/', '', $ckey);
						$colors[$pkey] = $this->backRef->linkItem(
							$label,
							$this->backRef->excludeIcon('<img src="clear.gif" width="15" height="12" border="0" align="top" style="'.$styles.'" />'),
							"top.loadTopMenu('".t3lib_div::linkThisScript()."&cmLevel=1&subname=moreoptions_tx_pagetreehighlight_cm1_".$type."&set=".rawurlencode($pkey)."');return false;"
						);
					}
				}
			}
		}
		return $colors;
	}

	function getLabel($conf, $key, $label = '')	{
		global $LANG;
		if ($conf[$key.'.'][$LANG->lang])	{
			$label = $conf[$key.'.'][$LANG->lang];
		} elseif ($conf[$key])	{
			$label = $conf[$key];
		}
		return $label;
	}

	function getConf($tsConfig)	{
		global $BE_USER;
		$conf = $tsConfig['user.'][$BE_USER->user['username'].'.'];
		if (!is_array($conf))	{
			$conf = $tsConfig['user.'][$BE_USER->user['uid'].'.'];
		}
		$groups = t3lib_div::trimExplode(',', $BE_USER->user['usergroup']);
		if (is_array($groups))	{
			foreach ($groups as $group)	{
				$subconf = $tsConfig['group.'][$group.'.'];
				if (is_array($subconf))	{
					if ($this->tsConfig['properties']['config.']['groupConfigIntersect'])	{
						if (!is_array($conf))	{
							$conf = $subconf;
						} else	{
							$keys1 = array_keys($conf);
							$keys2 = array_keys($subconf);
							$ikeys = array_intersect($keys1, $keys2);
							$nconf = array();
							foreach ($ikeys as $ikey)	{
								$nconf[$ikey] = $conf[$ikey];
							}
							$conf = $nconf;
						}
					} else	{
						if (!is_array($conf))	{
							$conf = $subconf;
						} else	{
							$conf = array_merge($conf, $subconf);
						}
					}
				}
			}
		}
		if (!is_array($conf)&&is_array($tsConfig['default.']))	{
			$conf = $tsConfig['default.'];
		} elseif (is_array($tsConfig['default.']))	{
			$conf = array_merge($conf, $tsConfig['default.']);
		}
		return $conf;
	}

	/**
	 * Reads the [extDir]/locallang.xml and returns the $LOCAL_LANG array found in that file.
	 *
	 * @return	[type]		...
	 */
	function includeLL()	{
		global $LANG;
	
		$LOCAL_LANG = $LANG->includeLLFile('EXT:pagetreehighlight/locallang.xml',FALSE);
		return $LOCAL_LANG;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pagetreehighlight/class.tx_pagetreehighlight_cm1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pagetreehighlight/class.tx_pagetreehighlight_cm1.php']);
}

?>
