package  {
	import flash.events.Event;
	import flash.events.EventDispatcher;
	
	[Event(name="success", type="ConnectionRequest")]
	[Event(name="failled", type="ConnectionRequest")]
	[Event(name="error", type="ConnectionRequest")]
	[Event(name="connectionError", type="ConnectionRequest")]
	[Event(name="xmlReturned", type="ConnectionXMLRequest")]
	[Event(name="validate", type="ConnectionXMLRequest")]
	[Event(name="loadItems", type="ConnectionXMLRequest")]
	public class ConnectionXMLRequest extends ConnectionRequest{
		//////////// variables ////////////
		protected var _no:int;
		protected var _handler:String;
		protected var _action:String;
		protected var _reloadable:Boolean;
		protected var _priority:uint = 5;
		protected var _params:Object = new Object();
		protected var _content:XML;
		protected var _xmlResponse:XML;
		protected var _messages:XMLList;
		protected var _items:Array = new Array();
		protected static var _autoNo = 1;
		
		//////////// Static variables ////////////
		public static const XML_RETURNED:String = "req xml returned";
		public static const VALIDATE:String = "req validate";
		public static const LOAD_ITEMS:String = "load req items";
		
		//////////// Constructor ////////////
        public function ConnectionXMLRequest(handler:String = null, action:String = null) {
			_no = _autoNo;
			_autoNo++;
			_handler = handler;
			_action = action;
			addEventListener(XML_RETURNED,xmlReponseReturnedHandler);
			addEventListener(RETURNED,returnedHandler);
        }
		
		//////////// Properties functions ////////////
		public function get no():int{
			return _no;
		}
		public function set no(val:int){
			if(!locked && _no != val){
				_no = val;
			}
		}
		
		public function get handler():String{
			return _handler;
		}
		public function set handler(val:String){
			if(!locked && _handler != val){
				_handler = val;
			}
		}
		
		public function get action():String{
			return _action;
		}
		public function set action(val:String){
			if(!locked && _action != val){
				_action = val;
			}
		}
		
		public function get params():Object{
			if(!locked){
				return _params;
			}else{
				var copy:Object = new Object();
				for(var prop:String in _params){
					copy[prop] = _params[prop];
				}
				return copy;
			}
		}
		public function set params(val:Object){
			if(!locked && _params != val){
				_params = val;
			}
		}
		
		public function get content():XML{
			if(!locked){
				return _content;
			}else{
				return _content.copy();
			}
		}
		public function set content(val:XML){
			if(!locked && _content != val){
				_content = val;
			}
		}
		
		public function get reloadable():Boolean{
			return _reloadable;
		}
		public function set reloadable(val:Boolean){
			if(!locked && _reloadable != val){
				_reloadable = val;
			}
		}
		
		public function get priority():uint{
			return _priority;
		}
		public function set priority(val:uint){
			if(!locked && _priority != val){
				_priority = val;
			}
		}
		public function get locked():Boolean{
			return !reloadable && loaded;
		}
		
		
		public function get xmlResponse():XML{
			if(_xmlResponse){
				return _xmlResponse.copy();
			}
			return null;
		}
		
		public function get items():Array{
			return _items.concat();
		}
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		public function getRequestXml(){
			var newXml:XML = new XML("<request />");
			newXml["@no"] = no;
			newXml["@handler"] = handler;
			newXml["@action"] = action;
			for(var prop:String in _params){
				newXml["@"+prop] = _params[prop];
			}
			if(_content){
				newXml.appendChild(_content);
			}
			return newXml;
		}
		public function hasMessage(no:int){
			for(var i:int = 0;i<_messages.length();i++){
			   if(parseInt(_messages[i].@code.toString())==no){
				   return true;
			   }
			}
			return false;
		}
		
		//////////// Private functions ////////////
		protected function validXmlResponse(xml:XML){
			_xmlResponse = xml;
			if(_xmlResponse.hasOwnProperty('warning')){
			   _messages = _xmlResponse.warning;
			   for(var i:int = 0;i<_messages.length();i++){
				   if(parseInt(_messages[i].@code.toString())>=400){
					   return false;
				   }
			   }
			}
			if(_xmlResponse.hasOwnProperty('items')){
				var canLoad:Boolean = dispatchEvent(new Event(LOAD_ITEMS,false,true))
				if(canLoad){
					_items = ClientMain.instance.savedItems.loadXml(_xmlResponse.items[0]);
				}
			}
			return dispatchEvent(new Event(VALIDATE,false,true));
		}
		
		//////////// Event Handlers functions ////////////
		protected function returnedHandler(e:Event){
			var res:XML;
			if(loadingItem.content is XML){
				res = loadingItem.content;
			}else{
				try{
					res = new XML(loadingItem.content);
				}catch(e){
					
				}
			}
			if(res){
				dispatchEvent(new ContentEvent(XML_RETURNED,res));
			}else{
				dispatchEvent(new Event(CONNECTION_ERROR));
			}
		}

		protected function xmlReponseReturnedHandler(e:ContentEvent){
			var res:XML = e.content as XML;
			var xml:XML = null;
			for(var i:int = 0;i<res.response.length();i++){
				if(parseInt(res.response[i].@no.toString()) == no){
					xml = res.response[i];
					break;
				}
			}
			if(xml){
				if(validXmlResponse(xml)){
					dispatchEvent(new Event(ConnectionRequest.SUCCESS));
				}else{
					dispatchEvent(new Event(ConnectionRequest.ERROR));
				}
			}else{
				dispatchEvent(new Event(ConnectionRequest.ERROR));
			}
				
		}

	}
	
}
