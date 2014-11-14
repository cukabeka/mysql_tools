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

// PARAMS
////////////////////////////////////////////////////////////////////////////////
$mypage      = rex_request('page'        ,'string');
$subpage     = rex_request('subpage'     ,'string');
$func        = rex_request('func'        ,'string');
$httpsdomain = rex_request('httpsdomain' ,'string');


// SAVE SETTINGS
////////////////////////////////////////////////////////////////////////////////
if($func=='savesettings')
{
  // MERGE REQUEST & ADDON SETTINGS
  $params_cast = $REX['ADDON'][$mypage]['params_cast'];
  $myCONF = array_merge($REX['ADDON'][$mypage]['settings'],a895_cast($_POST,$params_cast));

  // SAVE SETTINGS
  if(a895_saveConf($myCONF))
  {
    echo rex_info('Einstellungen wurden gespeichert.');
  }
  else
  {
    echo rex_warning('Beim speichern der Einstellungen ist ein Problem aufgetreten.');
  }
}


// MAIN PAGE
////////////////////////////////////////////////////////////////////////////////
if($func=='' || $func=='savesettings')
{
echo '
  <div class="rex-addon-output">
    <div class="rex-form">

    <form action="index.php" method="POST"">
      <input type="hidden" name="page"            value="'.$mypage.'" />
      <input type="hidden" name="subpage"         value="'.$subpage.'" />
      <input type="hidden" name="func"            value="savesettings" />


          <fieldset class="rex-form-col-1">
            <legend>Addon Settings</legend>
            <div class="rex-form-wrapper">

              <div class="rex-form-row">
                <p class="rex-form-col-a rex-form-text">
                  <label for="httpsdomain">HTTPS Domain:</label>
                  <strong>https://</strong> <input style="width:200px;" id="httpsdomain" class="rex-form-text" type="text" name="httpsdomain" value="'.stripslashes($REX['ADDON'][$mypage]['settings']['httpsdomain']).'" />
                </p>
              </div><!-- .rex-form-row -->

              <div class="rex-form-row rex-form-element-v2">
                <p class="rex-form-submit">
                  <input class="rex-form-submit" type="submit" id="submit" name="submit" value="Einstellungen sichern" />
                </p>
              </div><!-- .rex-form-row -->

            </div><!-- .rex-form-wrapper -->
          </fieldset>

    </form>

    </div><!-- .rex-form -->
  </div><!-- .rex-addon-output -->';
}
