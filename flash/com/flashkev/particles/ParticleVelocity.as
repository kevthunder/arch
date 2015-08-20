package com.flashkev.particles{
	import flash.geom.Point;
	import com.flashkev.utils.GeomUtils;
	
	public class ParticleVelocity extends ParticleMod{
		//////////// variables ////////////
		protected var _spd:Number=0;
		protected var _angle:Number=0;
		protected var _rotation:Number=0;
	
		//////////// Constructor ////////////
		public function ParticleVelocity(spd:Number=0,angle:Number=0,rotation:Number=0,priority:Number=0) {
			super(priority);
			if(_spd == 0){
				_priority = 100;
			}
			_spd = spd;
			_angle = angle;
			_rotation = rotation;
		}
			
		//////////// Properties functions ////////////
		public function get spd():Number{
			return _spd;
		}
		public function set spd(val:Number){
			_spd = val;
		}
		public function get angle():Number{
			return _angle;
		}
		public function set angle(val:Number){
			_angle = val;
		}
		public function get rotation():Number{
			return _rotation;
		}
		public function set rotation(val:Number){
			_rotation = val;
		}
		public function get vector():Point{
			return Point.polar(_spd,_angle/180*Math.PI);
		}
		public function set vector(val:Point){
			spd = val.length;
			if(val.length){
				angle = GeomUtils.solveAngle(null,val,true);
			}
		}
			
		//////////// Public functions ////////////
		override public function onInit(){
			
		}
		
		override public function onTick(){
			particle.y+=vector.x;
			particle.x+=vector.y;
			particle.rotation+=_rotation;
		}
		
		override public function clone():ParticleMod{
			var clone = new ParticleVelocity();
			clone.priority = this.priority;
			clone.spd = this.spd;
			clone.angle = this.angle;
			clone.rotation = this.rotation;
			return clone;
		}
		
		//////////// Private functions ////////////
			
		//////////// Event Handlers functions ////////////
		
	}
}
