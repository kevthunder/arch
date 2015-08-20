package  {
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.geom.Rectangle;
	
	[Event(name="success", type="ConnectionRequest")]
	[Event(name="failled", type="ConnectionRequest")]
	[Event(name="error", type="ConnectionRequest")]
	[Event(name="connectionError", type="ConnectionRequest")]
	[Event(name="xmlReturned", type="ConnectionXMLRequest")]
	public class KeepTilesUpdated extends ConnectionXMLRequest{
		//////////// variables ////////////
		
		//////////// Static variables ////////////
		
		//////////// Constructor ////////////
        public function KeepTilesUpdated() {
			super('tiles','keep_updated');
			addEventListener(ConnectionRequest.SUCCESS,successHandler);
        }
		
		//////////// Properties functions ////////////
		public function get rect():Rectangle{
			if(params.hasOwnProperty('x') && params.hasOwnProperty('y') && params.hasOwnProperty('width') && params.hasOwnProperty('height')){
				return new Rectangle(params['x'],params['y'],params['width'],params['height']);
			}
			return null;
		}
		public function set rect(val:Rectangle){
			params['x'] = val.x;
			params['y'] = val.y;
			params['width'] = val.width;
			params['height'] = val.height;
		}
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		
		//////////// Private functions ////////////
		
		//////////// Event Handlers functions ////////////
		public function successHandler(e:Event){
			if(xmlResponse.hasOwnProperty('@date')){
				params.lastDate = xmlResponse.@date;
			}
		}

	}
	
}
