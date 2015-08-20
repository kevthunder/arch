package com.flashkev.particles{
	import flash.events.Event;
	import flash.display.MovieClip;
	

	public class Particle extends MovieClip{
		//////////// variables ////////////
		protected var _group:ParticleGroup;
		protected var mods:Object;
		protected var ordMods:Array;
	
		//////////// Constructor ////////////
		public function Particle() {
			ordMods = new Array();
			mods = new Object();
			
			addEventListener(Event.ADDED_TO_STAGE,LoadHdl);
			addEventListener(Event.REMOVED_FROM_STAGE,UnloadHdl);
		}
			
		//////////// Properties functions ////////////
		public function get group():ParticleGroup{
			return _group;
		}
		public function set group(val:ParticleGroup){
			if(_group != val){
				if(_group){
					_group.removeParticle(this);
				}
				_group = val;
				if(_group && parent){
					_group.addParticle(this);
				}
			}
		}
		public function get scale():Number{
			return (scaleX + scaleY)/2;
		}
		public function set scale(val:Number):void{
			var ratio:int = scaleX / scaleY;
			var halfRatio = (ratio-1)/2+1;
			scaleX = val * halfRatio;
			scaleY = val / halfRatio;
			
		}
			
		//////////// Public functions ////////////
		public function addMods(mods:Object):void{
			var id:String;
			for(id in mods){
				if(mods[id] is ParticleMod){
					_addMod(id,mods[id]);
				}
			}
			for(id in mods){
				if(mods[id] is ParticleMod){
					mods[id].onInit();
				}
			}
			updateModOrder();
		}
		public function addMod(id:String,mod:ParticleMod):void{
			_addMod(id,mod);
			mods[id].onInit();
			updateModOrder();
		}
		public function getMod(id:String):ParticleMod{
			return mods[id];
		}
		public function getModId(mod:ParticleMod):String{
			for(var id:String in mods){
				if(mods[id] === mod){
					return id;
				}
			}
			return null;
		}
		public function removeMod(id:String):void{
			mods[id].particle = null;
			delete mods[id];
			updateModOrder()
		}
		public function clearMods():void{
			for(var id:String in mods){
				mods[id].particle = null;
			}
			mods = new Object();
			ordMods = new Array();
		}
		//////////// Private functions ////////////
		protected function _addMod(id:String,mod:ParticleMod):void{
			mods[id] = mod;
			mods[id].particle = this;
		}
		protected function updateModOrder(){
			ordMods = new Array();
			for(var id:String in mods){
				ordMods.push(mods[id]);
			}
			ordMods.sortOn('priority', Array.NUMERIC);
		}
			
		//////////// Event Handlers functions ////////////
		protected function enterFrameHdl(e:Event){
			for(var i:Number=0; i<ordMods.length; i++){
				ordMods[i].onTick();
			}
		}
		
		protected function LoadHdl(e:Event){
			addEventListener(Event.ENTER_FRAME,enterFrameHdl);
			for(var i:Number=0; i<ordMods.length; i++){
				ordMods[i].onInit();
			}
			if(_group){
				_group.addParticle(this);
			}
		}
		protected function UnloadHdl(e:Event){
			removeEventListener(Event.ENTER_FRAME,enterFrameHdl);
			if(_group){
				_group.removeParticle(this);
			}
		}
	}
}
