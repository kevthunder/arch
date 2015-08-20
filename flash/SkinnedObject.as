package  {
	import flash.display.DisplayObject;
	import flash.events.Event;
	import flash.utils.getQualifiedClassName;
	import flash.utils.getDefinitionByName;
	import flash.filters.BlurFilter;
	import xmlProps.SimpleXmlProp;
	
	[Event(name="displayed", type="SkinnedObject")]
	[Event(name="hidded", type="SkinnedObject")]
	[Event(name="skinChange", type="SkinnedObject")]
	public class SkinnedObject extends SavedItem {
		//////////// variables ////////////
		protected var _skinDefinition:SavedItemRef = new SavedItemRef(0,'skin');
		protected var _skin:Skin;
		protected var _displayed:Boolean;
		
		//////////// Static variables ////////////
		public static const DISPLAYED:String = 'item displayed';
		public static const HIDDED:String = 'item hidded';
		public static const SKIN_CHANGE:String = 'skin Change';
		
		
		//////////// Constructor ////////////
        public function SkinnedObject() {
			_skinDefinition.addEventListener(Event.CHANGE,skinRefChangeHandler);
        }
		
		//////////// Properties functions ////////////
		
		
		public function get skinDefinition():SkinDefinition{
			return _skinDefinition.item as SkinDefinition;
		}
		
		public function get skin():Skin{
			return _skin;
		}
		public function set skin(val:Skin){
			if(_skin != val){
				if(_skin){
					_skin.owner = null;
				}
				_skin = val;
				if(_skin){
					_skin.owner = this;
				}
				dispatchEvent(new Event(SKIN_CHANGE));
			}
		}
		
		
		public function get displayed():Boolean{
			return _displayed;
		}
		public function set displayed(val:Boolean){
			if(_displayed != val){
				_displayed = val;
				if(_displayed){
					dispatchEvent(new Event(DISPLAYED));
				}else{
					dispatchEvent(new Event(HIDDED));
				}
			}
		}
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		
		//////////// Private functions ////////////
		
		override protected function xmlDataDefinition(){
			super.xmlDataDefinition();
			addXmlDataDefinitions([
					new SimpleXmlProp('skin_id'),
				]);
		}
		
			
		override protected function getDataForXml():Object{
			var data:Object = super.getDataForXml();
			
			
			
			return data;
		}
		override protected function setDataFromXml(data:Object):Boolean{
			if(super.setDataFromXml(data)){
			
				if(data.hasOwnProperty('skin_id')) 
					_skinDefinition.id = data['skin_id'];
			
				return true;
			}
			return false;
		}
		
		//////////// Event Handlers functions ////////////
		protected function skinRefChangeHandler(e:Event){
			if(_skinDefinition.id && skinDefinition){
				var val = skinDefinition.getInstance();
				if(val){
					skin = val;
				}
			}
		}


	}
	
}
