package  {
	import flash.events.Event;
	
	public class SkillEvent extends Event{
		//////////// variables ////////////
		protected var _skillInstance:SkillInstance;
		
		//////////// Static variables ////////////
		public static const CAST:String = "cast";
		public static const CASTED:String = "casted";
		public static const SHOW_EFFECT:String = "showEffect";
		public static const INSTANCE_ADDED:String = "instanceAdded";
		
		//////////// Constructor ////////////
        public function SkillEvent(type:String, skillInstance:SkillInstance, bubbles:Boolean = false, cancelable:Boolean = false) {
			_skillInstance = skillInstance;
			super(type, bubbles, cancelable);
        }
		
		//////////// Properties functions ////////////
		
		public function get skillInstance():SkillInstance{
			return _skillInstance;
		}
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		
		//////////// Private functions ////////////
		

	}
	
}
