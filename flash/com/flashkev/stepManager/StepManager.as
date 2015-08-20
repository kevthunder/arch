package com.flashkev.stepManager{
	import flash.events.EventDispatcher;
	import flash.events.Event;
	
	public class StepManager extends EventDispatcher{
		//////////// variables ////////////
		public var steps:Object = new Object();
		public var stepsOrder:Array = new Array();
		
		protected var _curStep:String;
		
		//////////// Constructor ////////////
		public function StepManager(){
			
		}
      
		//////////// Properties functions ////////////
		public function get curStepIndex():int{
			return getStepIndex(curStep);
		}
		public function set curStepIndex(val:int){
			if(val<stepsOrder.length){
				curStep = stepsOrder[val].id;
			}
		}
		
		public function get curStep():String{
			return _curStep
		}
		public function set curStep(val:String){
			if(_curStep != val && (val == null || steps.hasOwnProperty(val)) ){
				var lastStep:String = _curStep;
				var allowChange:Boolean = true
				var e:Event;
				if(lastStep){
					e = new StepEvent(StepEvent.STEP_END, this, val, lastStep, false, true);
					allowChange = steps[lastStep].dispatchEvent(e);
				}
				if(allowChange){
					_curStep = val;
					if(_curStep!= null){
						e = new StepEvent(StepEvent.STEP_START, this, _curStep, lastStep);
						steps[_curStep].dispatchEvent(e);
					}
				}
			}
		}
		
		public function get curStepObj():Step{
			return getStepObj(curStep);
		}
		
		public function set curStepObj(val:Step){
			for(var i:int = 0 ; i<stepsOrder.length; i++){
				if(stepsOrder[i]===val){
					curStep = stepsOrder[i].id;
					return;
				}
			}
		}
		
		
		//////////// Public functions ////////////
		
		public function addStep(id:String,StartHandler:Function = null,EndHandler:Function = null){
			addStepAt(id,int.MAX_VALUE,StartHandler,EndHandler);
		}
		public function addStepAt(id:String,order:int=int.MAX_VALUE,StartHandler:Function = null,EndHandler:Function = null){
			var step:Step = new Step(id);
			addStepObjAt(step,order,StartHandler,EndHandler);
		}
		
		public function addStepObj(step:Step,StartHandler:Function = null,EndHandler:Function = null){
			addStepObjAt(step,int.MAX_VALUE,StartHandler,EndHandler);
		}
		
		public function addStepObjAt(step:Step,order:int=int.MAX_VALUE,StartHandler:Function = null,EndHandler:Function = null){
			steps[step.id] = step;
			if(StartHandler != null){
				step.addEventListener(StepEvent.STEP_START,StartHandler);
			}
			if(EndHandler != null){
				step.addEventListener(StepEvent.STEP_END,EndHandler);
			}
			for(var i:int = 0; i<step.fowardableEvents.length; i++){
				step.addEventListener(step.fowardableEvents[i],fowardInEvent);
			}
			if(order > stepsOrder.length){
				stepsOrder.push(step);
			}else{
				stepsOrder.splice(order,0,step);
			}
		}
		
		public function getStepObj(id:String):Step{
			if(steps.hasOwnProperty(id)){
				return steps[id];
			}else{
				return null;
			}
		}
		
		public function getStepIndex(id:String):int{
			if(steps.hasOwnProperty(id)){
				return stepsOrder.indexOf(steps[id]);
			}else{
				return -1;
			}
		}
		
		public function nextStep(loopAround:Boolean = false):Boolean{
			if(curStepIndex<stepsOrder.length-1){
				curStepIndex++;
				return true;
			}else if(loopAround){
				curStepIndex = 0;
				return true;
			}
			return false;
		}
		
		public function previousStep(loopAround:Boolean = false):Boolean{
			if(curStepIndex>0){
				curStepIndex--;
				return true;
			}else if(loopAround){
				curStepIndex = stepsOrder.length-1;
				return true;
			}
			return false;
		}
		
		//////////// Private functions ////////////
		
		
		//////////// Event Handlers functions ////////////
		protected function fowardInEvent(e:Event){
			if(!dispatchEvent(e)){
				e.preventDefault();
			}
		}
		
		
	}
}