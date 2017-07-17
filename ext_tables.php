<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (TYPO3_MODE=="BE")	{
	$GLOBALS["TBE_MODULES_EXT"]["xMOD_alt_clickmenu"]["extendCMclasses"][]=array(
		"name" => "tx_pagetreehighlight_cm1",
		"path" => t3lib_extMgm::extPath($_EXTKEY)."class.tx_pagetreehighlight_cm1.php"
	);
}

if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['defaultConfig'])	{
	t3lib_extMgm::addPageTSConfig('

# pagetreehighlight default configuration - begin
mod.tx_pagetreehighlight	{
	setup	{

# Using "page" and "branch" subparts you can set to allow specific colors
# only for single pages or for whole branches
#		page {
#		branch {
		both	{
		# The contents of the "default" key get merged to the per-user/group config
			default	{
				
                		10    {
                    			label = Yellow
                    			label.de = Gelb
                    			color = #f3f399
                		}
                		20    {
                    			label = Yellow (bold)
                    			label.de = Gelb (fett)
                    			color = #f3f399
                    			styles = font-weight: bold;
                		}
                		30    {
                    			label = Light gray
			                label.de = Hellgrau
                    			color = #d7d8d7
                		}
                		40    {
                    			label = Orange
                    			label.de = Orange
                    			color = #fed38b
                		}
                		50    {
                    			label = Green
                    			label.de = Gr&uuml;n
                    			color = #90fdb1
                		}
                		60    {
                    			label = Blue
                   		 	label.de = Blau
                    			color = #8cf1f6
                		}
                		70    {
                    			label = Magenta
                    			label.de = Magenta
                    			color = #cdb9cf
                		}
                		80    {
                    			label = Beige
                    			label.de = Beige
                    			color = #e3dbc3
                		} 
			}



#	Group or user specific configuration get merged and then merged with above
#	default configuration
#			group	{			# example

			user	{
				admin1 {
					100	{
						label = White on Black
						label.de = Weiss auf Schwarz
						color = #000000
						styles = color: #ffffff; font-weight: bold;
					}
					110	{
						label = Black on White
						label.de = Schwarz auf Weiss
						color = #ffffff
						styles = color: #000000; font-weight: bold;
					}
				}
			}
		}
	}
}
# pagetreehighlight default configuration - end
');

}






?>