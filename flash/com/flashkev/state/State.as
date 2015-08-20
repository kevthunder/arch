package com.flashkev.state {
	import flash.events.Event;
	import flash.events.EventDispatcher;
	
	public class State extends EventDispatcher{
		//////////// variables ////////////
		protected var _name:String;
		protected var _owner:Object;
		protected var _prop:String;
		protected var _value:Object;
		protected var _stringMap:Object;
		
		//////////// Constructor ////////////
        public function State(name:String,prop:String = null,stringMap:Object = null) {
			_name = name;
			_prop = prop;
			_stringMap = stringMap;
        }
		
		//////////// Properties functions ////////////
		public function get name():String{
			return _name;
		}
		public function set name(val:String){
			_name = val;
		}
		
		public function get owner():Object{//legacy
			return _owner;
		}
		public function set owner(val:Object){//legacy
			_owner = val;
		}
		
		public function get prop():String{//legacy
			return _prop;
		}
		public function set prop(val:String){//legacy
			_prop = val;
		}
		
		public function get value():Object{
			return _value;
		}
		public function set value(val:Object){
			if(_value !== val){
				_value = val;
				dispatchEvent(new StateEvent(StateEvent.STATE_CHANGE,name));
			}
		}
		
		public function get stringMap():Object{
			return _stringMap;
		}
		public function set stringMap(val:Object){
			_stringMap = val;
		}
		
		//////////// Public functions ////////////
		public function clone():State{
			var clone:State = new State(name,prop,stringMap);
			clone.value = value;
			return clone;
		}
		
		override public function toString():String{
			var val:Object;
			if(value !== null){
				val = value
			}else if(owner && prop && owner.hasOwnProperty(prop)){
				val = owner[prop]
			}
			if(val !== null){
				if(stringMap){
					if(stringMap.hasOwnProperty(val.toString())){
						return stringMap[val.toString()];
					}else{
						return "";
					}
				}else{
					if(val is Boolean){
						return (val)?name:"";
					}else{
						return val.toString();
					}
				}
			}else{
				return "";
			}
		}
		
		//////////// Private functions ////////////
		
	}
}