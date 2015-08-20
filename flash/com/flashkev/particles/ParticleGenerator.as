package com.flashkev.particles{
	import flash.events.Event;
	import flash.display.MovieClip;
	import flash.utils.getTimer;
	import flash.utils.getQualifiedClassName;
	import flash.utils.getDefinitionByName;
	import flash.geom.Point;
	

	public class ParticleGenerator extends MovieClip{
		//////////// variables ////////////
		protected var _particleType:Class;
		protected var _perSec:int = 5;
		protected var _ready:Boolean;
		protected var _lastTime:int;
	
		//////////// Constructor ////////////
		public function ParticleGenerator() {
			addEventListener(Event.ADDED_TO_STAGE,LoadHdl);
			addEventListener(Event.REMOVED_FROM_STAGE,UnloadHdl);
		}
			
		//////////// Properties functions ////////////
		public function get particleType():Class{
			return _particleType;
		}
		public function set particleType(val:Class):void{
			if(_particleType != val){
				_particleType = val; 
				tcheckReady();
			}
		}
		
		[Inspectable(defaultValue=5, type="Number")]
		public function get perSec():int{
			return _perSec;
		}
		public function set perSec(val:int):void{
			if(_perSec != val){
				_perSec = val; 
				tcheckReady();
			}
		}
		
		[Inspectable(defaultValue="", type="String")]
		public function get particleTypeName():String{
			if(!_particleType){
				return "false";
			}
			return getQualifiedClassName(_particleType);
		}
		public function set particleTypeName(val:String):void{
			if(val == "false"){
				particleType = null;
			}else{
				var c:Class = getDefinitionByName(val) as Class;
				if(c){
					particleType = c;
				}
			}
		}
			
		//////////// Public functions ////////////
		public function generateParticles(nb:int){
			for(var i:int =0; i<nb; i++){
				var particle:Particle = new particleType() as Particle;
				if(particle){
					var loc:Point = getParticleLocation();
					particle.x = loc.x;
					particle.y = loc.y;
					var zIndex:int = parent.getChildIndex(this);
					parent.addChildAt(particle,zIndex);
				}
			}
		}
		
		//////////// Private functions ////////////
		protected function getParticleLocation():Point{
			return new Point(this.x + Math.random()*this.width,this.y + Math.random()*this.height);
		}
		protected function tcheckReady(){
			var val:Boolean = (_particleType && _perSec > 0 && stage)
			if(val != _ready){
				_ready = val;
				if(val){
					_lastTime = getTimer();
					addEventListener(Event.ENTER_FRAME,enterFrameHdl);
				}else{
					removeEventListener(Event.ENTER_FRAME,enterFrameHdl);
				}
			}
		}
			
		//////////// Event Handlers functions ////////////
		protected function enterFrameHdl(e:Event){
			var newTime:int = getTimer();
			var nb:int = (newTime-_lastTime)/1000*_perSec;
			if(nb>0){
				nb = Math.min(perSec,nb);
				_lastTime = getTimer();
				generateParticles(nb);
			}
		}
		
		protected function LoadHdl(e:Event){
			tcheckReady();
		}
		protected function UnloadHdl(e:Event){
			tcheckReady();
		}
		
	}
}
