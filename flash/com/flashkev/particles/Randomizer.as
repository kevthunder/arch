package com.flashkev.particles{
	import flash.geom.Point;
	import flash.geom.Rectangle;
	import flash.display.DisplayObject;
	
	public class Randomizer extends ParticleMod{
		//////////// variables ////////////
		protected var _property:String = null;
		protected var _min:int;
		protected var _max:int;
		protected var _targetMod:String = null;
		
		//////////// Constructor ////////////
		public function Randomizer(property:String = null, min:int = 0, max:int = 1, targetMod:String = null) {
			_property = property;
			_min = min;
			_max = max;
			_targetMod = targetMod;
		}
			
		//////////// Properties functions ////////////
		public function get property():String{
			return _property;
		}
		public function set property(val:String){
			_property = val;
		}
		
		public function get min():int{
			return _min;
		}
		public function set min(val:int){
			_min = val;
		}
		
		public function get max():int{
			return _max;
		}
		public function set max(val:int){
			_max = val;
		}
		
		public function get targetMod():String{
			return _targetMod;
		}
		public function set targetMod(val:String){
			_targetMod = val;
		}
		
		public function get target():Object{
			if(_targetMod){
				return particle.getMod(_targetMod);
			}
			return particle;
		}
			
		//////////// Public functions ////////////
		override public function onInit(){
			if(_property && target && target.hasOwnProperty(_property)){
				target[_property] = Math.random()*(max-min)+min;
			}
		}
		
		override public function onTick(){
			
		}
		
		override public function clone():ParticleMod{
			var clone = new Randomizer();
			clone.priority = this.priority;
			clone.property = this.property;
			clone.min = this.min;
			clone.max = this.max;
			clone.targetMod = this.targetMod;
			return clone;
		}
		
		
		//////////// Private functions ////////////
			
		//////////// Event Handlers functions ////////////
		
	}
}
