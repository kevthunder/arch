package com.flashkev.state{
	import flash.events.Event;
	
	
	public class StateEvent extends Event{
		//////////// variables ////////////
		protected var _eventName:String;
		
		public static var STATE_CHANGE:String = "state change";
		
		
		//////////// Constructor ////////////
        public function StateEvent(type:String, eventName:String, bubbles:Boolean = false, cancelable:Boolean = false) {
			_eventName = eventName;
			super(type, bubbles, cancelable);
        }
		
		//////////// Properties functions ////////////
		public function get eventName():String{
			return _eventName;
		}
	}
}