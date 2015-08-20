package {
	import flash.events.Event;
	public class CollectionEvent extends Event{
		//////////// variables ////////////
		public var item:Object;
		
		public static const ADDED:String = 'added';
		public static const REMOVED:String = 'removed';
		//////////// Constructor ////////////
        public function CollectionEvent(type:String, item:Object, bubbles:Boolean = false, cancelable:Boolean = false) {
			this.item = item;
			super(type, bubbles, cancelable);
        }
		
		//////////// Properties functions ////////////
		
		//////////// Public functions ////////////

		//////////// Private functions ////////////
		
	}
}