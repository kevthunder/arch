package {
	import com.flashkev.customList.CustomList;
	import flash.events.Event;

	[Event(name="open", type="Event")]
	[Event(name="close", type="Event")]
	public class Inventory extends Popup {
		//////////// variables ////////////
		protected var _list:CustomList;
		
		
		//////////// Static variables ////////////
		
		
		//////////// Constructor ////////////
        public function Inventory() {
			addEventListener(Event.OPEN,openHandler);
        }
		
		//////////// Properties functions ////////////
		
		
		public function get list():CustomList{
			return _list;
		}
		public function set list(val:CustomList):void{
			if(_list != val){
				_list = val
				if(_list){
					_list.setRendererStyle('displayField','title');
					_list.addEventListener(Event.CHANGE,selectChangeHandler);
				}
			}
		}
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		
		
		//////////// Private functions ////////////
		
		
		//////////// Event Handlers functions ////////////
		protected function openHandler(e:Event){
			var req:ConnectionXMLRequest = new ConnectionXMLRequest('items', 'my_inventory');
			ClientMain.instance.connection.addXmlRequest(req);
			req.addEventListener(ConnectionRequest.SUCCESS,inventoryLoadedHandler);
			
		}
		protected function inventoryLoadedHandler(e:Event){
			var req:ConnectionXMLRequest = e.target as ConnectionXMLRequest;
			for(var i:int = 0; i < req.items.length; i++){
				list.addItem(req.items[i]);
			}
		}
		protected function selectChangeHandler(e:Event){
		}
		
	}
}