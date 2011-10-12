<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
  "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta NAME="keywords" CONTENT="<?=$keywords?>" />
	<meta NAME="description" CONTENT="<?=$description?>" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?=$title?></title>
	<link rel="icon" HREF="<?=$SiteRoot?>favicon.ico" type="image/x-icon" />
	<link rel="Shortcut Icon" HREF="<?=$SiteRoot?>favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" type="text/css" href="<?=$SiteRoot?>css/main.css?themeid=<?=$theme->id?>" />
	<link rel="stylesheet" type="text/css" href="<?=$SiteRoot?>js/lightbox/lightbox.css"  />
	<script src="<?=$SiteRoot?>js/scriptaculous/lib/prototype.js" type="text/javascript"></script>
 	<script src="<?=$SiteRoot?>js/scriptaculous/src/scriptaculous.js" type="text/javascript"></script>
 	<script src="<?=$SiteRoot?>js/lightbox/lightbox.js" type="text/javascript"></script>
 	<script src="<?=$SiteRoot?>js/dkp.js" type="text/javascript"></script>

 	<?=$extraHeaders?>
</head>
<body>
	<?=$content?>
</body>
</html>