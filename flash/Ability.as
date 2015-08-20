package  {
	
	public class Ability extends Skill{
		//////////// variables ////////////
		
		//////////// Static variables ////////////
		
		//////////// Constructor ////////////
        public function Ability(name = null) {
			super(name);
        }
		
		//////////// Properties functions ////////////
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		public function useNow(user:Character):Boolean{
			var instance:AbilityInstance = getInstance(user);
			
			return useInstance(instance);
		}
		
		//////////// Private functions ////////////
		protected function getInstance(user:Character):AbilityInstance{
			return new AbilityInstance(this,user);
		}
		
		protected function useInstance(inst:AbilityInstance){
			return false;
		}
		
		//////////// Event Handlers functions ////////////

	}
	
}
