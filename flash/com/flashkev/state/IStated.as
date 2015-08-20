package com.flashkev.state {
	public interface IStated{ 
		function get states():StateGroup;
		function setStateVal(stateName:String,val:Object):Boolean;
		function getStateVal(stateName:String):Object;
	}
}