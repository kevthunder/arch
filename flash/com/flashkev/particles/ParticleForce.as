package com.flashkev.particles{
	import flash.geom.Point;

	public class ParticleForce extends ParticleMod{
		//////////// variables ////////////
		protected var _strength:Number;
		protected var _angle:Number;
		
		
		protected var _angleTendency:Number = 0;
	
		//////////// Constructor ////////////
		public function ParticleForce(strength:Number = 0.1, angle:Number = 0) {
			super(priority);
			_strength = strength;
			_angle = angle;
		}
			
		//////////// Properties functions ////////////
		public function get strength():Number{
			return _strength;
		}
		public function set strength(val:Number){
			_strength = val;
		}
		
		public function get angle():Number{
			return _angle;
		}
		public function set angle(val:Number){
			_angle = val;
		}
		
		public function get accelerationVector():Point{
			return Point.polar(_strength,_angle/180*Math.PI);
		}
			
		//////////// Public functions ////////////
		override public function onInit(){
		}
		
		override public function onTick(){
			var velocity:ParticleVelocity = particle.getMod('velocity') as ParticleVelocity;
			velocity.vector = velocity.vector.add(accelerationVector);
		}
		
		override public function clone():ParticleMod{
			var clone:ParticleForce = new ParticleForce();
			clone.priority = this.priority;
			clone.strength = this.strength;
			clone.angle = this.angle;
			return clone;
		}
		
		//////////// Private functions ////////////
			
		//////////// Event Handlers functions ////////////
		
	}
}
