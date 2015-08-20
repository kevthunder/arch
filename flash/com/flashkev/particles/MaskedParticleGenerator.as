package com.flashkev.particles{
	import flash.events.Event;
	import flash.display.MovieClip;
	import flash.utils.getTimer;
	import flash.utils.getQualifiedClassName;
	import flash.utils.getDefinitionByName;
	import flash.display.Sprite;
	import flash.geom.Point;
	

	public class MaskedParticleGenerator extends ParticleGenerator{
		//////////// variables ////////////
		protected var _pmask:Sprite;
	
		//////////// Constructor ////////////
		public function MaskedParticleGenerator() {
		}
			
		//////////// Properties functions ////////////
		override protected function getParticleLocation():Point{
			var i:int = 0;
			do{
				var pos:Point = super.getParticleLocation();
				var validLocation:Boolean = _pmask.hitTestPoint(pos.x,pos.y,true);
				i++;
			}while(!validLocation && i<100);
			return pos;
		}
		
		
		public function get pmask():Sprite{
			return _pmask;
		}
		public function set pmask(val:Sprite):void{
			_pmask = val;
			_pmask.visible = false;
		}
			
		//////////// Public functions ////////////
		
		//////////// Private functions ////////////
			
		//////////// Event Handlers functions ////////////
		
	}
}
