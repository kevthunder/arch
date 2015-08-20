package {
	import flash.display.MovieClip;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Point;
	import flash.utils.getTimer;
	import br.com.stimuli.loading.BulkLoader;
	import br.com.stimuli.loading.loadingtypes.LoadingItem;
	import com.flashkev.docroot.SimpleRootLoader;
	import com.flashkev.customList.CustomList;
	import com.flashkev.viewer.Viewer;
	import com.flashkev.utils.NumberUtil;
	import de.polygonal.math.PM_PRNG;
	import com.sociodox.theminer.TheMiner;  
	
	[Event(name="changeCharacter", type="ClientMain")]
	public class ClientMain extends SimpleRootLoader {
		//////////// variables ////////////
		public static var instance:ClientMain;
		public var connection:Connection = new Connection();
		public var savedItems:SavedItemCollection = new SavedItemCollection();
		public var mainHolder:UInterfaceHolder = new UInterfaceHolder();
		public var ingame:UIGame;
		public var characterSelect:UICharacterSelect;
		public var loginWindow:LoginWindow;
		protected var _selectedCharacter:Character;
		
		//////////// Static variables ////////////
		public static const CHANGE_CHARACTER:String = "change character";
		
		//////////// Constructor ////////////
		public function ClientMain(){
			/*for(var i:int=0; i<16; i++){
				trace(Math.floor(Math.random()*PM_PRNG.MAX));
			}*/
			instance = this;
			playAtLoaded = false;
			addEventListener(DOC_LOADED,docLoaded);
			Viewer.defaultBulkLoader = connection.loader;
		}
		
		//////////// Properties functions ////////////
		public function get selectedCharacter():Character{
			return _selectedCharacter;
		}

		public function set selectedCharacter(val:Character):void{
			if(_selectedCharacter != val){
				_selectedCharacter = val;
				ingame.open();
				dispatchEvent(new Event(CHANGE_CHARACTER));
			}
		}
		
		//////////// Public functions ////////////
		public function test():String{
			return "test";
		}
		public function getTimer():int{
			//return flash.utils.getTimer();
			return connection.syncTimer();
		}
		public function loginNow(userName:String,Password:String):ConnectionRequest{
			var req:ConnectionRequest = connection.login(userName,Password);
			req.addEventListener(ConnectionRequest.SUCCESS, loggedHandler);
			return req;
		}
		public static function cached():Array{
			return [Tree1,Tree2,ChargeEffect];
		}
		
		//////////// Private functions ////////////
		
		//////////// Event Handlers functions ////////////
		protected function docLoaded(e:Event){
			this.addChild(new TheMiner());  
			
			ingame = new UIGame();
			characterSelect = new UICharacterSelect();
			addChild(mainHolder);
			ingame.holder = mainHolder;
			characterSelect.holder = mainHolder;
		}
		protected function loggedHandler(e:Event){
			loginWindow.opened = false;
			characterSelect.open();
		}
		
	}
	
	
}