package  {
	import flash.events.Event;
	import flash.events.EventDispatcher;

	[Event(name="change", type="flash.events.Event")]
	public class SavedItemRef extends EventDispatcher{
		//////////// variables ////////////
		protected var _id:int;
		protected var _model:String;
		protected var _item:SavedItem;
		protected var _collection:SavedItemCollection;
		
		//////////// Static variables ////////////
		
		//////////// Constructor ////////////
        public function SavedItemRef(id:int,model:String) {
			_id = id;
			_model = model;
			_collection = ClientMain.instance.savedItems;
        }
		
		//////////// Properties functions ////////////
		public function get id():int{
			return _id;
		}
		public function set id(val:int):void{
			if(_id != val){
				var oldItem:SavedItem = item;
				_id = val;
				if(_id != 0){
					item = getSavedItem();
					if(item == null){
						collection.addEventListener(CollectionEvent.ADDED,savedItemAddedHandler);
					}
				}else{
					item = null;
				}
			}
		}
		
		public function get model():String{
			return _model;
		}
		
		public function get item():SavedItem{
			return _item;
		}
		public function set item(val:SavedItem):void{
			if(_item != val){
				setItem(val);
				if(val != null){
					_id = val.id;
				}
				updated();
			}
		}
		public function get collection():SavedItemCollection{
			return _collection;
		}
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		
		//////////// Private functions ////////////
		protected function getSavedItem():SavedItem{
			return collection.getItem(_model,_id);
		}
		protected function setItem(val:SavedItem):void{
			if(_item != null){
				_item.removeEventListener(SavedItem.LOADED,itemLoadedHandler);
				_item.removeEventListener(SavedItem.UNLOADED,itemUnloadHandler);
			}
			_item = val
			if(_item != null){
				_item.addEventListener(SavedItem.LOADED,itemLoadedHandler);
				_item.addEventListener(SavedItem.UNLOADED,itemUnloadHandler);
			}
		}
		protected function updated(){
			dispatchEvent(new Event(Event.CHANGE));
		}
		
		//////////// Event Handlers functions ////////////
		protected function savedItemAddedHandler(e:CollectionEvent){
			var item:SavedItem = e.item as SavedItem;
			if(item != null && item.id == id && item.model == model){
				collection.removeEventListener(CollectionEvent.ADDED,savedItemAddedHandler);
				this.item = item;
			}
		}
		protected function itemUnloadHandler(e:Event){
			if(e.target == _item){
				item = null;
			}
		}
		protected function itemLoadedHandler(e:Event){
			if(e.target == _item){
				_id = (e.target as SavedItem).id;
			}
		}

	}
	
}
