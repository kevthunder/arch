package com.flashkev.state {
	import flash.events.Event;
	import flash.events.EventDispatcher;
	
	
	public class StateGroup extends EventDispatcher{
		//////////// variables ////////////
		protected var _states:Array = new Array();
		protected var _owner:Object;//legacy
		
		//////////// Static variables ////////////
		
		//////////// Constructor ////////////
        public function StateGroup(... states) {
			addStates(states);
        }
		
		//////////// Properties functions ////////////
		public function get states():Array{
			return _states;
		}
		public function set states(val:Array){
			clear();
			addStates(val);
		}
		
		public function get owner():Object{//legacy
			return _owner;
		}
		public function set owner(val:Object){//legacy
			if(val != _owner){
				setOwner(val);
			}
		}
		//////////// Static functions ////////////
		public static function newButtonStates(owner:IStated):StateGroup{
			var states:StateGroup = new StateGroup(
				new State("selected","selected"),
				new CompoundState(
					new State("disabled","enabled",{"true":"","false":"disabled"}),
					new State("mouse","curMouseState")
					)
			);
			states.owner = owner;
			return states;
		}
		
		//////////// Public functions ////////////
		public function addStates(states:Object){
			var i:int;
			if(states is Array){
				for(i=0;i<states.length;i++){
					addStates(states[i]);
				}
			}else{
				addState(states);
			}
		}
		public function addState(state:Object,pos:int= int.MAX_VALUE):int{
			if(state is StateGroup || state is State) {
				pos = Math.min(_states.length-1,pos);
				pos = Math.max(0,pos);
				_states.splice(pos,0,state);
				state.owner = owner;//legacy
				state.addEventListener(StateEvent.STATE_CHANGE,stateChangeHandler);
				return pos;
			}
			return -1;
		}
		public function clear(){
			_states = new Array();
		}
		public function clone():StateGroup{
			var clone:StateGroup = new StateGroup();
			for(var i:int = 0; i<_states.length; i++){
				clone.states.push(_states[i].clone());
			}
			return clone;
		}
		
		public function setStateVal(stateName:String,val:Object):Boolean{
			for(var i:int = 0;i<_states.length;i++){
				if(_states[i] is StateGroup){
					if(_states[i].setStateVal(stateName,val)){
						return true;
					}
				}else if(_states[i] is State){
					if(_states[i].name == stateName){
						_states[i].value = val;
						return true;
					}
				}
			}
			return false;
		}
		
		public function getStateVal(stateName:String):Object{
			for(var i:int = 0;i<_states.length;i++){
				if(_states[i] is StateGroup){
					var val:Object = _states[i].getStateVal(stateName)
					if(val != null){
						return val;
					}
				}else if(_states[i] is State){
					if(_states[i].name == stateName){
						return _states[i].value;
					}
				}
			}
			return null;
		}
		
		public function getStrings(sufix:String):Array{
			var strings:Array = [sufix];
			for(var i:int = 0;i<_states.length;i++){
				var stateStrings:Array = new Array();
				if(_states[i] is StateGroup){
					stateStrings = _states[i].getStrings(sufix);
				}else if(_states[i] is State){
					stateStrings = [_states[i].toString()];
				}
				for(var k:int = 0;k<stateStrings.length;k++){
					var stateStr:String = stateStrings[k];
					if(stateStr !== ""){
						var lastLength:int = strings.length;
						for(var j:int = 0;j<lastLength;j++){
							strings.push(stateStr + strings[j].substr(0,1).toUpperCase() + strings[j].substr(1));
						}
					}
				}
			}
			//trace(strings);
			return strings;
		}
		
		override public function toString():String{
			var str:String = "";
			var i:int;
			for(i = 0;i<_states.length;i++){
				var stateStr:String = _states[i].toString();
				if(str != ""){
					stateStr + str.substr(0,1).toUpperCase() + str.substr(1);
					return str;
				}
			}
			return str;
		}
		
		//////////// Private functions ////////////
		protected function setOwner(val:Object){//legacy
			for(var i:int = 0; i<_states.length; i++){
				_states[i].owner = val;
			}
		}
		//////////// Event Handlers functions ////////////
		protected function stateChangeHandler(e:StateEvent){
			dispatchEvent(new StateEvent(StateEvent.STATE_CHANGE,e.eventName));
		}
	}
}