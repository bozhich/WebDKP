<?php
/*===========================================================
CLASS DESCRIPTION
=============================================================
The wowstats class provides a set of static methods for
retrieving statistics for items from World of Warcraft.
It provides function to create item tooltip links or to just
retrieve php class instances that contains information about a
specific item.

Item data is retrieved from wowhead and is then cached locally
in a mysql database. This improves perforamnce for future
lookups.

Created by Scott Bailey, 2008

Modified by Protevis, 2009
- added class item_data
- rewrited some code for wowhead xml

*/
include_once("itemcache.php");

    class item_data {
      public function get_xml($url) {
        $ch = curl_init();
        $header[] = 'Accept-Language: en-gb';
        $browser = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)';
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_HTTPHEADER, $header); 
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt ($ch, CURLOPT_USERAGENT, $browser);
        $url_string = curl_exec($ch);
        return simplexml_load_string($url_string);
		curl_close($ch);
      }
    }

class wowstats {
	/*===========================================================
	MEMBER VARIABLES
	============================================================*/
	//the url where item xml data can be downloaded from for items.
	//{ITEM_NAME} will be replaced with a url encoded name
      const itemUrl = "http://www.wowhead.com/item={ITEM_NAME}&xml";

	/*===========================================================

	============================================================*/
	function GetItem($name, $candownload = false){

		//first, attempt to download it from the database
		$item = new itemcache();
		$item->loadFromDatabaseByName($name);
		//if id didn't exist, we can either download
		//the stats from online, or give up
		if($item->id == "" || $item->itemid == "" || $item->itemid == 0) {
        
				return wowstats::DownloadItem($name);
		}
		//if we could download the item, return it
		return $item;
	}

	function ItemExists($name){
		return itemcache::exists($name);
	}

	function GetTextLink($name, $candownload = false){
		$item = wowstats::GetItem($name, $candownload);
		if($item === false ) {
			//construct the 'click to load tooltip' link
			$toReturn = "<a href='javascript:;' class='q99 tooltip noitemdata'>$name</a>";
			return $toReturn;
		}

		$link = $item->link;
		$name = $item->name;
		$quality = $item->quality;

		if($item->itemid == 0)
			$toReturn = "<a href='javascript:;' class='q0 tooltip itemnotfound'>$name</a>";
		else
			$toReturn = "<a href='$link' class='q$quality'>$name</a>";


		return $toReturn;
	}



	function DownloadItem($name){

		//get the url that we will download from
		$url = wowstats::itemUrl;
		$url = str_replace("{ITEM_NAME}", urlencode($name), $url);

		//get and parser the data
        $doc = new item_data();
        $doc = $doc->get_xml($url);

		//create a new item
		$item = new itemcache();

		//grab the needed data out of the xml document
		$item->name = $doc->item->name;
		$item->itemid = $doc->item['id'];
		$item->quality = $doc->item->quality['id'];
		//$item->link = $doc->item->link;
        /* lukas */
        $item->link =  preg_replace('(http:\/\/www.wowhead.com\/item=(\d+))' , 'http://www.openwow.com/?item=\1',$doc->item->link) ;
    	$item->icon = $doc->item->icon;

		if($item->itemid == "") {
			$item->name = $name;
			$item->itemid = 0;
			$item->saveNew();
			return $item;
		}

		//save the results into the database
		$item->saveNew();

		return $item;
	}
}

?>
