package com.flashkev.utils{
	import flash.events.Event;
	import flash.events.IEventDispatcher;

	// Ex:
	// myBtn.addEventListener(MouseEvent.CLICK,ListenOnce.makeFunct(myFunct,'foo'));
	// function myFunct(e:Event,caption){
	//		trace(caption); // output : foo
	// }
	//
	public class ListenOnce extends Object
	{
		//////////// variables ////////////
		
		//////////// Constructor ////////////

		//////////// Properties functions ////////////
		
		//////////// functions ////////////
		public static function makeFunct(funct:Function,... rest):Function{
			var newFunct:Function = function(e:Event){
				funct.apply(this, [e].concat(rest));
				(e.currentTarget as IEventDispatcher).removeEventListener(e.type,arguments.callee);
			};
			return newFunct;
		}
	}
}