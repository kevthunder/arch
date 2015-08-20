package com.flashkev.options {
	
	dynamic public class RelOption extends Object{
		//////////// variables ////////////
		protected var _value:String
		
		//////////// Constructor ////////////
        public function RelOption(value:String) {
			_value = value;
        }
		
		//////////// Properties functions ////////////
		public function get value():String{
			return _value;
		}
		public function set value(val:String){
			_value = val;
		}
		//////////// Public functions ////////////
		
		//////////// Private functions ////////////
		
		//////////// Event Handlers functions ////////////
		
		
	}
}