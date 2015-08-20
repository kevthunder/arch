package com.flashkev.genericList{
	import flash.events.IEventDispatcher;
	
	public interface IGenericRenderer extends IEventDispatcher 
	{ 
		function get data():Object;
		function set data(val:Object):void;
		function get ownerList():GenericList;
		function set ownerList(val:GenericList):void;
	}
	
}