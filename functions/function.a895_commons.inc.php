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

// INCLUDE PARSER FUNCTION
////////////////////////////////////////////////////////////////////////////////
if (!function_exists('a895_incparse'))
{
  function a895_incparse($root,$source,$parsemode,$return=false)
  {

    switch ($parsemode)
    {
      case 'textile':
      $source = $root.$source;
      $content = file_get_contents($source);
      $html = a895_textileparser($content,true);
      break;

      case 'txt':
      $source = $root.$source;
      $content = file_get_contents($source);

      // links erzeugen
      //$content = ereg_replace('http://www.', 'www.', $content);
      $content = preg_replace('/www\./', 'http://www.', $content);
      $content = preg_replace("#(^|[^\"=]{1})(http://|ftp://|mailto:|https://)([^\s<>]+)([\s\n<>]|$)#sm","\\1<a class=\"jsopenwin\" href=\"\\2\\3\">\\3</a>\\4",$content);

      $html =  '<pre class="plain">'.$content.'</pre>';
      break;

      case 'raw':
      $source = $root.$source;
      $content = file_get_contents($source);
      $html = $content;
      break;

      case 'php':
      $source = $root.$source;
      $html =  get_include_contents($source);
      break;

      case 'iframe':
      $html = '<iframe src="'.$source.'" width="99%" height="600px"></iframe>';
      break;

      case 'jsopenwin':
      $html = 'Externer link: <a href="'.$source.'">'.$source.'</a>
      <script language="JavaScript">
      <!--
      window.open(\''.$source.'\',\''.$source.'\');
      //-->
      </script>';
      break;

      case 'extlink':
      $html = 'Externer link: <a href="'.$source.'">'.$source.'</a>';
      break;
    }

    if($return)
    {
      return $html;
    }
    else
    {
      echo $html;
    }

  }
}

// TEXTILE PARSER FUNCTION
////////////////////////////////////////////////////////////////////////////////
if (!function_exists('a895_textileparser'))
{
  function a895_textileparser($textile,$return=false)
  {
    if(OOAddon::isAvailable("textile"))
    {
      global $REX;

      if($textile!='')
      {
        $textile = htmlspecialchars_decode($textile);
        $textile = str_replace("<br />","",$textile);
        $textile = str_replace("&#039;","'",$textile);
        if (strpos($REX['LANG'],'utf'))
        {
          $html = rex_a79_textile($textile);
        }
        else
        {
          $html =  utf8_decode(rex_a79_textile($textile));
        }

        if($return)
        {
          return $html;
        }
        else
        {
          echo $html;
        }
      }

    }
    else
    {
      $html = rex_warning('WARNUNG: Das <a href="index.php?page=addon">Textile Addon</a> ist nicht aktiviert! Der Text wird ungeparst angezeigt..');
      $html .= '<pre>'.$textile.'</pre>';

      if($return)
      {
        return $html;
      }
      else
      {
        echo $html;
      }
    }
  }
}

// ECHO TEXTILE FORMATED STRING
////////////////////////////////////////////////////////////////////////////////
if (!function_exists('echotextile'))
{
  function echotextile($msg) {
    global $REX;
    if(OOAddon::isAvailable("textile")) {
      if($msg!='') {
         $msg = str_replace("	","",$msg); // tabs entfernen
         if (strpos($REX['LANG'],'utf')) {
          echo rex_a79_textile($msg);
        } else {
          echo utf8_decode(rex_a79_textile($msg));
        }
      }
    } else {
      $fallback = rex_warning('WARNUNG: Das <a href="index.php?page=addon">Textile Addon</a> ist nicht aktiviert! Der Text wird ungeparst angezeigt..');
      $fallback .= '<pre>'.$msg.'</pre>';
      echo $fallback;
    }
  }
}



// http://php.net/manual/de/function.include.php
////////////////////////////////////////////////////////////////////////////////
if (!function_exists('get_include_contents'))
{
  function get_include_contents($filename) {
    if (is_file($filename)) {
      ob_start();
      include $filename;
      $contents = ob_get_contents();
      ob_end_clean();
      return $contents;
    }
    return false;
  }
}


// SAVE ADDON SETTINGS
////////////////////////////////////////////////////////////////////////////////
function a895_saveConf($myCONF)
{
  global $REX,$mypage;

  // SAVE SETTINGS
  $DYN    = '$REX["ADDON"]["'.$mypage.'"]["settings"] = '.stripslashes(var_export($myCONF,true)).';';
  $config = $REX['INCLUDE_PATH'].'/addons/'.$mypage.'/config.inc.php';
  if(rex_replace_dynamic_contents($config, $DYN))
  {
    // UPDATE REX
    $REX['ADDON'][$mypage]['settings'] = $myCONF;

    return true;
  }
  else
  {
    return false;
  }
}


// START SESSION WRAPPER
////////////////////////////////////////////////////////////////////////////////
function a895_startSession($tool)
{
  global $REX;

  if(!$tool)
    return false;

  // INSTALL HTACCESS
  $ret = a895_htaccessInstall($tool);

  // LOCK SESSION
  $ret = a895_lockSession($tool);

  return $ret;
}


// END SESSION WRAPPER
////////////////////////////////////////////////////////////////////////////////
function a895_endSession($tool)
{
  global $REX;

  if(!$tool)
    return false;

  // INSTALL HTACCESS
  $ret = a895_htaccessDelete($tool);

  // LOCK SESSION
  $ret = a895_unlockSession($tool);

  return $ret;
}


// LOCK SESSION
////////////////////////////////////////////////////////////////////////////////
function a895_lockSession($tool=false)
{
  global $REX,$mypage;

  if(!$tool)
    return false;

  // GET ADDON CONFIG
  $myCONF = $REX['ADDON'][$mypage]['settings'];

  // LOCK SESSION
  $myCONF['sessions'][$tool] = array (
       'user'       => $REX['USER']->getValue('login'),
       'ip'         => $_SERVER['REMOTE_ADDR'],
       'session_id' => $_REQUEST['PHPSESSID']
    );

  if(a895_saveConf($myCONF))
  {
    return true;
  }
  else
  {
    return false;
  }
}


// UNLOCK SESSION
////////////////////////////////////////////////////////////////////////////////
function a895_unlockSession($tool=false)
{
  global $REX,$mypage;

  if(!$tool)
    return false;

  // GET ADDON CONFIG
  $myCONF = $REX['ADDON'][$mypage]['settings'];

  // UNLOCK SESSION
  unset($myCONF['sessions'][$tool]);

  if(a895_saveConf($myCONF))
  {
    return true;
  }
  else
  {
    return false;
  }
}


// INSTALL HTACCESS
////////////////////////////////////////////////////////////////////////////////
function a895_htaccessInstall($tool=false,$ip=false)
{
  global $REX,$mypage;

  if(!$tool)
    return false;

  if(!$ip)
  {
    $ip = $_SERVER['REMOTE_ADDR'];
  }

  $ht_file  = $REX['ADDON'][$mypage]['ht_files'][$tool];
  $ht_conts = 'Order Deny,Allow'.PHP_EOL.
              'Deny from all'.PHP_EOL.
              'Allow from '.$ip;

  if(rex_put_file_contents($ht_file,$ht_conts))
  {
    return true;
  }
  else
  {
    return false;
  }
}


// DELETE HTACCESS
////////////////////////////////////////////////////////////////////////////////
function a895_htaccessDelete($ht_owner='all')
{
  global $REX,$mypage;
  $ret = true;

  switch($ht_owner)
  {
    case 'all':
    foreach($REX['ADDON'][$mypage]['ht_files'] as $tool => $ht_file)
    {
      if(file_exists($ht_file))
        if(!unlink($ht_file))
          $ret = false;
    }
    break;

    default:
      $ht_file = $REX['ADDON'][$mypage]['ht_files'][$ht_owner];
      if(file_exists($ht_file))
        if(!unlink($ht_file))
          $ret = false;
  }
  return $ret;
}


// GET DOMAIN
////////////////////////////////////////////////////////////////////////////////
function a895_getDomain()
{
  global $REX,$mypage;

  // GET ADDON CONFIG
  $myCONF = $REX['ADDON'][$mypage]['settings'];

  // SWITCH DOMAIN
  if($myCONF['httpsdomain']!='')
  {
    return 'https://'.$myCONF['httpsdomain'];
  }
  else
  {
    return 'http://'.$_SERVER['HTTP_HOST'];
  }
}


// GET INACTIVE USERS
////////////////////////////////////////////////////////////////////////////////
function a895_getOfflineUsers()
{
  global $REX;
  $users = array();

  $qry = 'SELECT `login` FROM rex_user WHERE `session_id`=\'\';';
  $tmp = new rex_sql;
  foreach($tmp->getArray($qry) as $k => $v)
  {
    $users[] = $v['login'];
  }

  if(count($users)>0)
  {
    return $users;
  }
  else
  {
    return false;
  }
}


// GET USER SESSIONS
////////////////////////////////////////////////////////////////////////////////
function a895_getUserSessions($user=false)
{
  global $REX,$mypage;
  $user_sessions = array();

  if(!$user)
    return false;

  $sessions = $REX['ADDON'][$mypage]['settings']['sessions'];
  foreach($sessions as $tool => $session)
  {
    if($session['user'] == $user)
    {
      $user_sessions[] = $tool;
    }
  }

  if(count($user_sessions)>0)
  {
    return $user_sessions;
  }
  else
  {
    return false;
  }
}


// LOGOUT CLEANUP FUNCTION
////////////////////////////////////////////////////////////////////////////////
function a895_logoutCleanup()
{
  global $REX,$mypage;

  $offline_users = a895_getOfflineUsers();

  if(is_array($offline_users))
  {
    foreach($offline_users as $user)
    {
      $tools = a895_getUserSessions($user);
      if(is_array($tools))
      {
        foreach($tools as $tool)
        {
          a895_endSession($tool);
        }
      }
    }
  }

}


// SESSION PERM
////////////////////////////////////////////////////////////////////////////////
function a895_hasSessionPerm($tool=false,$mode='bool')
{
  global $REX,$mypage;
  $bool = false;

  if(!$tool)
    return false;

  // GET ADDON CONFIG
  $myCONF = $REX['ADDON'][$mypage]['settings'];

  // CHECK PERM FOR TOOL
  if($REX['USER']->isValueOf("rights","admin[]") ||
    ($REX['USER']->getValue('login')) == $myCONF['sessions'][$tool]['user'])
  {
    $bool = true;
  }

  // SWITCH MODE
  switch($mode)
  {
    case 'css':
      if(!$bool)
      {
        return 'disabled="disabled"';
      }
      else
      {
        return '';
      }
      break;

    default:
      return $bool;
  }
}


// PARAMS CAST FUNCTIONS
////////////////////////////////////////////////////////////////////////////////
function a895_nl_2_array($str)
{
  $arr = array_filter(preg_split("/\n|\r\n|\r/", $str));
  return is_array($arr) ? $arr : array($arr);
}

function a895_array_2_nl($arr)
{
  return count($arr)>0 ? implode(PHP_EOL,$arr) : '';
}

function a895_https_clean($str)
{
  $str = rtrim(trim($str),'/');
  $str = str_replace('https://','',$str);
  return $str;
}

function a895_cast($request,$conf)
{
  if(is_array($request) && is_array($conf))
  {
    foreach($conf as $key => $cast)
    {
      switch($cast)
      {
        case 'unset':
          unset($request[$key]);
          break;

        case 'nl_2_array':
          $request[$key] = a895_nl_2_array($request[$key]);
          break;

        case 'https':
          $request[$key] = a895_https_clean($request[$key]);
          break;

        default:
          $request[$key] = rex_request($key,$cast);
      }
    }
    return $request;
  }
  else
  {
    trigger_error('wrong input type, array expected', E_USER_ERROR);
  }
}
