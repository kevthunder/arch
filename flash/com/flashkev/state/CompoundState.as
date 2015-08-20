package com.flashkev.state {
	
	
	public class CompoundState extends StateGroup{
		//////////// variables ////////////
		
		//////////// Constructor ////////////
        public function CompoundState(... states) {
			addStates(states);
        }
		
		//////////// Properties functions ////////////
		
		
		//////////// Public functions ////////////
		override public function clone():StateGroup{
			var clone:CompoundState = new CompoundState();
			for(var i:int = 0; i<_states.length; i++){
				clone.states.push(_states[i].clone());
			}
			return clone;
		}
		
		override public function getStrings(sufix:String):Array{
			return [this.toString()];
		}
		
		override public function toString():String{
			var str:String = "";
			var i:int;
			for(i = 0;i<_states.length;i++){
				str = _states[i].toString();
				if(str != ""){
					return str;
				}
			}
			return "";
		}
		
		//////////// Private functions ////////////
		
	}
}