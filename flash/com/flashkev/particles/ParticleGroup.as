package com.flashkev.particles{
	public class ParticleGroup extends Object{
		//////////// variables ////////////
		protected var particles:Array;
		protected var mods:Object;
		
		//////////// Constructor ////////////
		public function ParticleGroup(mods:Object = null) {
			this.particles = new Array();
			this.mods = new Object();
			if(mods !== null){
				addMods(mods);
			}
		}
			
		//////////// Properties functions ////////////
			
		//////////// Public functions ////////////
		public function addParticle(particle:Particle){
			particles.push(particle);
			copyModsToParticle(mods,particle);
		}
		
		public function getParticlePos(particle:Particle){
			for(var i = 0;i<particles.length;i++){
				if(particles[i] == particle){
					return i;
				}
			}
			return -1;
		}
		
		public function removeParticle(particle:Particle){
			var index:Number = getParticlePos(particle);
			if(index != -1){
				particles.splice(index,1);
			}
		}
		
		
		
		public function addMods(mods:Object):void{
			for(var id:String in mods){
				if(mods[id] is ParticleMod){
					_addMod(id,mods[id]);
				}
			}
			copyModsToParticles(mods);
		}
		public function addMod(id:String,mod:ParticleMod):void{
			_addMod(id,mod);
			for(var i:Number = 0; i<particles.length; i++){
				particles[i].addMod(id,mod.clone());
			}
		}
		public function setModProperty(id:String,prop:String,val){
			mods[id][prop] = val;
			for(var i:Number = 0; i<particles.length; i++){
				particles[i].getMod(id)[prop] = val;
			}
		}
		public function removeMod(id:String):void{
			delete mods[id];
			for(var i:Number = 0; i<particles.length; i++){
				particles[i].removeMod(id);
			}
		}
		public function clearMods():void{
			for(var id:String in mods){
				mods[id].particle = null;
			}
			mods = new Object();
			for(var i:Number = 0; i<particles.length; i++){
				particles[i].clearMods();
			}
		}
		//////////// Private functions ////////////
		protected function _addMod(id:String,mod:ParticleMod):void{
			mods[id] = mod;
		}
		protected function copyModsToParticle(mods:Object,particle:Particle):void{
			var copied:Object = new Object();
			for(var id:String in mods){
				copied[id] = mods[id].clone();
			}
			particle.addMods(copied);
		}
		protected function copyModsToParticles(mods:Object):void{
			for(var i:Number = 0; i<particles.length; i++){
				copyModsToParticle(mods,particles[i]);
			}
		}
		//////////// Event Handlers functions ////////////
		
	}
}
