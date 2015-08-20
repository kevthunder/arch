package  {
	
	public class CollectionLink {
		//////////// variables ////////////
		protected var _source:CollectionBase;
		protected var _target:CollectionBase;
		protected var _filter:Function;
		protected var _imported:Array = new Array();
		
		
		//////////// Static variables ////////////
		
		//////////// Constructor ////////////
        public function CollectionLink(source:CollectionBase, target:CollectionBase, filter:Function) {
			this.target = target;
			this.source = source;
			this.filter = filter;
			importAll();
        }
		
		//////////// Properties functions ////////////
		public function get target():CollectionBase{
			return _target;
		}
		public function set target(val:CollectionBase){
			if(_target != val){
				if(_target != null){
					unimportAll();
					_target.removeCollectionLink(this);
				}
				_target = val;
				if(_target != null){
					importAll();
					_target.addCollectionLink(this);
				}
			}
		}
		public function get source():CollectionBase{
			return _source;
		}
		public function set source(val:CollectionBase){
			if(_source != val){
				if(_source!=null){
					_source.removeEventListener(CollectionEvent.ADDED,addedItemHandler);
					_source.removeEventListener(CollectionEvent.REMOVED,removedItemHandler);
					_source.removeCollectionLink(this);
				}
				_source = val;
				if(_source!=null){
					_source.addEventListener(CollectionEvent.ADDED,addedItemHandler);
					_source.addEventListener(CollectionEvent.REMOVED,removedItemHandler);
					_source.addCollectionLink(this);
				}
				updateAll();
			}
		}
		public function get filter():Function{
			return _filter;
		}
		public function set filter(val:Function){
			if(_filter != val){
				_filter = val;
				updateAll();
			}
		}
		
		
		//////////// Static functions ////////////
		public static function filterNone(item:Object = null){
			return false;
		}
		
		//////////// Public functions ////////////
		public function isImported(item:Object){
			return (_imported.indexOf(item) != -1);
		}
		
		public function updateNow(){
			updateAll();
		}
		
		public function del(){
			source = null;
			source = null;
			filter = null;
		}
		
		//////////// Private functions ////////////
		protected function importAll(){
			if(source != null){
				var items:Array = source.getItems();
				if(items != null){
					for(var i:int=0; i<items.length; i++){
						importItem(items[i]);
					}
				}
			}
		}
		protected function updateAll(){
			for(var i:int=0; i<_imported.length; i++){
				updateImportedAt(i);
			}
			importAll();
		}
		protected function unimportAll(){
			while(_imported.length){
				unimportAt(0);
			}
		}
		protected function importItem(item:Object):Boolean{
			if(_imported.indexOf(item) == -1 && (_filter == null || _filter(item)) ){
				if(target.addItem(item)){
					_imported.push(item);
					return true;
				}
			}
			return false;
		}
		protected function unimport(item:Object){
			return unimportAt(_imported.indexOf(item));
		}
		protected function unimportAt(pos:int){
			if(pos != -1){
				target.removeItem(_imported[pos]);
				_imported.splice(pos, 1);
				return true;
			}
			return false;
		}
		protected function updateImportedAt(pos:int){
			if(source == null || !source.hasItem(_imported[pos]) || !(_filter == null || _filter(_imported[pos]))){
				unimportAt(pos);
			}
		}
		
		//////////// Event Handlers functions ////////////
		function addedItemHandler(e:CollectionEvent){
			importItem(e.item);
		}
		function removedItemHandler(e:CollectionEvent){
			unimport(e.item);
		}
		

	}
	
}
