<?php
/**
* MySQL Tools - Redaxo Addon
*
* @author http://rexdev.de
* @link https://github.com/jdlx/mysql_tools
*
* @package redaxo 4.3.x/4.4.x
* @version 1.0.0
*/

/**
* Adminer Lib
* @link http://www.adminer.org/
* @version 3.6.1
*/

/**
* SQLBuddy Lib
* @link https://github.com/calvinlough/sqlbuddy
* @version 1.3.3
*/

// GET PARAMS
////////////////////////////////////////////////////////////////////////////////
$mypage   = rex_request('page', 'string');
$subpage  = rex_request('subpage', 'string');
$faceless = rex_request('faceless', 'string');

if($faceless != 1)
{


  // BACKEND CSS
  //////////////////////////////////////////////////////////////////////////////
  if ($REX['REDAXO'])
  {
    rex_register_extension('PAGE_HEADER', 'mysql_tools_header');

    function mysql_tools_header($params)
    {
      $params['subject'] .= PHP_EOL.'  <!-- mysql_tools Addon -->'.
                            PHP_EOL.'  <link rel="stylesheet" type="text/css" href="../files/addons/mysql_tools/backend.css" media="screen, projection, print" />'.
                            PHP_EOL.'  <!-- /mysql_tools Addon -->'.PHP_EOL;
      return $params['subject'];
    }
  }


  // REX BACKEND LAYOUT TOP
  //////////////////////////////////////////////////////////////////////////////
  require $REX['INCLUDE_PATH'] . '/layout/top.php';

  // TITLE & SUBPAGE NAVIGATION
  //////////////////////////////////////////////////////////////////////////////
  rex_title($REX['ADDON']['name'][$mypage].' <span class="addonversion">'.$REX['ADDON']['version'][$mypage].'</span>', $REX['ADDON'][$mypage]['SUBPAGES']);

  // INCLUDE REQUESTED SUBPAGE
  //////////////////////////////////////////////////////////////////////////////
  if(!$subpage)
  {
    $subpage = 'settings';  /* DEFAULT SUBPAGE */
  }
  require $REX['INCLUDE_PATH'] . '/addons/'.$mypage.'/pages/'.$subpage.'.inc.php';

  // JS SCRIPT FÜR LINKS IN NEUEN FENSTERN (per <a class="jsopenwin">)
  ////////////////////////////////////////////////////////////////////////////////
  echo '
  <script type="text/javascript">
  // onload
  window.onload = externalLinks;

  // http://www.sitepoint.com/article/standards-compliant-world
  function externalLinks()
  {
   if (!document.getElementsByTagName) return;
   var anchors = document.getElementsByTagName("a");
   for (var i=0; i<anchors.length; i++)
   {
     var anchor = anchors[i];
     if (anchor.getAttribute("href"))
     {
       if (anchor.getAttribute("class") == "jsopenwin")
       {
       anchor.target = "_blank";
       }
     }
   }
  }
  </script>
  ';

  // REX BACKEND LAYOUT BOTTOM
  //////////////////////////////////////////////////////////////////////////////
  require $REX['INCLUDE_PATH'] . '/layout/bottom.php';
}
else
{
  require $REX['INCLUDE_PATH'] . '/addons/'.$mypage.'/pages/'.$subpage.'.inc.php';
}
