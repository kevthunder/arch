package  {
	import flash.events.EventDispatcher;
	import flash.events.Event;
	
	public class SavedItemCollection extends CollectionBase{
		//////////// variables ////////////
		public var xmlMap:Object = {
			'tile' : Tile,
			'character' : Character,
			'path' : Path,
			'skill' : Skill,
			'structure' : Structure,
			'item' : Item,
			'message' : Message,
			'timed_event' : SavedEvent,
			'skill_instance' : SkillInstance,
			'effect' : Effect,
			'skin' : SkinDefinition
		}
		protected var _models:Object = new Object();
		
		//////////// Static variables ////////////
		
		//////////// Constructor ////////////
        public function SavedItemCollection() {
        }
		
		//////////// Properties functions ////////////
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		public function loadXml(xml:XML):Array{
			var added:Array = new Array();
			for(var i:int=0; i<xml.children().length(); i++){
				var child:XML = xml.children()[i];
				if(xmlMap.hasOwnProperty(child.name())){
					var item:SavedItem = null;
					if(child.hasOwnProperty('@id')){
						item = getItem(child.name(),parseInt(child.@id.toString()));
					}
					if(!item){
						var dataType:Class = XmlEncodable.getXmlDataType(child,xmlMap[child.name()]);
						item = (new dataType()) as SavedItem;
					}
					if(item.importData(child)){
						added.push(item);
					}
					if(child.hasOwnProperty('items')){
						var subAdded = this.loadXml(child.items[0]);
					}
				}
			}
			return added;
		}
		
		override public function addItem(item:Object):Boolean{
			return addSavedItem(item as SavedItem);
		}
		override public function removeItem(item:Object):Boolean{
			return removeSavedItem(item as SavedItem);
		}
		override public function hasItem(item:Object):Boolean{
			return hasSavedItem(item as SavedItem);
		}
		override public function getItems():Array{
			var items:Array = new Array();
			
			for(var model:String in _models){
				for(var id:String in _models[model]){
					items.push(_models[model][id]);
				}
			}
			return items;
		}
		
		
		public function addSavedItem(item:SavedItem):Boolean{
			if(item == null || !item.loaded){
				return false;
			}
			if(!_models.hasOwnProperty(item.model)){
				_models[item.model] = new Object();
			}
			var list:Object = _models[item.model]
			if(list.hasOwnProperty(item.id)){
				if(list[item.id] == item){
					return true;
				}else{
					return false;
				}
			}else{
				list[item.id] = item;
				item.addEventListener(SavedItem.UNLOADED,itemUnloadHandler);
				dispatchEvent(new CollectionEvent(CollectionEvent.ADDED,item));
				return true;
			}
			return false;
		}
		public function removeSavedItem(item:SavedItem):Boolean{
			if(item == null){
				return false;
			}
			if(hasSavedItem(item)){
				delete _models[item.model][item.id];
				item.removeEventListener(SavedItem.UNLOADED,itemUnloadHandler);
				item.unload();
				return true;
			}
			return false;
		}
		public function hasSavedItem(item:SavedItem):Boolean{
			var found = getItem(item.model,item.id);
			return (found == item);
		}
		public function getItem(model:String,id:int):SavedItem{
			if(_models.hasOwnProperty(model) && _models[model].hasOwnProperty(id)){
				return _models[model][id];
			}
			return null;
		}
		//////////// Private functions ////////////
		
		//////////// Event Handlers functions ////////////
		protected function itemUnloadHandler(e:Event){
			removeSavedItem(e.target as SavedItem);
		}

	}
	
}
