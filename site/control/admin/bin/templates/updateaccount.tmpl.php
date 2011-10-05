<?=$tabs?>
<?=$sidebar?>

<div class="adminContents">

<?php if(isset($eventResult)){ ?>
<div style="margin-left:70px;width:375px" class="<?=($eventResult?"message":"errorMessage")?>"><?=$eventMessage?></div>
<?php } ?>

<br />

<form action="<?=$baseurl?>Admin/UpdateAccount" method="post" name="updateAccount">
<input type="hidden" name="event" value="updateAccount">
<div style="float:left;padding-top:10px;"><img src="<?=$siteRoot?>images/dkp/account.gif"></div>
<div style="margin-left:70px">
<table class="dkpForm" >
<tr>
	<td colspan=2 class="title">Change Account Details</td>
</tr>
<tr>
	<td class="label" style="width:180px">Username:</td>
	<td><input name="username" type="text" value="<?=$siteUser->username?>" ></td>
</tr>
<tr>
	<td class="label">Email:</td>
	<td><input name="email" type="text" value="<?=$siteUser->email?>"></td>
</tr>
<tr>
	<td class="label">Current Password:</td>
	<td><input name="password" type="password" value=""></td>
</tr>
<tr>
	<td colspan=2><input type="submit" value="Save Changes"></td>
</tr>
</table>
</div>
</form>

<br />
<br />
<form action="<?=$baseurl?>Admin/UpdateAccount" method="post" name="updatePassword">
<input type="hidden" name="event" value="updatePassword">
<div style="float:left;padding-top:10px;"><img src="<?=$siteRoot?>images/dkp/password.gif"></div>
<div style="margin-left:70px">
<table class="dkpForm" >
<tr>
	<td colspan=2 class="title">Change Password</td>
</tr>
<tr>
	<td class="label" style="width:180px">New Password:</td>
	<td><input name="password1" type="password" value="" ></td>
</tr>
<tr>
	<td class="label">Retype Password:</td>
	<td><input name="password2" type="password" value="" ></td>
</tr>
<tr>
	<td class="label">Current Password:</td>
	<td><input name="currentPassword" type="password" value=""></td>
</tr>
<tr>
	<td colspan=2><input type="submit" value="Save Changes"></td>
</tr>
</table>
</div>
</form>



</div>
