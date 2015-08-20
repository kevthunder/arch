package com.flashkev.events {
	import flash.events.Event;

	public class CommonEvent extends Event{
		//////////// variables ////////////
		
		//////////// Static variables ////////////
		public static const SUBMIT:String = "submit";
		
		//////////// Constructor ////////////
		public function CommonEvent(type:String, bubbles:Boolean = false, cancelable:Boolean = false) {
			super(type, bubbles, cancelable);
        }
		
		//////////// Properties functions ////////////
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		
		//////////// Private functions ////////////
		
		//////////// Event Handlers functions ////////////
		
	}
}