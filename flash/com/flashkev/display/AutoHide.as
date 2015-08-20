package com.flashkev.display {
	import flash.display.Sprite;
	import flash.display.DisplayObject;
	import flash.display.MovieClip;
	import flash.events.Event;

	public class AutoHide extends MovieClip {
		//////////// variables ////////////
		public var toHide:DisplayObject;

		//////////// Constructor ////////////
		
		public function AutoHide() {
			addEventListener(Event.ADDED_TO_STAGE,addedToStageHandler)
		}

		//////////// Properties functions ////////////
		

		//////////// functions ////////////
		protected function addedToStageHandler(e:Event){
			if(toHide){
				toHide.visible = false;
			}else{
				visible = false;
			}
		}
		
	}
}