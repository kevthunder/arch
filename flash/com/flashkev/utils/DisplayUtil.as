package com.flashkev.utils{
	import flash.display.DisplayObject;
	import flash.display.DisplayObjectContainer;

	public class DisplayUtil{
		//////////// variables ////////////

		//////////// Constructor ////////////
		
		public function DisplayUtil() {
		}

		//////////// Properties functions ////////////
		

		//////////// Public functions ////////////
		public static function hasParentOfType(target:DisplayObject, type:Class):Boolean{
			var p:DisplayObjectContainer = target.parent;
			while(p){
				if(p is type){
					return true;
				}
				p = p.parent;
			}
			return false;
		}
		public static function hasParent(target:DisplayObject, parent:DisplayObjectContainer):Boolean{
			var p:DisplayObjectContainer = target.parent;
			while(p){
				if(p == parent){
					return true;
				}
				p = p.parent;
			}
			return false;
		}
		//////////// Private functions ////////////
		
		
	}
}