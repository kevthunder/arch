package com.flashkev.particles{
		
	public class SimpleParticleGroup extends ParticleGroup{
		//////////// variables ////////////
		
		//////////// Constructor ////////////
		public function SimpleParticleGroup(mods:Object = null) {
			addMods({velocity:new ParticleVelocity(0,0),wind:new ParticleWindSimulator(3,0.1,0,4,70)});
		}
			
		//////////// Properties functions ////////////
			
		//////////// Public functions ////////////
		
		//////////// Private functions ////////////
		
		//////////// Event Handlers functions ////////////
		
	}
}
