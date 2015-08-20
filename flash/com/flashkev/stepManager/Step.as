package com.flashkev.stepManager{
	import flash.events.EventDispatcher;
	import flash.events.Event;
	
	public class Step extends EventDispatcher{
		//////////// variables ////////////
		protected var _id:String;
		protected var _fowardableEvents:Array = [StepEvent.STEP_START,StepEvent.STEP_END];
		
		//////////// Constructor ////////////
		public function Step(id:String){
			_id = id;
		}
      
		//////////// Properties functions ////////////
		
		public function get id():String{
			return _id;
		}
		/*public function set id(val:String):void{
			_id = val;
		}*/
		public function get fowardableEvents():Array{
			return _fowardableEvents.concat();
		}
		
		
		//////////// Public functions ////////////
		override public function toString():String{
			return id;
		}
		
		//////////// Private functions ////////////
		
		
		//////////// Event Handlers functions ////////////
		
		
		
	}
}