package {
	import flash.display.MovieClip;
	import flash.display.InteractiveObject;
	import flash.events.MouseEvent;
	import flash.events.Event;

	[Event(name="open", type="Event")]
	[Event(name="close", type="Event")]
	public class UInterface extends MovieClip {
		//////////// variables ////////////
		protected var _btClose:InteractiveObject;
		protected var _opened:Boolean;
		protected var _holder:UInterfaceHolder;
		
		//////////// Static variables ////////////
		
		
		//////////// Constructor ////////////
        public function UInterface() {
			updateOpenDisplay();
			addEventListener(Event.REMOVED_FROM_STAGE,RemovedFromStageHandler);
        }
		
		//////////// Properties functions ////////////
		
		public function get shown():Boolean{
			return _opened && stage != null;
		}
		
		public function get opened():Boolean{
			return _opened;
		}
		
		public function set opened(val:Boolean):void{
			if(_opened != val){
				_opened = val;
				updateOpenDisplay();
				if(_opened){
					if(holder){
						holder.current = this;
					}
					dispatchEvent(new Event(Event.OPEN));
				}else{
					dispatchEvent(new Event(Event.CLOSE));
				}
			}
		}
		
		public function get holder():UInterfaceHolder{
			return _holder;
		}
		
		public function set holder(val:UInterfaceHolder):void{
			if(_holder != val){
				_holder = val;
				if(_holder){
					if(opened){
						holder.current = this;
						holder.addChild(this);
					}else if(parent != null){
						parent.removeChild(this);
					}
				}
			}
		}
		
		public function get btClose():InteractiveObject{
			return _btClose;
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
		public function open(){
			opened = true;
		}
		public function close(){
			opened = false;
		}
		
		
		//////////// Private functions ////////////
		protected function updateOpenDisplay(){
			visible = _opened;
			if(_opened){
				if(holder){
					holder.addChild(this);
				}
			}else{
				if(holder){
					holder.removeChild(this);
				}
			}
		}
		
		
		//////////// Event Handlers functions ////////////
		protected function closeClickHandler(e:Event){
			opened = false;
		}
		protected function RemovedFromStageHandler(e:Event){
			if(!holder && parent == null){
				opened = false;
			}
		}
		
	}
}