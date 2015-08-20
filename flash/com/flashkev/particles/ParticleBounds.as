package com.flashkev.particles{
	import flash.geom.Point;
	import flash.geom.Rectangle;
	import flash.display.DisplayObject;
	
	public class ParticleBounds extends ParticleMod{
		//////////// variables ////////////
		protected var _rect:Rectangle = null;
		protected var _rectClip:DisplayObject = null;
	
		//////////// Constructor ////////////
		public function ParticleBounds(rect:Rectangle = null) {
			_rect = rect;
		}
			
		//////////// Properties functions ////////////
		public function get rect():Rectangle{
			if(_rectClip){
				return new Rectangle(_rectClip.x,_rectClip.y,_rectClip.width,_rectClip.height);
			}
			return _rect;
		}
		public function set rect(val:Rectangle){
			_rect = val;
		}
		
		public function get rectClip():DisplayObject{
			return _rectClip;
		}
		public function set rectClip(val:DisplayObject){
			_rectClip = val;
		}
			
		//////////// Public functions ////////////
		override public function onInit(){
			
		}
		
		override public function onTick(){
			if(rect){
				if(particle.x<rect.x){
					outOfBound();
				}
				if(particle.x>rect.right){
					outOfBound();
				}
				if(particle.y<rect.y){
					outOfBound();
				}
				if(particle.y>rect.bottom){
					outOfBound();
				}
			}
		}
		
		override public function clone():ParticleMod{
			var clone = new ParticleBounds();
			clone.priority = this.priority;
			clone.rect = this.rect;
			clone.rectClip = this.rectClip;
			return clone;
		}
		
		public function outOfBound(){
			particle.parent.removeChild(particle);
		}
		
		//////////// Private functions ////////////
			
		//////////// Event Handlers functions ////////////
		
	}
}
