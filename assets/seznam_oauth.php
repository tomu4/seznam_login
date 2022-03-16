<?php
/*
UserSpice 4
An Open Source PHP User Management System
by the UserSpice Team at http://UserSpice.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
?>

<?php
$db=DB::getInstance();

$settingsQ=$db->query("SELECT * FROM settings");
$settings=$settingsQ->first();

$appID=$settings->plg_sez_sezid;
$secret=$settings->plg_sez_sezsecret;
$callback=$settings->plg_sez_sezcallback;

if(!isset($_SESSION)){session_start();}

$curl = curl_init();

curl_setopt_array($curl, [
  CURLOPT_URL => "https://login.szn.cz/api/v1/oauth/auth",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_POSTFIELDS => "client_id=$appID&scope=identity&response_type=code&redirect_uri=$callback",
  CURLOPT_HTTPHEADER => [
    "content-type: application/x-www-form-urlencoded"
  ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);
if ($err) {
  echo "cURL Error #:" . $err;
}

curl_close($curl);
?>

  <?=$response?><img class="img-responsive" align=right src="<?=$us_url_root?>usersc/plugins/seznam_login/assets/seznam.png" alt=""/></a>
