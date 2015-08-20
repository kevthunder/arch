package com.flashkev.particles{
	import flash.geom.Point;

	public class ParticleWindSimulator extends ParticleMod{
		//////////// variables ////////////
		protected var _targetSpd:Number;
		protected var _acceleration:Number;
		protected var _targetAngle:Number;
		protected var _rotationSpd:Number;
		protected var _spreadAngle:Number;
		
		
		protected var _angleTendency:Number = 0;
	
		//////////// Constructor ////////////
		public function ParticleWindSimulator(targetSpd:Number = 3, acceleration:Number = 0.1, targetAngle:Number = 0, rotationSpd:Number = 4, spreadAngle:Number = 70, priority:Number = 0) {
			super(priority);
			_targetSpd = targetSpd;
			_acceleration = acceleration;
			_targetAngle = targetAngle;
			_rotationSpd = rotationSpd;
			_spreadAngle = spreadAngle;
		}
			
		//////////// Properties functions ////////////
		public function get targetSpd():Number{
			return _targetSpd;
		}
		public function set targetSpd(val:Number){
			_targetSpd = val;
		}
		
		public function get acceleration():Number{
			return _acceleration;
		}
		public function set acceleration(val:Number){
			_acceleration = val;
		}
		
		public function get targetAngle():Number{
			return _targetAngle;
		}
		public function set targetAngle(val:Number){
			_targetAngle = val;
		}
		
		public function get rotationSpd():Number{
			return _rotationSpd;
		}
		public function set rotationSpd(val:Number){
			_rotationSpd = val;
		}
		
		public function get spreadAngle():Number{
			return _spreadAngle;
		}
		public function set spreadAngle(val:Number){
			_spreadAngle = val;
		}
			
		//////////// Public functions ////////////
		override public function onInit(){
			var velocity = particle.getMod('velocity');
			velocity.rotation =  Math.random()*8 -4;
		}
		
		override public function onTick(){
			var velocity = particle.getMod('velocity');
			if(Math.floor(Math.random()*15)==0){
				_angleTendency = (Math.random()-(velocity.angle-_targetAngle+_spreadAngle)/_spreadAngle/2)*_rotationSpd;
			}
			velocity.angle += _angleTendency;
			
			if(velocity.spd != _targetSpd){
				if(Math.abs(_targetSpd - velocity.spd) < _acceleration){
					velocity.spd = _targetSpd;
				}else{
					velocity.spd += _acceleration * Math.abs(_targetSpd - velocity.spd)/(_targetSpd - velocity.spd);
				}
			}
		}
		
		override public function clone():ParticleMod{
			var clone = new ParticleWindSimulator();
			clone.priority = this.priority;
			clone.targetSpd = this.targetSpd;
			clone.acceleration = this.acceleration;
			clone.targetAngle = this.targetAngle;
			clone.rotationSpd = this.rotationSpd;
			clone.spreadAngle = this.spreadAngle;
			return clone;
		}
		
		//////////// Private functions ////////////
			
		//////////// Event Handlers functions ////////////
		
	}
}
