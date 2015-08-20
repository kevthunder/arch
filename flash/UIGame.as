package {
	import flash.events.Event;
	import com.flashkev.customList.CustomList;
	import flash.events.MouseEvent;
	import com.flashkev.docroot.KRoot;
	import flash.display.InteractiveObject;

	public class UIGame extends UInterface {
		//////////// variables ////////////
		protected var _fastAbilities:CustomList;
		protected var _tilesDisplay:TilesDisplay;
		protected var _lastCharacter:Character;
		protected var _inventory:Inventory;
		protected var _btInventory:InteractiveObject;
		public var currentAbility:Ability;
		public var chat:UIChat;
		
		//////////// Static variables ////////////
		
		
		//////////// Constructor ////////////
        public function UIGame() {
			game.addEventListener(ClientMain.CHANGE_CHARACTER,charChangeHandler);
			addEventListener(Event.OPEN,openHandler);
        }
		
		//////////// Properties functions ////////////
		public function get game():ClientMain{
			return ClientMain.instance;
		}
		
		public function get fastAbilities():CustomList{
			return _fastAbilities;
		}
		public function set fastAbilities(val:CustomList):void{
			if(_fastAbilities != val){
				_fastAbilities = val
				if(_fastAbilities){
					fastAbilities.setRendererStyle('displayField','name');
					fastAbilities.addEventListener(Event.CHANGE,fastAbilitiesChangeHandler);
				}
			}
		}
		public function get tilesDisplay():TilesDisplay{
			return _tilesDisplay;
		}
		public function set tilesDisplay(val:TilesDisplay):void{
			if(_tilesDisplay != val){
				_tilesDisplay = val
				if(_tilesDisplay){
					_tilesDisplay.addEventListener(MouseEvent.CLICK,clickHandler);
				}
			}
		}
		public function get inventory():Inventory{
			return _inventory;
		}
		public function set inventory(val:Inventory):void{
			if(_inventory != val){
				_inventory = val;
			}
		}
		public function get btInventory():InteractiveObject{
			return _btInventory;
		}
		public function set btInventory(val:InteractiveObject):void{
			if(_btInventory != val){
				_btInventory = val;
				if(_btInventory){
					_btInventory.addEventListener(MouseEvent.CLICK,btInventoryClickHandler);
				}
			}
		}
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		public function updateAbilities(){
			if(_lastCharacter != game.selectedCharacter){
				fastAbilities.removeAll();
				_lastCharacter = game.selectedCharacter;
				if(game.selectedCharacter){
					var req:ConnectionXMLRequest = game.selectedCharacter.getAbilities();
					req.addEventListener(ConnectionRequest.SUCCESS, charAbilityLoadedHandler);
				}
			}
		}
		
		
		//////////// Private functions ////////////
		
		
		//////////// Event Handlers functions ////////////
		protected function clickHandler(e:Event){
			if(currentAbility){
				currentAbility.useNow(game.selectedCharacter);
			}
		}
		protected function openHandler(e:Event){
			updateAbilities();
			chat.active = true;
		}
		protected function charChangeHandler(e:Event){
			if(opened){
				updateAbilities();
			}
			if(game.selectedCharacter){
				tilesDisplay.camera.follow(game.selectedCharacter);
			}
		}
		protected function charAbilityLoadedHandler(e:Event){
			var req:ConnectionXMLRequest = e.target as ConnectionXMLRequest;
			
			fastAbilities.removeAll();
			for(var i:int = 0; i< req.items.length; i++){
				fastAbilities.addItem(req.items[i]);
			}
		}
		protected function btInventoryClickHandler(e:Event){
			inventory.opened = !inventory.opened;
		}
		protected function fastAbilitiesChangeHandler(e:Event){
			var ability:Ability = fastAbilities.selectedItem as Ability;
			if(ability){
				KRoot.log(ability);
				currentAbility = ability;
			}
		}
		
	}
}