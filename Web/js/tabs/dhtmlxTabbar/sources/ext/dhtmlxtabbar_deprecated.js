//v.3.5 build 120822

/*
Copyright DHTMLX LTD. http://www.dhtmlx.com
You allowed to use this component or parts of it under GPL terms
To use it on other terms or get Professional edition of the component please contact us at sales@dhtmlx.com
*/
   dhtmlXTabBar.prototype.setOnLoadingEnd=function(func){
		this.attachEvent("onXLE",func);
    };
    dhtmlXTabBar.prototype.setOnTabContentLoaded=function(func){
		this.attachEvent("onTabContentLoaded",func);
    };
    dhtmlXTabBar.prototype.setOnTabClose=function(func){
		this.attachEvent("onTabClose",func);
    };  
    dhtmlXTabBar.prototype.setOnSelectHandler=function(func){
    	this.attachEvent("onSelect",func);
    }