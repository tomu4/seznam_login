<?php
require_once '../../../../users/init.php';

$db=DB::getInstance();

$settingsQ=$db->query("SELECT * FROM settings");
$settings=$settingsQ->first();

if(!isset($_SESSION)){session_start();}

$appID=$settings->plg_sez_sezid;
$secret=$settings->plg_sez_sezsecret;
$callback=$settings->plg_sez_sezcallback;
$whereNext=$settings->plg_sez_sezfinalredir;

$curl = curl_init();

curl_setopt_array($curl, [
  CURLOPT_URL => "https://login.szn.cz/api/v1/oauth/token",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => json_encode(['grant_type' => 'authorization_code',
   "code" => $_GET['code'],
   "redirect_uri" => $callback,
   "client_secret" => $secret,
   "client_id" => $appID]),
  CURLOPT_HTTPHEADER => [
    'accept: application/json',
    'content-type: application/json'
  ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
}

$data=json_decode($response);
$sezEmail = $data->{"account_name"};

$curl = curl_init();

curl_setopt_array($curl, [
  CURLOPT_URL => "https://profil.seznam.cz/api/v1/user",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => [
    'Authorization: bearer '.$data->{"access_token"},
	'Accept: application/json'
  ],
]);

$response = curl_exec($curl);
$data=json_decode($response);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
}

//In case you want to test what you get back from FriendFace
// var_dump($user);
// echo 'Name: ' . $fbuser['name'];
// echo '<br>email: ' . $fbuser['email'];
// echo '<br>id: ' . $fbuser['id'];

//Facebook Authenticated - Let's do something with that info

//Check to see if the user has an account

$checkExistingQ = $db->query("SELECT * FROM users WHERE email = ?",array ($sezEmail));

$CEQCount = $checkExistingQ->count();

//Existing UserSpice User Found

if ($CEQCount>0){
$checkExisting = $checkExistingQ->first();
$newLoginCount = $checkExisting->logins+1;
$newLastLogin = date("Y-m-d H:i:s");

$fields=array('plg_sez_uid'=>$data->{"oauth_user_id"}, 'logins'=>$newLoginCount, 'last_login'=>$newLastLogin);

$db->update('users',$checkExisting->id,$fields);
$sessionName = Config::get('session/session_name');
Session::put($sessionName, $checkExisting->id);

$hooks = getMyHooks(['page'=>'loginSuccess']);
includeHook($hooks,'body');

  $ip = ipCheck();
  $q = $db->query("SELECT id FROM us_ip_list WHERE ip = ?",array($ip));
  $c = $q->count();
  if($c < 1){
    $db->insert('us_ip_list', array(
      'user_id' => $checkExisting->id,
      'ip' => $ip,
    ));
  }else{
    $f = $q->first();
    $db->update('us_ip_list',$f->id, array(
      'user_id' => $checkExisting->id,
      'ip' => $ip,
    ));
  }


if (!isset($_GET['page'])){	
   Redirect::to($us_url_root.$whereNext);
}
else{
	Redirect::to('../../../../../'.$_GET['page']);
}
}else{
  if($settings->registration==0) {
    session_destroy();
    Redirect::to($us_url_root.'users/join.php');
    die();
  } else {
    // //No Existing UserSpice User Found
    // if ($CEQCount<0){
    //$fbpassword = password_hash(Token::generate(),PASSWORD_BCRYPT,array('cost' => 12));
    $date = date("Y-m-d H:i:s");
    $sez_fname = $data->{"firstname"};
    $sez_lname = $data->{"lastname"};
    $sezname=$sez_fname.' '.$sez_lname;
    if($settings->auto_assign_un==1) {
      $username=$data->{"username"};
	  if(!$username or $username=="") $username=username_helper($sez_fname,$sez_lname,$sezEmail);
      if(!$username) $username=NULL;
    } else {
	$username=$data->{"username"};
    if(!$username or $username=="") $username=$sezEmail;
    }
    $fields=array('email'=>$sezEmail,'username'=>$username,'fname'=>$sez_fname,'lname'=>$sez_lname,'permissions'=>1,'logins'=>1,'join_date'=>$date,'last_login'=>$date,'email_verified'=>1,'password'=>NULL,'plg_sez_uid'=>$data->{"oauth_user_id"});

    $db->insert('users',$fields);
    $theNewId = $db->lastId();

    $insert2 = $db->query("INSERT INTO user_permission_matches SET user_id = $theNewId, permission_id = 1");

    $ip = ipCheck();
    $q = $db->query("SELECT id FROM us_ip_list WHERE ip = ?",array($ip));
    $c = $q->count();
    if($c < 1){
      $db->insert('us_ip_list', array(
        'user_id' => $theNewId,
        'ip' => $ip,
      ));
    }else{
      $f = $q->first();
      $db->update('us_ip_list',$f->id, array(
        'user_id' => $theNewId,
        'ip' => $ip,
      ));
    }
    include($abs_us_root.$us_url_root.'usersc/scripts/during_user_creation.php');

    $sessionName = Config::get('session/session_name');
    Session::put($sessionName, $theNewId);
if (!isset($_GET['page'])){	
   Redirect::to($us_url_root.$whereNext);
}
else{
	Redirect::to('../../../../../'.$_GET['page']);
}
  }
}


?>
