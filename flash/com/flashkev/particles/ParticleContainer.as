package com.flashkev.particles{
	import flash.display.BitmapData;
	import flash.geom.Point;
	import flash.geom.Rectangle;
	import flash.geom.Matrix;
	import flash.geom.ColorTransform;
	import flash.events.Event;
		
	public class ParticleContainer extends MovieClip{
		//////////// variables ////////////
		protected var _particleType:String = "particle";
		
		protected var particles:Array;
		
		//////////// Constructor ////////////
		public function ParticleContainer() {
			particles = new Array();
			
			addEventListener(Event.ADDED_TO_STAGE,LoadHdl);
		}
			
		//////////// Properties functions ////////////
		public function get particleType():String{
			return _particleType;
		}
		
		public function set particleType(val:String){
			_particleType = val;
		}
		
			
		//////////// Public functions ////////////
		public function start(){
			//trace(_particleType);
			var list:Array = new Array();
			for(var i in this){
				if(!(this[i] instanceof Particle) && (this[i] instanceof MovieClip || this[i] instanceof TextField)){
					list.push(this[i]);
				}
			}
			for(var i =0; i<list.length;i++){
				list[i].visible = true;
				var pos:Point = new Point(list[i].x,list[i].y);
				//var particle:MovieClip = this.createEmptyMovieClip(_particleType+particles.length, this.getNextHighestDepth());
				var particle:MovieClip = this.attachMovie(_particleType, _particleType+particles.length, this.getNextHighestDepth());
				particle.x = pos.x;
				particle.y = pos.y;
				if(list[i] instanceof MovieClip){
					list[i].x = 0;
					list[i].y = 0;
					var particleBitmap:BitmapData = new BitmapData(list[i].width+1, list[i].height+1, true, 0x00000000);
					particleBitmap.draw(list[i],list[i].transform.matrix);
					particle.attachBitmap(particleBitmap,particle.getNextHighestDepth(),"auto",true);
					particles.push(particle);
					list[i].x = pos.x;
					list[i].y = pos.y;
				}else if(list[i] instanceof TextField){
					var textField:TextField = particle.createTextField("txt",particle.getNextHighestDepth(),0,0,list[i].width,list[i].height);
					textField.text = list[i].text;
					textField.setNewTextFormat(list[i].getTextFormat());
					textField.setTextFormat(list[i].getTextFormat());
					textField.embedFonts = list[i].embedFonts;
				}
				list[i].visible = false;
			}
		}
		//////////// Private functions ////////////
		
		
		//////////// Event Handlers functions ////////////
		protected function LoadHdl(e:Event){
			start();
		}
	}
}
