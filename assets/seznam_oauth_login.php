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
$sez_url="https://login.szn.cz/?client_id=".$appID."&scope=identity&response_type=code&redirect_uri=".$callback."&service=oauth";
if(isset($authUrl)) { ?>
  <a href="<?=$sez_url?>">
    <img class='img-responsive' src="<?=$us_url_root?>usersc/plugins/seznam_login/assets/seznam.png" alt=""/>
  </a>
<?php } ?>
