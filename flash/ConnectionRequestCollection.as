package  {
	import flash.events.Event;
	import flash.events.EventDispatcher;
	
	public class ConnectionRequestCollection extends ConnectionRequest{
		//////////// variables ////////////
		protected var _xmlResponse:XML;
		protected var _response:String;
		
		//////////// Static variables ////////////
		
		//////////// Constructor ////////////
        public function ConnectionXMLRequest() {
			addEventListener(RETURNED,loadedHandler);
        }
		
		//////////// Properties functions ////////////
		public function get xmlResponse():XML{
			if(_xmlResponse){
				return _xmlResponse.copy();
			}
			return null;
		}
		
		override public function get response():Object{
			return _response;
		}
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		
		//////////// Private functions ////////////
		
		//////////// Event Handlers functions ////////////
		override protected function loadedHandler(e:Event){
			_response = loadingItem.content;
			var res:XML = loadingItem.content as XML;
			if(res){
				_xmlResponse = res;
				dispatchEvent(new Event(SUCCESS));
			}else{
				dispatchEvent(new Event(CONNECTION_ERROR));
			}
		}

	}
	
}
