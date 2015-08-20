package com.flashkev.stepManager{
	import flash.display.DisplayObject;
	import flash.display.DisplayObjectContainer;
	import flash.events.Event;
	
	public class DisplayBindedStep extends Step{
		//////////// variables ////////////
		protected var _display:Object;
		protected var _parent:DisplayObjectContainer;
		protected var _instance:DisplayObject;
		
		public static const EVENT_INSTANCE_INIT = "instance init";
		//////////// Constructor ////////////
		public function DisplayBindedStep(id:String,display:Object,parent:DisplayObjectContainer){
			//_fowardableEvents.push();
			super(id);
			_display = display;
			_parent = parent;
			addEventListener(StepEvent.STEP_START,startHandler, false, 5);
			addEventListener(StepEvent.STEP_END,endHandler, false, -5);
			for(var i:int = 0; i<fowardableEvents.length; i++){
				addEventListener(fowardableEvents[i],fowardEvent);
			}
		}
      
		//////////// Properties functions ////////////
		public function get display():Object{
			return _display;
		}
		public function set display(val:Object):void{
			_display = val;
		}
		
		public function get parent():DisplayObjectContainer{
			return _parent;
		}
		public function set parent(val:DisplayObjectContainer):void{
			_parent = val;
		}
		
		public function get instance():DisplayObject{
			return _instance;
		}
		
		//////////// Public functions ////////////
		
		//////////// Private functions ////////////
		
		//Deprecated : use EVENT_INSTANCE_INIT event instead.
		//Legacy : override if you want to do something on the creation of the instance.
		protected function initInstance(){
			
		}
		
		//////////// Event Handlers functions ////////////
		protected function fowardEvent(e:StepEvent){
			if(_instance != null){
				_instance.dispatchEvent(e);
			}
		}
		protected function startHandler(e:StepEvent){
			if(_instance == null){
				if(_display is Class){
					_instance = (new _display()) as DisplayObject;
				}else if(_display is DisplayObject){
					_instance = _display as DisplayObject;
				}
				initInstance();
				dispatchEvent(new Event(EVENT_INSTANCE_INIT));
			}
			if(_instance != null && _parent != null){
				_parent.addChild(_instance);
				
				//trace("test");
			}
		}
		
		protected function endHandler(e:StepEvent){
			if(_instance != null){
				_instance.parent.removeChild(_instance);
			}
		}
		
		
	}
}