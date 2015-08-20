package  {
	import flash.events.Event;
	import flash.events.EventDispatcher;

	public class SavedItemAnyRef extends SavedItemRef{
		//////////// variables ////////////
		
		//////////// Static variables ////////////
		
		//////////// Constructor ////////////
        public function SavedItemAnyRef(id:int = 0,model:String = null) {
			super(id,model);
        }
		
		//////////// Properties functions ////////////
		public function set model(val:String):void{
			if(_model != val){
				_model = val;
				setItem(getSavedItem());
				if(item == null){
					_id = 0;
				}
				updated();
			}
		}
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		
		//////////// Private functions ////////////
		
		//////////// Event Handlers functions ////////////

	}
	
}
