package com.flashkev.particles{
	import flash.geom.Point;
	
	public class ParticleLetterChanger extends ParticleMod{
		//////////// variables ////////////
		
		
		protected var _original_letter:String;
		protected var _timeout:Number = 0;
		protected var _timeout2:Number = 0;
	
		//////////// Constructor ////////////
		public function ParticleLetterChanger(priority:Number) {
			
		}
			
		//////////// Properties functions ////////////
		
			
		//////////// Public functions ////////////
		public function onInit(){
			if(particle.hasOwnProperty('txt')){
				_original_letter = particle['txt'].text;
				_timeout = particle.getDepth()*4;
				_timeout2 = _timeout+10;
				particle.visible=false;
				//trace(_original_letter);
			}
		}
		
		public function onTick(){
			if(_timeout>0){
				particle.visible=false;
				//trace(_timeout);
				_timeout--;
			}else{
				particle.visible=true;
			}
			if(_timeout2>0){
				//trace(String.fromCharCode(97+Math.floor(Math.random()*26)));
				particle['txt'].text = String.fromCharCode(97+Math.floor(Math.random()*26));
				_timeout2--;
			}else{
				particle['txt'].text = _original_letter;
			}
		}
		
		public function clone():ParticleMod{
			var clone = new ParticleLetterChanger();
			clone.priority = this.priority;
			return clone;
		}
		
		//////////// Private functions ////////////
			
		//////////// Event Handlers functions ////////////
		
	}
}
