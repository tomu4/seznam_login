<?php if(!in_array($user->data()->id,$master_account)){ Redirect::to($us_url_root.'users/admin.php');} //only allow master accounts to manage plugins! ?>

<?php
include "plugin_info.php";
pluginActive($plugin_name);
if(!empty($_POST['plugin_google_login'])){
 $token = $_POST['csrf'];
if(!Token::check($token)){
include($abs_us_root.$us_url_root.'usersc/scripts/token_error.php');
}
 // Redirect::to('admin.php?err=I+agree!!!');
}
$token = Token::generate();

if(!empty($_POST['updateSettings'])){
  $fields = array(
    'plg_sez_sezid'=>Input::get('plg_sez_sezid'),
    'plg_sez_sezlogin'=>Input::get('sezlogin'),
    'plg_sez_sezsecret'=>Input::get('plg_sez_sezsecret'),
    'plg_sez_sezcallback'=>Input::get('plg_sez_sezcallback'),
    'plg_sez_sezfinalredir'=>Input::get('plg_sez_sezfinalredir'),
  );
  //var_dump($fields);
  echo Input::get('sezlogin');
$db->update('settings',1,$fields);
$settings = $db->query("SELECT * FROM settings")->first();
//Redirect::to('admin.php?view=plugins_config&plugin=seznam_login&err=Settings+saved');
}
?>
<div class="content mt-3">
  <div class="row">
    <div class="col-6 offset-3">
      <h2>Seznam Login Settings</h2>
<strong>Please note:</strong> Social logins require that you do some configuration on your own with Seznam. Get your Client and secret id <a href="https://vyvojari.seznam.cz/oauth/admin">here.</a><br><br>


<!-- left -->
<form class="" action="" method="post">
<div class="form-group">
  <label for="plg_sez_sezlogin">Enable Seznam Login</label>
  <span style="float:right;">
    <label class="switch switch-text switch-success">
                <input name="sezlogin" id="plg_sez_sezlogin" value=1 type="checkbox" class="switch-input toggle" data-desc="Seznam Login" <?php if($settings->plg_sez_sezlogin==1) echo 'checked="true"'; ?>>
                <span data-on="Yes" data-off="No" class="switch-label"></span>
                <span class="switch-handle"></span>
              </label>
            </span>
          </div>

          <div class="form-group">
            <label for="sezid">Seznam client ID</label>
            <input type="password" class="form-control ajxtxt" data-desc="Seznam client ID" name="plg_sez_sezid" id="sezid" value="<?=$settings->plg_sez_sezid?>">
          </div>

          <div class="form-group">
            <label for="sezsecret">Seznam client Secret</label>
            <input type="password" class="form-control ajxtxt" data-desc="Seznam client Secret" name="plg_sez_sezsecret" id="sezsecret" value="<?=$settings->plg_sez_sezsecret?>">
          </div>

          <div class="form-group">
            <label for="sezcallback">Seznam Callback URL</label>
            <input type="text" class="form-control ajxtxt" data-desc="Seznam Callback URL" name="plg_sez_sezcallback" id="sezcallback" value="<?=$settings->plg_sez_sezcallback?>">
          </div>

  		<!--<div class="form-group">
            <label for="graph_ver">Facebook Graph Version - Formatted as v3.2</label>
            <input type="text" class="form-control ajxtxt" data-desc="Facebook Graph Version" name="graph_ver" id="graph_ver" value="<?=$settings->plg_sez_graph_ver?>">
          </div>-->

  		<div class="form-group">
            <label for="sezfinalredir">Redirect After Seznam Login</label>
            <input type="text" class="form-control ajxtxt" data-desc="Seznam Redirect" name="plg_sez_sezfinalredir" id="sezfinalredir" value="<?=$settings->plg_sez_sezfinalredir?>">
          </div>
		  <div class="form-group">
              <input type="submit" name="updateSettings" value="Update Settings" class="btn btn-primary">
            </div>

  		</div>
		</form>
  		</div>
      <br><br>
      If you appreciate this plugin and would like to make a donation to the author, you can do so at <a href="https://pay.vivawallet.com/tomas-uchytil">https://pay.vivawallet.com/tomas-uchytil</a>. Either way, thanks for using UserSpice!
<br><br>
