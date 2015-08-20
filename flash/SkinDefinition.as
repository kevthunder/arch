package  {
	import flash.events.Event;
	import flash.utils.getDefinitionByName;
	import xmlProps.SimpleXmlProp;
	
	
	[Event(name="showEffect", type="SkillEvent")]
	public class SkinDefinition extends SavedItem{

		//////////// variables ////////////
		protected var _className:String;
		
		//////////// Static variables ////////////
		
		//////////// Constructor ////////////
        public function SkinDefinition() {
			_model = 'skin';
        }
		
		//////////// Properties functions ////////////
		public function get className():String{
			return _className;
		}
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		public function getInstance():Skin{
			return loadSkinByName(className);
		}
		
		//////////// Private functions ////////////
		protected function loadSkinByName(classname:String):Skin{
			var c:Class;
			try{
				c = getDefinitionByName(classname) as Class;
			}catch(e){
				
			}
			if(c){
				var val:Skin = new c() as Skin;
				if(val){
					return val;
				}
			}
			return null;
		}
		
		override protected function xmlDataDefinition(){
			super.xmlDataDefinition();
			addXmlDataDefinitions([
					new SimpleXmlProp('class_name'),
				]);
		}
		
			
		override protected function getDataForXml():Object{
			var data:Object = super.getDataForXml();
			
			
			return data;
		}
		override protected function setDataFromXml(data:Object):Boolean{
			if(super.setDataFromXml(data)){
			
				if(data.hasOwnProperty('class_name')) _className = data['class_name'];
			
				return true;
			}
			return false;
		}
		
		
		//////////// Event Handlers functions ////////////

	}
	
}
