package {
	import flash.events.Event;
	import flash.display.DisplayObject;
	import flash.display.InteractiveObject;
	import flash.text.TextField;
	import flash.events.MouseEvent;
	import flash.events.KeyboardEvent;
	import flash.ui.Keyboard;
	import flash.geom.Point;
	import flash.text.TextFormat;

	[Event(name="open", type="Event")]
	[Event(name="close", type="Event")]
	public class UIChat extends UInterface {
		//////////// variables ////////////
		protected var _bg:DisplayObject;
		protected var _active:Boolean;
		protected var _btSend:InteractiveObject;
		protected var _btOpen:InteractiveObject;
		protected var _txtInput:TextField;
		protected var _txtMessages:TextField;
		protected var _updateRequest:ConnectionXMLRequest = new ConnectionXMLRequest('messages','keep_updated');
		protected var _centerObject:TilePositionnedObject;
		protected var _messages:Array;
		protected var _range:int = 10;
		protected var _maxTime:int = 10000;
		protected var _strengthRange:int = 5;
		protected var _minSize:int = 8;
		protected var _maxSize:int = 12;
		protected var _minBrightness:int = 100;
		protected var _maxBrightness:int = 255;
		
		
		//////////// Static variables ////////////
		
		
		//////////// Constructor ////////////
        public function UIChat() {
			_messages = new Array();
			_updateRequest.params = {
				'range' : _range
			}
			_updateRequest.reloadable = true;
			_updateRequest.addEventListener(ConnectionRequest.SUCCESS,updateResponceHandler);
			_updateRequest.addEventListener(ConnectionRequest.FAILLED,updateFailedHandler);
			_updateRequest.addEventListener(ConnectionRequest.ERROR,updateFailedHandler);
        }
		
		//////////// Properties functions ////////////
		
		public function get active():Boolean{
			return _active;
		}
		public function set active(val:Boolean):void{
			if(_active != val){
				_active = val
				if(_active){
					centerObject = ClientMain.instance.selectedCharacter;
					updateReqTime();
					ClientMain.instance.connection.addStaticXmlRequest(_updateRequest);
					ClientMain.instance.addEventListener(ClientMain.CHANGE_CHARACTER,characterChangerHandler);
				}else{
					ClientMain.instance.connection.removeXmlRequest(_updateRequest);
					ClientMain.instance.removeEventListener(ClientMain.CHANGE_CHARACTER,characterChangerHandler);
				}
			}
		}
		
		public function get bg():DisplayObject{
			return _bg;
		}
		public function set bg(val:DisplayObject):void{
			if(_bg != val){
				_bg = val
				if(_bg){
					bg.visible = opened;
				}
			}
		}
		public function get btSend():InteractiveObject{
			return _btSend;
		}
		public function set btSend(val:InteractiveObject):void{
			if(_btSend != val){
				_btSend = val
				if(_btSend){
					btSend.visible = opened;
					btSend.addEventListener(MouseEvent.CLICK,sendClickHandler)
				}
			}
		}
		public function get btOpen():InteractiveObject{
			return _btOpen;
		}
		public function set btOpen(val:InteractiveObject):void{
			if(_btOpen != val){
				_btOpen = val
				if(_btOpen){
					btOpen.visible = !opened;
					btOpen.addEventListener(MouseEvent.CLICK,openClickHandler);
				}
			}
		}
		public function get txtInput():TextField{
			return _txtInput;
		}
		public function set txtInput(val:TextField):void{
			if(_txtInput != val){
				_txtInput = val
				if(_txtInput){
					txtInput.visible = opened;
				}
			}
		}
		public function get txtMessages():TextField{
			return _txtMessages;
		}
		public function set txtMessages(val:TextField):void{
			if(_txtMessages != val){
				_txtMessages = val
				if(_txtMessages){
					txtMessages.mouseEnabled = opened;
					updateMessagesArea();
				}
			}
		}
		
		
		public function get centerObject():TilePositionnedObject{
			return _centerObject;
		}
		public function set centerObject(val:TilePositionnedObject):void{
			if(_centerObject != val){
				if(_centerObject){
					_centerObject.removeEventListener(TilePositionnedObject.MOVED,targetMovedHandler);
				}
				_centerObject = val;
				if(_centerObject){
					targetMovedHandler(null);
					_centerObject.addEventListener(TilePositionnedObject.MOVED,targetMovedHandler);
				}
			}
		}
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		public function send():ConnectionXMLRequest{
			var req:ConnectionXMLRequest = sendMsg(txtInput.text);
			txtInput.text = '';
			return req;
		}
		
		public function sendMsg(text:String):ConnectionXMLRequest{
			if(text.length > 1){
				trace(text);
				
				var req:ConnectionXMLRequest = new ConnectionXMLRequest('messages','post');
				req.params = {
					'text' : text
				}
				ClientMain.instance.connection.addXmlRequest(req);
				
				return req;
			}
			return null;
		}
		
		
		//////////// Private functions ////////////
		override protected function updateOpenDisplay(){
			bg.visible = opened;
			btSend.visible = opened;
			btOpen.visible = !opened;
			txtInput.visible = opened;
			this.mouseEnabled = opened;
			updateMessagesArea();
			if(opened){
				if(stage){
					this.hitArea = null;
					stage.addEventListener(KeyboardEvent.KEY_UP,keyDownHandler);
				}
			}else{
				if(stage){
					stage.removeEventListener(KeyboardEvent.KEY_UP,keyDownHandler);
				}
			}
		}
		
		protected function updateMessagesArea(){
			if(opened){
				txtMessages.y = 25;
				txtMessages.x = 0;
				txtMessages.height = 300;
			}else{
				txtMessages.y = 0;
				txtMessages.x = 30;
				txtMessages.height = 100;
			};
			updateScroll();
		}
		
		protected function updateReqTime(){
			var time:Number = 1;
			if(_messages.length){
				time = (_messages[_messages.length-1] as Message).time;
			}
			var exclude:Array = new Array();
			for(var i:int =_messages.length-1; i>=0 && (_messages[i] as Message).time >= time; i--){
				exclude.push((_messages[i] as Message).id);
			}
			time = Math.max(ClientMain.instance.getTimer()-_maxTime,time);
			var serverTime:Number = ClientMain.instance.connection.timerToServerTime(time);
			//serverTime = 1;
			if(!isNaN(serverTime)){
				_updateRequest.params.since = serverTime;
				_updateRequest.params.exclude = exclude;
			}
		}
		protected function updateScroll(){
			txtMessages.scrollV = txtMessages.maxScrollV;
		}
		
		//////////// Event Handlers functions ////////////
		protected function openClickHandler(e:Event){
			opened = true;
		}
		protected function sendClickHandler(e:Event){
			send();
		}
		protected function keyDownHandler(e:KeyboardEvent){
			if(e.keyCode == Keyboard.ENTER){
				send();
			}
		}
		protected function characterChangerHandler(e:Event){
			centerObject = ClientMain.instance.selectedCharacter;
		}
		protected function targetMovedHandler(e:Event){
			_updateRequest.params.x = centerObject.posX;
			_updateRequest.params.y = centerObject.posY;
		}
		protected function updateResponceHandler(e:Event){
			for(var i:int =0; i<_updateRequest.items.length; i++){
				var msg:Message = _updateRequest.items[i] as Message;
				if(msg){
					if(txtMessages.length){
						txtMessages.appendText("\n");
					}
					var dist:Number = Point.distance(new Point(msg.x,msg.y),new Point(Math.floor(centerObject.posX),Math.floor(centerObject.posY)));
					var strength:Number = Math.max(0,_strengthRange-Math.abs(dist-1))/_strengthRange;
					trace(strength);
					var size:int = (_maxSize-_minSize)*strength+_minSize;
					var brightness:int = (_maxBrightness-_minBrightness)*strength+_minBrightness;
					var tf:TextFormat = new TextFormat(null,size,brightness*65793);//65793 = 256*256+256+1
					txtMessages.defaultTextFormat = tf;
					txtMessages.appendText(msg.text);
					_messages.push(msg);
				}
			}
			updateScroll();
			updateReqTime();
		}
		protected function updateFailedHandler(e:Event){
			updateReqTime();
		}
	}
}