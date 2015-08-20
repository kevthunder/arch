package com.flashkev.particles{
	import flash.geom.Point;
	import flash.geom.Rectangle;
	import flash.display.DisplayObject;
	import flash.utils.getTimer;
	
	public class ParticleTimeout extends ParticleMod{
		//////////// variables ////////////
		protected var _time:uint;
		protected var _initTime:uint;
	
		//////////// Constructor ////////////
		public function ParticleTimeout(time:uint = 2000) {
			_time = time;
		}
			
		//////////// Properties functions ////////////
		public function get time():uint{
			return _time;
		}
		public function set time(val:uint){
			_time = val;
		}
		
			
		//////////// Public functions ////////////
		override public function onInit(){
			_initTime = getTimer();
		}
		
		override public function onTick(){
			if(getTimer() - _initTime > time){
				timedOut();
			}
		}
		
		override public function clone():ParticleMod{
			var clone = new ParticleTimeout();
			clone.priority = this.priority;
			clone.time = this.time;
			return clone;
		}
		
		public function timedOut(){
			particle.parent.removeChild(particle);
		}
		
		//////////// Private functions ////////////
			
		//////////// Event Handlers functions ////////////
		
	}
}
