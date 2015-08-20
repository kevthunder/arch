package com.flashkev.genericList{
	import flash.events.Event;
	
	
	public class GenericRendererEvent extends Event{
		//////////// variables ////////////
		public static var INIT:String = "init";
		public static var REMOVE:String = "remove";
		public static var MOVE:String = "move";
		public static var DATACHANGE:String = "data change";
		
		
		//////////// Constructor ////////////
        public function GenericRendererEvent(type:String, renderer:IGenericRenderer=null, bubbles:Boolean = false, cancelable:Boolean = false) {
			super(type, bubbles, cancelable);
        }
		
	}
}