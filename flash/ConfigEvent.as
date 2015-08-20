package  {
	import flash.events.Event;
	
	public class ConfigEvent extends Event{
		//////////// variables ////////////
		protected var _configName:String;
		protected var _oldVal:Object;
		protected var _newVal:Object;
		
		//////////// Static variables ////////////
		public static const CHANGE:String = "config change";
		public static const CHANGED:String = "config changed";
		
		//////////// Constructor ////////////
		public function ConfigEvent(type:String, configName:String, oldVal:Object, newVal:Object, bubbles:Boolean = false, cancelable:Boolean = false) {
			_configName = configName;
			_oldVal = oldVal;
			_newVal = newVal;
			super(type, bubbles, cancelable);
		}
		
		//////////// Properties functions ////////////
		public function get configName():String{
			return _configName;
		}
		public function get oldVal():Object{
			return _oldVal;
		}
		public function get newVal():Object{
			return _newVal;
		}
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		
		//////////// Private functions ////////////
		

	}
	
}
