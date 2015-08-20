package  {
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.geom.Rectangle;
	
	public class Camera extends PrecisePos{
		//////////// variables ////////////
		protected var _followTarget:Character;
		
		//////////// Static variables ////////////
		
		//////////// Constructor ////////////
        public function Camera() {
			
        }
		
		//////////// Properties functions ////////////
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		public function follow(target:Character){
			if(_followTarget != null){
				_followTarget.removeEventListener(TilePositionnedObject.MOVED,targetMovedHandler);
			}
			_followTarget = target;
			_followTarget.addEventListener(TilePositionnedObject.MOVED,targetMovedHandler);
			targetMovedHandler();
		}
		
		//////////// Private functions ////////////
		
		//////////// Event Handlers functions ////////////
		protected function targetMovedHandler(e:Event = null){
			this.setTo(_followTarget.precisePos);
		}

	}
	
}
