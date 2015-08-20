package  {
	import flash.events.EventDispatcher;
	import flash.events.Event;
	
	[Event(name="added", type="CollectionEvent")]
	[Event(name="removed", type="CollectionEvent")]
	public class CollectionBase extends EventDispatcher{
		//////////// variables ////////////
		protected var _collectionLinks:Array = new Array();
		
		//////////// Static variables ////////////
		
		//////////// Constructor ////////////
        public function CollectionBase() {
        }
		
		//////////// Properties functions ////////////
		public function get collectionLinks(){
			return _collectionLinks.concat();
		}
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		public function addItem(item:Object):Boolean{
			return false;
		}
		public function removeItem(item:Object):Boolean{
			return false;
		}
		public function hasItem(item:Object):Boolean{
			return false;
		}
		public function getItems():Array{
			return null;
		}
		public function importArray(arr:Array){
			var added:int = 0;
			if(arr != null){
				for(var i:int = 0; i<arr.length; i++){
					if(addItem(arr[i])){
						added++;
					}
				}
			}
			return added;
		}
		public function clear():Boolean{
			return false;
		}
		
		public function addCollectionLink(link:CollectionLink):Boolean{
			if(!hasCollectionLink(link)){
				_collectionLinks.push(link);
				return true;
			}else{
				return true;
			}
			return false;
		}
		public function removeCollectionLink(link:CollectionLink):Boolean{
			var i = indexOfCollectionLink(link)
			if(i != -1){
				_collectionLinks.splice(i,1);
				link.del();
			}
			return false
		}
		public function hasCollectionLink(link:CollectionLink):Boolean{
			return indexOfCollectionLink(link) != -1;
		}
		//////////// Private functions ////////////
		protected function indexOfCollectionLink(link:CollectionLink):int{
			return _collectionLinks.indexOf(link);
		}
		
		//////////// Event Handlers functions ////////////

	}
	
}
