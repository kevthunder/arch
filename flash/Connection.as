package  {
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.net.URLRequest;
	import flash.net.URLRequestMethod;
	import flash.net.URLVariables;
	import flash.utils.getTimer;
	import flash.utils.Timer;
	import br.com.stimuli.loading.BulkLoader;
	import br.com.stimuli.loading.loadingtypes.LoadingItem;
	import com.flashkev.docroot.KRoot;
	import flash.events.TimerEvent;
	import flash.geom.Rectangle;
	
	public class Connection extends EventDispatcher{
		//////////// variables ////////////
		protected var _loader : BulkLoader;
		protected var _urlHome:String = 'http://localhost/architecturers/';
		protected var _xmlRequests:Array = new Array();
		protected var _staticXmlRequests:Array = new Array();
		protected var _priorityMulti:int = 500;
		protected var _priorityBase:int = 1000;
		protected var _lastReqTime:int;
		protected var _waiting:Boolean;
		protected var _timer:Timer;
		protected var _loadingItem:LoadingItem;
		protected var _serverStartTime:Number = NaN;
		protected var _firstServerStartTime:Number = NaN;
		public var buffer:int = 10000;
		
		//////////// Static variables ////////////
		
		//////////// Constructor ////////////
		public function Connection() {
			_timer = new Timer(Math.min(_priorityMulti,_priorityBase));
			loader = new BulkLoader(BulkLoader.getUniqueName());
			loader.logFunction = KRoot.log;
			loader.logLevel = BulkLoader.LOG_INFO;
			_timer.addEventListener(TimerEvent.TIMER,timerHandler);
			
			var req:ConnectionXMLRequest = new ConnectionXMLRequest('commun','reset_buffer');
			req.priority = 100000;
			addXmlRequest(req);
			
		}
		
		//////////// Properties functions ////////////
		public function get urlHome():String{
			return _urlHome;
		}
		
		public function get loader():BulkLoader{
			return _loader;
		}
		public function set loader(val:BulkLoader):void{
			_loader = val;
			_loader.start();
		}
		
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		public function addXmlRequest(req:ConnectionXMLRequest):Boolean{
			if(req!=null){
				var pos:int = 0;
				while(pos < _xmlRequests.length && (_xmlRequests[pos] as ConnectionXMLRequest).priority < req.priority){
					pos++;
				}
				if(pos == _xmlRequests.length){
					_xmlRequests.push(req);
				}else{
					_xmlRequests.splice(pos,0,req);
				}
				req.addEventListener(ConnectionRequest.PROCESSED,xmlRequestProcessedHandler);
				updateTimer();
				
				return true
			}
			return false
		}
		public function addStaticXmlRequest(req:ConnectionXMLRequest):Boolean{
			if(req!=null){
				var pos:int = 0;
				while(pos < _staticXmlRequests.length && (_staticXmlRequests[pos] as ConnectionXMLRequest).priority < req.priority){
					pos++;
				}
				if(pos == _staticXmlRequests.length){
					_staticXmlRequests.push(req);
				}else{
					_staticXmlRequests.splice(pos,0,req);
				}
				updateTimer();
				
				return true
			}
			return false
		}
		public function removeXmlRequest(req:ConnectionXMLRequest):Boolean{
			if(req!=null){
				var i:int;
				for(i = 0;i<_xmlRequests.length;i++){
					if(_xmlRequests[i] == req){
						req.removeEventListener(ConnectionRequest.PROCESSED,xmlRequestProcessedHandler);
						_xmlRequests.splice(i,1);
						return true;
					}
				}
				for(i = 0;i<_staticXmlRequests.length;i++){
					if(_staticXmlRequests[i] == req){
						_staticXmlRequests.splice(i,1);
						return true;
					}
				}
			}
			return false;
		}
		
		
		public function login(username:String,password:String):ConnectionRequest{
			var req:ConnectionRequest = new ConnectionRequest();
			
			loader.remove("login");
			var urlReq:URLRequest = new URLRequest(urlHome+"users/client_login");
			urlReq.method = URLRequestMethod.POST;
			var vars = new URLVariables();
			vars.username = username;
			vars.password = password;
			urlReq.data = vars;
			req.loadingItem = loader.add(urlReq, {id:"login", type:"xml", preventCache:true})
			req.addEventListener(ConnectionRequest.RETURNED,loginReturnHandler);
			return req;
		}
		
		public function saveXml(xml:XML):ConnectionXMLRequest{
			var req:ConnectionXMLRequest = new ConnectionXMLRequest('commun','save');
			req.content = xml;
			req.priority = 1;
			addXmlRequest(req);
			return req;
		}
		
		public function getCharacters():LoadingItem{
			loader.remove("Characters_xml");
			return loader.add(urlHome+"characters/my_characters", {id:"Characters_xml", type:"xml", preventCache:true});
		}
		
		public function selecteCharacter(id:int):ConnectionXMLRequest{
			var req:ConnectionXMLRequest = new ConnectionXMLRequest('characters','select');
			req.params = {
				character_id : id
			}
			req.priority = 1;
			addXmlRequest(req);
			return req;
		}
		public function syncTimer():int{
			return realTimerToSyncTimer(getTimer());
		}
		public function serverTimeToTimer(time:Number):int{
			return realTimerToSyncTimer((time-_serverStartTime)*1000);
		}
		public function timerToServerTime(time:int):Number{
			return SyncTimerToRealTimer(time)/1000+_serverStartTime;
		}
		
		//////////// Private functions ////////////
		protected function realTimerToSyncTimer(time:int){
			return (_serverStartTime - _firstServerStartTime)*1000+time;
		}
		protected function SyncTimerToRealTimer(time:int){
			return time-(_serverStartTime - _firstServerStartTime)*1000;
		}
		
		
		protected function tcheckRequest(){
			var time:int = getTimer() - _lastReqTime;
			if(!_waiting && time >= getRequestsMinInterval()){
				sendRequests();
			}
		}
		
		protected function sendRequests(){
			_lastReqTime = getTimer();
			var xml:XML = new XML('<architecturers/>');
			var req:ConnectionXMLRequest;
			var allRequests = _xmlRequests.concat(_staticXmlRequests);
			allRequests.sortOn(["priority", "no"], [Array.NUMERIC , Array.NUMERIC]);
			var i:int;
			for(i = 0;i<allRequests.length;i++){
				req = allRequests[i] as ConnectionXMLRequest;
				xml.appendChild(req.getRequestXml());
			}
			ClientMain.instance.log(xml.toXMLString());
			var ureq:URLRequest = new URLRequest(urlHome+"inputs/parse");
			ureq.method = URLRequestMethod.POST;
			var vars = new URLVariables();
			vars.xml = xml.toXMLString();
			ureq.data = vars;
			
			loader.remove("xml_link");
			_loadingItem = loader.add(ureq, {id:"xml_link"/*, type:"xml"*/, preventCache:true});
			for(i = 0;i<allRequests.length;i++){
				req = allRequests[i] as ConnectionXMLRequest;
				req.loadingItem = _loadingItem;
			}
			_loadingItem.addEventListener(BulkLoader.COMPLETE,loadedHandler);
			_loadingItem.addEventListener(BulkLoader.ERROR,errorHandler);
			setWaiting(true);
		}
		
		protected function setWaiting(val:Boolean){
			if(_waiting != val){
				_waiting = val
				updateTimer();
			}
		}
		
		protected function updateTimer(){
			var shouldRun:Boolean = (!_waiting && (_xmlRequests.length || _staticXmlRequests.length));
			if(_timer.running != shouldRun){
				if(shouldRun){
					_timer.start();
				}else{
					_timer.stop();
					_timer.reset();
				}
			}
		}
		
		protected function getRequestsMinInterval():int{
			var minPriority:int = int.MAX_VALUE;
			if(_xmlRequests.length){
				minPriority = Math.min((_xmlRequests[0] as ConnectionXMLRequest).priority,minPriority);
			}
			if(_staticXmlRequests.length){
				minPriority = Math.min((_staticXmlRequests[0] as ConnectionXMLRequest).priority,minPriority);
			}
			if(minPriority == int.MAX_VALUE){
				return int.MAX_VALUE;
			}else{
				if(minPriority <= 1){
					return 0;
				}else{
					return (minPriority-2)*_priorityMulti+_priorityBase;
				}
			}
		}
		
		//////////// Event Handlers functions ////////////
		protected function loadedHandler(e:Event){var res:XML;
			if(_loadingItem.content is XML){
				res = _loadingItem.content;
			}else{
				try{
					res = new XML(_loadingItem.content);
				}catch(e){
					
				}
			}
			if(res){
				if(res.hasOwnProperty("@time")){
					var servertime = Number(res.@time);
					var ssTime = servertime - getTimer()/1000;
					//trace("time :",servertime);
					//trace("time diff :",(ssTime -_serverStartTime));
					//trace("realtimer :",servertime-_serverStartTime);
					//trace("time to timer :",serverTimeToTimer(servertime));
					//trace("timer :",syncTimer());
					ClientMain.instance.log('Responce Received : ');
					ClientMain.instance.log(res);
					if(isNaN(_serverStartTime)){
						_firstServerStartTime = ssTime;
						_serverStartTime = ssTime;
					}else if(_serverStartTime < ssTime ){
						_serverStartTime = ssTime;
					}
				}
			}
			setWaiting(false);
		}
		protected function errorHandler(e:Event){
			setWaiting(false);
		}
		protected function loginReturnHandler(e:Event){
			var req:ConnectionRequest = e.target as ConnectionRequest;
			var responce:XML = req.loadingItem.content as XML;
			//KRoot.log(responce.toString());
			if(responce.warning.@code.toString() == '201'){
				req.dispatchEvent(new Event(ConnectionRequest.SUCCESS));
			}else{
				req.dispatchEvent(new Event(ConnectionRequest.ERROR));
			}
		}
		protected function timerHandler(e:Event){
			tcheckRequest();
		}
		protected function xmlRequestProcessedHandler(e:Event){
			removeXmlRequest(e.target as ConnectionXMLRequest);
		}
		

	}
	
}
