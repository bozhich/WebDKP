/*==============================================
Main site class. Provides access to common tasks
related to the site.
===============================================*/
Site = new (function() {
	//The root path of the site, such as http://www.site.com/
	this.SiteRoot = "";
	//The url of the current page. Includes site root
	this.Url = "";
	//the id of the current page (if applicable)
	this.PageId = "";

	this.SetSiteRoot = function(siteRoot) {
		this.SiteRoot = siteRoot;
	}
	this.SetUrl = function(url) {
		this.url = url;
	}
	this.SetPageId = function(pageid) {
		this.PageId = pageid;
	}
	this.Init = function(siteRoot, url, pageid) {
		this.SiteRoot = siteRoot;
		this.Url = url;
		this.PageId = pageid;
	}
})();



/*==============================================
STATIC Utility class provides access to common
tasks
===============================================*/
var Util = new (function() {

	this.Test = function(message) {
		alert(message);
	}

	/*==============================================
	Shows the element with the given id
	===============================================*/
	this.Show = function(id) {
		if($(id) ) {
			$(id).style.display ='block';
		}
	}
	/*==============================================
	Hides the element with the given id
	===============================================*/
	this.Hide = function(id) {
		if($(id)) {
			$(id).style.display = 'none';
		}
	}
	/*==============================================
	Toggles the element with the given id between Hide or Show
	===============================================*/
	this.Toggle = function(id) {
		document.getElementById(id).style.display =
			(document.getElementById(id).style.display == 'none') ? 'block' : 'none';
	}

	/*==============================================
	Toggles the element with the given id between Hide or Show
	===============================================*/
	this.Toggle = function(id, focusAfter) {
		document.getElementById(id).style.display =
			(document.getElementById(id).style.display == 'none') ? 'block' : 'none';
		Util.Focus(focusAfter);
	}

	/*==============================================
	Focuses on the given element
	===============================================*/
	this.Focus = function(id) {
		if(!$(id)) {
			return;
		}
		$(id).focus();
	}

	/*=========================================================================
	Converts a px string (such as "50px") to an int.
	==========================================================================*/
	this.ConvertPxToInt = function(string) {
		return parseInt(string.substring(0,string.indexOf("px")));
	}
	/*=========================================================================
	Converts a string to an int
	==========================================================================*/
	this.ConvertToInt = function(string) {
		return parseInt(string);
	}
	/*=========================================================================
	Submits the given form if the passed event is an enter (key return) event
	on the keyboard. Allows forms to be submitted when the user presses enter,
	without having to select the submit button manually.
	Use as follows: <input type='text' onkeypress="Util.SubmitIfEnter('form',event)">
	NOTE: Do not use for forms with only a single input box. This will cause them
	to be submitted twice
	==========================================================================*/
	this.SubmitIfEnter = function(formName, event) {
		if(this.IsEnterEvent(event)){
			this.SubmitForm(formName);
		}
	}
	/*=========================================================================
	Submits the given form if the passed event is an enter (key return) event
	on the keyboard. Allows forms to be submitted when the user presses enter,
	without having to select the submit button manually.
	Use as follows: <input type='text' onkeypress="Util.SubmitIfEnter('form',event)">
	NOTE: Do not use for forms with only a single input box. This will cause them
	to be submitted twice
	==========================================================================*/
	this.SubmitIfEnter = function(formName, event, destination) {
		if(this.IsEnterEvent(event)){
			this.SubmitForm(formName, destination);
		}
	}

	/*=========================================================================
	Returns true if the passed event represents an enter keypress event
	==========================================================================*/
	this.IsEnterEvent = function(event) {
		if(event.keyCode == 13)
			return true;
		if (window.event && window.event.keyCode == 13)
	   	 	return true;
	}
	/*=========================================================================
	Auto sets focus to the given input box on page load.
	==========================================================================*/
	this.SetAutoFocus = function(inputId) {
		if(document.getElementById(inputId)) {
			document.getElementById(inputId).focus();
		}
		else {
			var self = this;
			setTimeout (function () { self.SetAutoFocus(inputId); }, 100);
		}
	}
	/*=========================================================================
	Submits the given formname to the given url destination. Destination
	is optional. If not provided the form will just be submitted.
	==========================================================================*/
	this.SubmitForm = function(formName, destination) {
		//make sure form valid
		if (!document.forms[formName]) {
			return;
		}
		//change the destination if set
		if (destination) {
			document.forms[formName].action = destination;
		}
		//submit form
		document.forms[formName].submit();
	}
	/*=========================================================================
	Submits the given formname to the given url destination. Destination
	is optional. If not provided the form will just be submitted.
	==========================================================================*/
	this.Submit = function(formName, destination) {
		this.SubmitForm(formName,destination);
	}

	/*==============================================
	Disables text selection within a given element
	===============================================*/
	this.DisableSelection = function(element) {
	    element.onselectstart = function() {
	        return false;
	    };
	    element.unselectable = "on";
	    element.style.MozUserSelect = "none";
	    element.style.cursor = "default";
	}

	/*==============================================
	Submits an Ajax request to the current page, invoking
	the given ajax handler and passing it the given data.
	OnSuccess and OnFailure are methods that will be invoked
	when the Ajax request works or fails.
	===============================================*/
	this.AjaxRequest = function(AjaxHandler, data, OnSuccess, OnFailure) {

		var parameters = "ajax="+AjaxHandler+"&"+data;

		if (typeof OnFailure == "undefined") {
			new Ajax.Request(Site.SiteRoot + Site.Url, {
			method:'post',
			parameters:parameters,
			onSuccess: OnSuccess
			});
		}
		else {
			new Ajax.Request(Site.SiteRoot + Site.Url, {
			method:'post',
			parameters: parameters,
			onSuccess: OnSuccess,
			onFailure: OnFailure
			});
		}
	}
})();
