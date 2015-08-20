package  {
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import br.com.stimuli.loading.BulkLoader;
	import br.com.stimuli.loading.loadingtypes.LoadingItem;
	
	[Event(name="success", type="ConnectionRequest")]
	[Event(name="failled", type="ConnectionRequest")]
	[Event(name="error", type="ConnectionRequest")]
	[Event(name="connectionError", type="ConnectionRequest")]
	[Event(name="returned", type="ConnectionRequest")]
	[Event(name="processed", type="ConnectionRequest")]
	public class ConnectionRequest extends EventDispatcher{
		//////////// variables ////////////
		protected var _loadingItem:LoadingItem;
		protected var _response:Object;
		protected var _loaded:Boolean;
		
		//////////// Static variables ////////////
		public static const SUCCESS:String = "req success";
		public static const FAILLED:String = "req failled";
		public static const ERROR:String = "req error";
		public static const CONNECTION_ERROR:String = "req connection error";
		public static const RETURNED:String = "req returned";
		public static const PROCESSED:String = "req processed";
		
		//////////// Constructor ////////////
        public function ConnectionRequest() {
			
        }
		
		//////////// Properties functions ////////////
		public function get loadingItem():LoadingItem{
			return _loadingItem;
		}
		public function set loadingItem(val:LoadingItem):void{
			if(_loadingItem != val){
				if(_loadingItem != null){
					_loadingItem.removeEventListener(BulkLoader.COMPLETE,loadedHandler);
				}
				_loadingItem = val;
				if(_loadingItem != null){
					_loadingItem.addEventListener(BulkLoader.COMPLETE,loadedHandler);
					_loadingItem.addEventListener(BulkLoader.ERROR,errorHandler);
				}
			}
		}
		
		public function get response():Object{
			return _response
		}
		public function get loaded():Boolean{
			return _loaded;
		}
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		
		//////////// Private functions ////////////
		
		//////////// Event Handlers functions ////////////
		protected function loadedHandler(e:Event){
			_loaded = true;
			var res:Object = loadingItem.content;
			if(res){
				_response = res;
				dispatchEvent(new Event(RETURNED));
			}else{
				dispatchEvent(new Event(ERROR));
			}
			dispatchEvent(new Event(PROCESSED));
		}
		protected function errorHandler(e:Event){
			_loaded = false;
			dispatchEvent(new Event(CONNECTION_ERROR));
			dispatchEvent(new Event(PROCESSED));
		}
		

	}
	
}
