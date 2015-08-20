package  {
	import flash.events.EventDispatcher;
	
	dynamic public class AbilityInstance extends EventDispatcher{
		//////////// variables ////////////
		protected var _ability:Ability;
		protected var _user:Character;
		//////////// Static variables ////////////
		
		//////////// Constructor ////////////
        public function AbilityInstance(ability:Ability,user:Character) {
			_ability = ability;
			_user = user;
        }
		
		//////////// Properties functions ////////////
		public function get ability():Ability{
			return _ability;
		}
		
		public function get user():Character{
			return _user;
		}
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		
		//////////// Private functions ////////////
		
		//////////// Event Handlers functions ////////////

	}
	
}
