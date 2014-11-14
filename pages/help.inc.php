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
$mypage  = rex_request('page',    'string');
$subpage = rex_request('subpage', 'string');
$chapter = rex_request('chapter', 'string');
$func    = rex_request('func',    'string');

// SUBNAVIGATION ITEMS
////////////////////////////////////////////////////////////////////////////////
$chapterpages = array (
''                => array('Readme'                        ,'_readme.textile'                             ,'textile'),
'changelog'       => array('Changelog'                     ,'_changelog.textile'                          ,'textile'),
//'text_demo'       => array('<em>TXT include</em>'          ,'pages/_txt_include_example.txt'              ,'txt'),
//'php_demo'        => array('<em>PHP include</em>'          ,'pages/_php_include_example.php'              ,'php'),
//'iframelink_demo' => array('<em>Link (iframe)</em>'        ,'http://rexdev.de/addons/addon-template.html' ,'iframe'),
//'newwinlink_demo' => array('<em>Link (neues Fenster)</em>' ,'http://rexdev.de/addons/addon-template.html' ,'jsopenwin')
);

// BUILD CHAPTER NAVIGATION
////////////////////////////////////////////////////////////////////////////////
$chapternav = '';
foreach ($chapterpages as $chapterparam => $chapterprops)
{
  if ($chapter != $chapterparam) {
    $chapternav .= ' | <a href="?page='.$mypage.'&subpage=help&chapter='.$chapterparam.'" class="chapter '.$chapterparam.' '.$chapterprops[2].'">'.$chapterprops[0].'</a>';
  } else {
    $chapternav .= ' | <span class="chapter '.$chapterparam.' '.$chapterprops[2].'">'.$chapterprops[0].'</span>';
  }
}
$chapternav = ltrim($chapternav, " | ");

// BUILD CHAPTER OUTPUT
////////////////////////////////////////////////////////////////////////////////
$addonroot = $REX['INCLUDE_PATH']. '/addons/'.$mypage.'/';
$source    = $chapterpages[$chapter][1];
$parse     = $chapterpages[$chapter][2];

$html = a895_incparse($addonroot,$source,$parse,true);


// OUTPUT
////////////////////////////////////////////////////////////////////////////////
echo '
<div class="rex-addon-output">
  <h2 class="rex-hl2" style="font-size:1em">'.$chapternav.'</h2>
  <div class="rex-addon-content">
    <div class= "addon-template">
    '.$html.'
    </div>
  </div>
</div>';
