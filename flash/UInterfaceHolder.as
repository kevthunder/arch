package {
	import flash.events.Event;

	public class UInterfaceHolder extends UInterface {
		//////////// variables ////////////
		protected var _current:UInterface;
		
		//////////// Static variables ////////////
		
		
		//////////// Constructor ////////////
        public function UInterfaceHolder() {
			open();
        }
		
		//////////// Properties functions ////////////
		public function get current():UInterface{
			return _current;
		}
		
		public function set current(val:UInterface):void{
			if(_current != val){
				if(_current){
					_current.removeEventListener(Event.CLOSE,currentCloseHandler);
					_current.opened = false;
				}
				_current = val;
				if(_current){
					_current.addEventListener(Event.CLOSE,currentCloseHandler);
					_current.holder = this;
					_current.opened = true;
				}
			}
		}
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		
		
		//////////// Private functions ////////////
		
		
		//////////// Event Handlers functions ////////////
		protected function currentCloseHandler(e:Event){
			if(e.target == current){
				current = null;
			}
		}
		
	}
}