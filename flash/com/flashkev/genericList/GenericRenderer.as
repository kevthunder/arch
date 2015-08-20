package com.flashkev.genericList{
	import flash.display.MovieClip;
	
	public class GenericRenderer extends MovieClip implements IGenericRenderer { 
		//////////// variables ////////////
		protected var _data:Object;
		protected var _ownerList:GenericList;
		
		//////////// Constructor ////////////
		public function GenericRenderer() {
			
		}
		
		//////////// Properties functions ////////////
		public function get data():Object{
			return _data;
		}
		public function set data(val:Object):void{
			_data = val;
		}
		public function get ownerList():GenericList{
			return _ownerList;
		}
		public function set ownerList(val:GenericList):void{
			_ownerList = val;
		}
		
		//////////// Public functions ////////////
		
		//////////// Private functions ////////////
		
		//////////// Event Handlers functions ////////////
	}
	
}