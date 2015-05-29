<?php
include('_init.inc.php');

$pageData = getWebsiteHomePage();

$pageData["isHomePage"] = 0;
$pageData["parentID"] = $pageData["pageID"];
$pageData["404"] = true;

//update data with defaults if empty
$defaults = getWebsiteSettingsData();

if(trim($pageData["pageTitle"]) == '') {
	$pageData["pageTitle"] = $defaults["site_title"];
}

if(trim($pageData["metaDesc"]) == '') {
	$pageData["metaDesc"] = $defaults["site_metadesc"];
}

if(trim($pageData["ogDesc"]) == '') {
	$pageData["ogDesc"] = $defaults["site_description"];
}

$pageData["info_twitter"] = $defaults["info_twitter"];

$templateClass = 'tertiary';
$pageData["navTitle"] = "Page Not Found";

$specialContentBlocks = array();
$specialContentBlocks[0]["blockType"] = "basic";
$specialContentBlocks[0]["isMarkdown"] = 1;
$specialContentBlocks[0]["htmlData"] = "The page you requested was not found.

The page may have moved, or you may have stumbled upon an outdated link. In any case, you can use the navigation above to find what you are looking for. 

If you have found a link on the page that is broken and needs updating, feel free to [contact us](/contact). Thanks!";

//swiftype.com

/* $specialContentBlocks[1]["blockType"] = "basic";
$specialContentBlocks[1]["isMarkdown"] = 0;
$specialContentBlocks[1]["htmlData"] = '<input type="text" class="form-control st-default-search-input"><div class="st-search-container"></div>';

$specialContentBlocks[2]["blockType"] = "code";
$opts = array("language"=>"html");
$specialContentBlocks[2]["optionsJson"] = json_encode($opts);

$specialContentBlocks[2]["htmlData"] = "<script type=\"text/javascript\">
  (function(w,d,t,u,n,s,e){w['SwiftypeObject']=n;w[n]=w[n]||function(){
  (w[n].q=w[n].q||[]).push(arguments);};s=d.createElement(t);
  e=d.getElementsByTagName(t)[0];s.async=1;s.src=u;e.parentNode.insertBefore(s,e);
  })(window,document,'script','//s.swiftypecdn.com/install/v2/st.js','_st');

  _st('install','Kfc9Xbfg44ujTLepyUsu','2.0.0');
</script>";
*/

include($siteHeader);
include($templatesDir . "/$templateClass.inc.php");
include($siteFooter);	
?>