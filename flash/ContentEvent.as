package  {
	import flash.events.Event;
	
	public class ContentEvent extends Event{
		//////////// variables ////////////
		protected var _content:Object;
		
		//////////// Static variables ////////////
		
		//////////// Constructor ////////////
        public function ContentEvent(type:String, content:Object, bubbles:Boolean = false, cancelable:Boolean = false) {
			_content = content;
			super(type, bubbles, cancelable);
        }
		
		//////////// Properties functions ////////////
		
		public function get content():Object{
			return _content;
		}
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		
		//////////// Private functions ////////////
		

	}
	
}
