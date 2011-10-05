<?php
global $guildset;
?>

<div id="header">
	<a href="<?=$SiteRoot?>"><img src="<?=$theme->getAbsDirectory()?>images/header/logo.png"></a>
</div>
<div id="bar">
	<?php if(!$guildset){ ?>
	<a class="barlink" href="<?=$SiteRoot?>Setup">Setup</a>
	<div class="barsep">&nbsp;</div>
	<?php } else { ?>
	<a class="barlink <?=($activetab=="dkp"?"activetab":"")?>" href="<?=$SiteRoot?>Dkp">DKP</a>
	<div class="barsep">&nbsp;</div>
  <a class="barlink <?=($activetab=="loot"?"activetab":"")?>" href="<?=$SiteRoot?>Loot">Loot</a>
	<div class="barsep">&nbsp;</div>
	<a class="barlink <?=($activetab=="awards"?"activetab":"")?>" href="<?=$SiteRoot?>Awards">Awards</a>
	<div class="barsep">&nbsp;</div>
	<a class="barlink" href="<?=$SiteRoot?>forum">Forum</a>
  <div class="barsep">&nbsp;</div>
		<?php if($settings && $settings->GetLootTableEnabled()) { ?>
	<a class="barlink <?=($activetab=="loottable"?"activetab":"")?>" href="<?=$SiteRoot?>LootTable">Loot Table</a>
	<div class="barsep">&nbsp;</div>
	<?php } ?>
	<?php if($siteUser->visitor){ ?>
	<a class="barlink" href="<?=$SiteRoot?>Login">Login</a>
	<div class="barsep">&nbsp;</div>
	<?php } else { ?>
	<a class="barlink <?=($activetab=="admin"?"activetab":"")?>" href="<?=$SiteRoot?>Admin">Admin</a>
	<div class="barsep">&nbsp;</div>
	<a class="barlink" href="<?=$SiteRoot?>login?siteUserEvent=logout">Logout</a>
	<div class="barsep">&nbsp;</div>
	<?php } ?>
	<?php } ?>
</div>