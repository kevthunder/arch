package {
	import flash.events.Event;
	import com.flashkev.customList.CustomList;
	import br.com.stimuli.loading.BulkLoader;
	import br.com.stimuli.loading.loadingtypes.LoadingItem;

	public class UICharacterSelect extends UInterface {
		//////////// variables ////////////
		protected var _characterList:CustomList;
		
		//////////// Static variables ////////////
		
		
		//////////// Constructor ////////////
        public function UICharacterSelect() {
			addEventListener(Event.OPEN,openHandler);
        }
		
		//////////// Properties functions ////////////
		public function get game():ClientMain{
			return ClientMain.instance;
		}
		
		public function get characterList():CustomList{
			return _characterList;
		}
		public function set characterList(val:CustomList):void{
			if(_characterList != val){
				_characterList = val
				if(_characterList){
					_characterList.setRendererStyle('displayField','name');
					_characterList.addEventListener(Event.CHANGE,characterListChangeHandler);
				}
			}
		}
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		
		
		//////////// Private functions ////////////
		
		
		//////////// Event Handlers functions ////////////
		protected function openHandler(e:Event){
			characterList.removeAll();
			game.connection.getCharacters().addEventListener(BulkLoader.COMPLETE, charactersLoaded);
		}
		
		protected function charactersLoaded(e:Event){
			var loadedItem:LoadingItem = e.target as LoadingItem;
			
			//trace(loadedItem.content);
			var chars:Array = game.savedItems.loadXml(loadedItem.content as XML);
			//trace(chars)
			for(var i:int = 0; i<chars.length; i++){
				characterList.addItem(chars[i]);
			}
			characterList.visible = true;
		}
		protected function characterListChangeHandler(e:Event){
			var char:Character = characterList.selectedItem as Character;
			if(char){
				char.select();
			}
		}
		
	}
}