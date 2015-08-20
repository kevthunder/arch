package  {
	import flash.events.Event;
	
	[Event(name="cast", type="EventFromSaved")]
	[Event(name="casted", type="EventFromSaved")]
	public class EventFromSaved extends Event {
		//////////// variables ////////////
		protected var _savedEvent:SavedEvent;
		
		//////////// Static variables ////////////
		public static const CAST:String = 'cast';
		public static const CASTED:String = 'casted';
		
		//////////// Constructor ////////////
        public function EventFromSaved(type:String, savedEvent:SavedEvent, bubbles:Boolean = false, cancelable:Boolean = false) {
			_savedEvent = savedEvent;
			super(type,bubbles,cancelable);
        }
		
		//////////// Properties functions ////////////
		public function get savedEvent():SavedEvent{
			return _savedEvent;
		}
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		
		//////////// Private functions ////////////
		
		
		//////////// Event Handlers functions ////////////

	}
	
}
