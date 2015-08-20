package com.flashkev.particles{
	
	public class ParticleMod extends Object{
		//////////// variables ////////////
		protected var _priority:Number = 0;
		protected var _particle:Particle;
	
		//////////// Constructor ////////////
		public function ParticleMod(priority:Number = 0) {
			_priority = priority;
		}
			
		//////////// Properties functions ////////////
		public function get priority():Number{
			return _priority;
		}
		public function set priority(val:Number){
			_priority = val;
		}
		
		public function get particle():Particle{
			return _particle;
		}
		public function set particle(val:Particle){
			_particle = val;
		}
			
		//////////// Public functions ////////////
		public function onInit(){
			
		}
		
		public function onTick(){
			
		}
		
		public function clone():ParticleMod{
			var clone = new ParticleMod();
			clone.priority = this.priority;
			return clone;
		}
		//////////// Private functions ////////////
			
		//////////// Event Handlers functions ////////////
		
	}
}
