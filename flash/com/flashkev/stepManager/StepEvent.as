package com.flashkev.stepManager{
	import flash.events.Event;
	public class StepEvent extends Event{
		//////////// variables ////////////
		public var curStep:String;
		public var lastStep:String;
		public var stepManager:StepManager;
		
		public static const STEP_START:String = 'start';
		public static const STEP_END:String = 'end';
		//////////// Constructor ////////////
        public function StepEvent(type:String, stepManager:StepManager, curStep:String, lastStep:String, bubbles:Boolean = false, cancelable:Boolean = false) {
			this.curStep = curStep;
			this.lastStep = lastStep;
			this.stepManager = stepManager;
			super(type, bubbles, cancelable);
        }
		
		//////////// Properties functions ////////////
		
		//////////// Public functions ////////////
		override public function clone():Event {
            return new StepEvent(type, stepManager, curStep, lastStep, bubbles, cancelable);
        }

		//////////// Private functions ////////////
		
	}
}