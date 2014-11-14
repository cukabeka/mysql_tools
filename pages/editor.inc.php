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
$mypage      = rex_request('page'     ,'string');
$subpage     = rex_request('subpage'  ,'string');
$func        = rex_request('func'     ,'string');
$domain      = a895_getDomain();


// ABORT SESSION
////////////////////////////////////////////////////////////////////////////////
if($func=='aborteditor')
{
  if(a895_endSession('editor') && a895_endSession('adminer'))
  {
    echo rex_info('AdminerEditor Session beendet.');
  }
}


// SESSION START FORM
////////////////////////////////////////////////////////////////////////////////
if(($func=='' || $func=='aborteditor') && !isset($REX['ADDON'][$mypage]['settings']['sessions']['editor']))
{
  echo '
    <div class="rex-addon-output">
      <div class="rex-form">

      <form action="index.php" method="POST"">
        <input type="hidden" name="page"            value="'.$mypage.'" />
        <input type="hidden" name="subpage"         value="'.$subpage.'" />
        <input type="hidden" name="func"            value="editorstart" />

            <fieldset class="rex-form-col-1">
              <legend>AdminerEditor 3.2.1</legend>
              <div class="rex-form-wrapper">

                <div class="rex-form-row rex-form-element-v2">
                  <p class="rex-form-submit">
                    <input class="rex-form-submit" type="submit" id="submit" name="submit" value="AdminerEditor Session starten" />
                  </p>
                </div><!-- .rex-form-row -->

              </div><!-- .rex-form-wrapper -->
            </fieldset>

      </form>

      </div><!-- .rex-form -->
    </div><!-- .rex-addon-output -->';
}


// HIDDEN SQLBUDDY LAUNCH FORM
////////////////////////////////////////////////////////////////////////////////
if($func=='editorstart')
{
  // SETUP HTACCESS, LOCK SESSION
  a895_startSession('editor');
  a895_startSession('adminer'); /*Editor needs Adminer resources*/

  echo '
    <div class="rex-addon-output" style="display:none;">
      <div class="rex-form">

      <form id="openeditor" action="'.$domain.'/redaxo/include/addons/'.$mypage.'/libs/adminer-3.6.1/editor/index.php" method="POST" target="adminereditor_'.$_REQUEST['PHPSESSID'].'">
        <input type="hidden" name="auth[username]"        value="'.$REX['DB']['1']['LOGIN'].'" />
        <input type="hidden" name="auth[server]"          value="'.$REX['DB']['1']['HOST'].'" />
        <input type="hidden" name="auth[password]"        value="'.$REX['DB']['1']['PSW'].'" />
        <input type="hidden" name="auth[db]"              value="'.$REX['DB']['1']['NAME'].'" />
        <input type="hidden" name="adminer_version"       value="3.6.1" />
        <input type="hidden" name="auth[driver]"          value="server" />

            <fieldset class="rex-form-col-1">
              <legend>AdminerEditor 3.6.1</legend>
              <div class="rex-form-wrapper">

                <div class="rex-form-row rex-form-element-v2">
                  <p class="rex-form-submit">
                    <input class="rex-form-submit" type="submit" value="SQLBuddy Fenster öffnen" />
                  </p>
                </div><!-- .rex-form-row -->

              </div><!-- .rex-form-wrapper -->
            </fieldset>

      </form>

      </div><!-- .rex-form -->
    </div><!-- .rex-addon-output -->


    <script type="text/javascript">
      document.getElementById("openeditor").submit();
    </script>';
}


// ACTIVE SESSION FORM
////////////////////////////////////////////////////////////////////////////////
if(isset($REX['ADDON'][$mypage]['settings']['sessions']['editor']['user']))
{
  // SESSION ABORT PERM
  $disabled = a895_hasSessionPerm('editor','css');

  echo '
    <div class="rex-addon-output">
      <div class="rex-form">

      <form action="index.php" method="POST"">
        <input type="hidden" name="page"            value="'.$mypage.'" />
        <input type="hidden" name="subpage"         value="'.$subpage.'" />
        <input type="hidden" name="func" value="aborteditor" />


            <fieldset class="rex-form-col-1">
              <legend>Aktive AdminerEditor Session</legend>
              <div class="rex-form-wrapper">

                <div class="rex-form-row">
                  <p class="rex-form-col-a rex-form-text">
                    <label for="sessionuser">User:</label>
                    <input disabled="disabled" id="sessionuser" class="rex-form-text" type="text" name="sessionuser" value="'.$REX['ADDON'][$mypage]['settings']['sessions']['editor']['user'].'" />
                  </p>
                </div><!-- .rex-form-row -->

                <div class="rex-form-row">
                  <p class="rex-form-col-a rex-form-text">
                    <label for="sessionuser">IP:</label>
                    <input disabled="disabled" id="sessionuser" class="rex-form-text" type="text" name="sessionuser" value="'.$REX['ADDON'][$mypage]['settings']['sessions']['editor']['ip'].'" />
                  </p>
                </div><!-- .rex-form-row -->

                <div class="rex-form-row rex-form-element-v2">
                  <p class="rex-form-submit">
                    <input '.$disabled.' class="rex-form-submit" type="submit" value="Session beenden" />
                  </p>
                </div><!-- .rex-form-row -->

              </div><!-- .rex-form-wrapper -->
            </fieldset>

      </form>

      </div><!-- .rex-form -->
    </div><!-- .rex-addon-output -->
  ';
}
