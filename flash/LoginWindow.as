package  {
	import flash.display.MovieClip;
	import flash.text.TextField;
	import flash.text.TextFieldType;
	import flash.display.InteractiveObject;
	import flash.events.MouseEvent;
	import flash.events.KeyboardEvent;
	import flash.events.Event;
	import flash.text.TextField
	import flash.utils.setTimeout;
	//import fl.text.TLFTextField;
	
	public class LoginWindow extends MovieClip {
		//////////// variables ////////////
		protected var _txtUsername:TextField;
		protected var _txtPassword:TextField;
		protected var _loginButton:InteractiveObject;
		public var loadingClip:MovieClip;
		
		protected var _processing:Boolean = false;
		protected var _opened:Boolean = false;
		protected var messageToolTip:Tooltip;
		protected var txtMessage:TextField;
		
		//////////// Static variables ////////////
		
		//////////// Constructor ////////////
        public function LoginWindow() {
			opened = true;
			loadingClip.visible = false;
			txtMessage = new TextField();
			txtMessage.width = 300;
			txtMessage.height = 80;
			messageToolTip = new Tooltip(false);
			messageToolTip.orientation = TooltipBg.BOTTOM;
			messageToolTip.content = txtMessage;
			messageToolTip.y = -100;
			addChild(messageToolTip);
			addEventListener(Event.ADDED_TO_STAGE,addedToStageHandler);
        }
		
		//////////// Properties functions ////////////
		public function get loginButton():InteractiveObject{
			return _loginButton;
		}
		public function set loginButton(val:InteractiveObject):void{
			if(_loginButton != val){
				if(_loginButton != null){
					_loginButton.removeEventListener(MouseEvent.CLICK,loginClickHandler);
				}
				_loginButton = val;
				if(_loginButton != null){
					_loginButton.addEventListener(MouseEvent.CLICK,loginClickHandler);
					_loginButton.tabIndex = 4;
				}
			}
		}
		public function get txtUsername():TextField{
			return _txtUsername;
		}
		public function set txtUsername(val:TextField):void{
			if(_txtUsername != val){
				if(_txtUsername != null){
				}
				_txtUsername = val;
				if(_txtUsername != null){
					_txtUsername.tabIndex = 2;
				}
			}
		}
		public function get txtPassword():TextField{
			return _txtPassword;
		}
		public function set txtPassword(val:TextField):void{
			if(_txtPassword != val){
				if(_txtPassword != null){
				}
				_txtPassword = val;
				if(_txtPassword != null){
					_txtPassword.tabIndex = 3;
				}
			}
		}
		public function get processing():Boolean{
			return _processing;
		}
		public function set processing(val:Boolean):void{
			if(_processing != val){
				_processing = val;
				if(_processing){
					messageToolTip.opened = false;
					loadingClip.gotoAndPlay(1);
					loadingClip.visible = true;
					loginButton.visible = false;
					txtUsername.type = TextFieldType.DYNAMIC;
					txtPassword.type = TextFieldType.DYNAMIC;
				}else{
					loadingClip.stop();
					loadingClip.visible = false;
					loginButton.visible = true;
					txtUsername.type = TextFieldType.INPUT;
					txtPassword.type = TextFieldType.INPUT;
				}
			}
		}
		public function get opened():Boolean{
			return _opened;
		}
		public function set opened(val:Boolean):void{
			if(_opened != val){
				_opened = val;
				this.visible = _opened
				if(_opened){
					processing = false;
					if(this.stage){
						openDisplay();
					}
				}else{
					if(this.stage){
						stage.removeEventListener(KeyboardEvent.KEY_UP,keyUpHandler)
					}
				}
			}
		}
		
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		
		//////////// Private functions ////////////
		protected function openDisplay(){
			stage.focus = txtUsername;
			txtUsername.setSelection(0,txtUsername.text.length);
			stage.addEventListener(KeyboardEvent.KEY_UP,keyUpHandler)
		}
		
		//////////// Event Handlers functions ////////////
		protected function addedToStageHandler(e:Event){
			if(opened){
				openDisplay();
			}
		}
		protected function loginClickHandler(e:Event){
			//trace("click");
			var req:ConnectionRequest = ClientMain.instance.loginNow(txtUsername.text,txtPassword.text);
			req.addEventListener(ConnectionRequest.ERROR,loginErrorHandler);
			processing = true;
		}
		protected function loginErrorHandler(e:Event){
			processing = false;
			var req:ConnectionRequest = e.target as ConnectionRequest;
			var responce:XML = req.loadingItem.content as XML;
			txtMessage.text = responce.warning.toString();
			messageToolTip.opened = true;
		}
		
		protected function keyUpHandler(e:KeyboardEvent){
			if(e.keyCode == 13){
				loginClickHandler(null);
			}
		}

	}
	
}
