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



require_once(t3lib_extMgm::extPath('pagetreehighlight').'class.tx_pagetreehighlight_pagetree.php');

class ux_SC_alt_db_navframe extends SC_alt_db_navframe {


	/**
	 * Initialiation of the class
	 *
	 * @return	void
	 */
	function init()	{
		global $BE_USER;
		parent::init();
		


		
		$this->setTempDBmount = t3lib_div::_GP('setTempDBmount');
		
		$this->pagetree = t3lib_div::makeInstance('tx_pagetreehighlight_pagetree');
		$this->pagetree->ext_IconMode = $BE_USER->getTSConfigVal('options.pageTree.disableIconLinkToContextmenu');
		$this->pagetree->ext_showPageId = $BE_USER->getTSConfigVal('options.pageTree.showPageIdWithTitle');
		$this->pagetree->thisScript = 'alt_db_navframe.php';
		$this->pagetree->addField('alias');
		$this->pagetree->addField('shortcut');
		$this->pagetree->addField('shortcut_mode');
		$this->pagetree->addField('mount_pid');
		$this->pagetree->addField('mount_pid_ol');
		$this->pagetree->addField('nav_hide');
		$this->pagetree->addField('url');
		
		$this->initializeTemporaryDBmount();
		
				if (!$this->ajax) {
		$code ='
var reloadinfo = t3ajax.getElementsByTagName("reloadinfo");
if (reloadinfo && reloadinfo.length)	{
	top.frames[reloadinfo[0].firstChild.data].location.href = reloadinfo[1].firstChild.data;
}
var reloadcode = t3ajax.getElementsByTagName("reloadcode");
if (reloadcode && reloadcode.length)	{
	eval(reloadcode[0].firstChild.data);
}
		';
//		$code = '';
		$this->doc->JScode = preg_replace('/(var clickmenu =)/', $code.chr(10).'$1', $this->doc->JScode);
		
		$precode = '
		navFrameHighlightedClass = Array();
		var oid = top.fsMod.navFrameHighlightedID[frameSetModule];
		theObj = document.getElementById(oid);
		if (theObj)	{
			navFrameHighlightedClass[highLightID] = theObj.getAttribute("class");
		}
		';
		$code = '
		var oid = top.fsMod.navFrameHighlightedID[frameSetModule];
		var oclass = navFrameHighlightedClass[oid];
		theObj = document.getElementById(oid);
		if ((typeof(oclass)!="undefined")&&theObj)	{
			theObj.setAttribute("class", oclass);
		}
		theObj = document.getElementById(highLightID);
		navFrameHighlightedClass[highLightID] = theObj.getAttribute("class");

		top.fsMod.navFrameHighlightedID[frameSetModule] = highLightID;
		theObj = document.getElementById(highLightID);
		if (theObj)	{
			var eclass = theObj.getAttribute("class")+" ";
			theObj.setAttribute("class", eclass+"navFrameHL");
		}
		';

		$this->doc->JScode .= $this->doc->wrapScriptTags($precode).preg_replace('/(function hilight_row\(frameSetModule\,highLightID\) {)(.*)(\/\/\s+Remove\s+old\:)(.*)(\/\/\s+Set\s+new\:.*})/sU', '$1'.chr(10).$code.chr(10), $this->doc->JScode);
		}
	}

}


// Include extension?
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['typo3/alt_db_navframe.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['typo3/alt_db_navframe.php']);
}

?>