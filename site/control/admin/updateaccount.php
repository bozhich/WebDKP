<?php
include_once("adminmain.php");
include_once("lib/dkp/dkpAccountUtil.php");
/*=================================================

=================================================*/
class pageUpdateAccount extends pageAdminMain {

	/*=================================================

	=================================================*/
	function area2()
	{
		global $sql;

		$this->title = "Update Account Settings";
		$this->border = 1;

		$this->set("log", $this->log);
		return $this->fetch("updateaccount.tmpl.php");
	}

	function eventUpdateAccount(){
		//todo: check permissions

		//get post data
		$name = util::getData("username");
		$email = util::getData("email");
		$password = util::getData("password");

		//update username
		$result = dkpAccountUtil::UpdateUsername($password, $name);
		if($result != dkpAccountUtil::UPDATE_OK) {
			$this->setEventResult(false, dkpAccountUtil::GetErrorString($result));
			return;
		}

		//update email
		$result = dkpAccountUtil::UpdateEmail($password, $email);
		if($result != dkpAccountUtil::UPDATE_OK) {
			$this->setEventResult(false, dkpAccountUtil::GetErrorString($result));
			return;
		}

		$this->setEventResult(true,"Account Updated!");
	}

	function eventUpdatePassword(){
		$currentpassword = util::getData("currentPassword");
		$password1 = util::getData("password1");
		$password2 = util::getData("password2");

		$result = dkpAccountUtil::UpdatePassword($currentpassword, $password1, $password2);
		if($result != dkpAccountUtil::UPDATE_OK) {
			$this->setEventResult(false, dkpAccountUtil::GetErrorString($result));
			return;
		}

		$this->setEventResult(true,"Password Updated!");
	}
}
?>