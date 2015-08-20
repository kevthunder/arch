package {
	import flash.display.MovieClip;
	import flash.display.InteractiveObject;
	import flash.events.MouseEvent;
	import flash.events.Event;

	[Event(name="open", type="Event")]
	[Event(name="close", type="Event")]
	public class Popup extends MovieClip {
		//////////// variables ////////////
		protected var _btClose:InteractiveObject;
		protected var _opened:Boolean;
		
		//////////// Static variables ////////////
		
		
		//////////// Constructor ////////////
        public function Popup() {
			visible = false;
        }
		
		//////////// Properties functions ////////////
		
		public function get opened():Boolean{
			return _opened;
		}
		
		public function set opened(val:Boolean):void{
			if(_opened != val){
				_opened = val;
				visible = _opened;
				if(_opened){
					dispatchEvent(new Event(Event.OPEN));
				}else{
					dispatchEvent(new Event(Event.CLOSE));
				}
			}
		}
		
		public function set btClose(val:InteractiveObject):void{
			if(_btClose != val){
				if(_btClose){
					_btClose.removeEventListener(MouseEvent.CLICK,closeClickHandler);
				}
				_btClose = val;
				if(_btClose){
					_btClose.addEventListener(MouseEvent.CLICK,closeClickHandler);
				}
			}
		}
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		
		
		//////////// Private functions ////////////
		
		
		//////////// Event Handlers functions ////////////
		protected function closeClickHandler(e:Event){
			opened = false;
		}
		
	}
}