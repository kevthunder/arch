package  {
	import flash.events.EventDispatcher;
	import xmlProps.*;
	import flash.utils.getDefinitionByName;
	
	[Event(name="instanceAdded", type="SkillEvent")]
	public class Skill extends SavedItem {
		//////////// variables ////////////
		protected var _name:String;
		protected var _range:Number;
		
		//////////// Static variables ////////////
		
		//////////// Constructor ////////////
        public function Skill(name:String=null) {
			_model = 'skill';
			var typesToLoad:Array = [
				CastedAbility
			];
			_name = name;
        }
		
		//////////// Properties functions ////////////
		public function get name():String{
			return _name;
		}
		public function get range():Number{
			return _range;
		}
		
		//////////// Static functions ////////////
		static function getXmlDataType(xml:XML,BaseClass:Class):Class{
			if(xml.hasOwnProperty("ui_behavior") && xml.ui_behavior.hasOwnProperty("@base_class")){
				return getDefinitionByName(xml.ui_behavior.@base_class.toString()) as Class;
			}else{
				return Skill;
			}
		}
		
		//////////// Public functions ////////////
		public function addInstance(inst:SkillInstance){
			dispatchEvent(new SkillEvent(SkillEvent.INSTANCE_ADDED,inst));
		}
		
		//////////// Private functions ////////////
		override protected function xmlDataDefinition(){
			super.xmlDataDefinition();
			addXmlDataDefinitions([
					new SimpleXmlProp('title'),
					new SimpleXmlProp('range')
				]);
		}
		
			
		override protected function getDataForXml():Object{
			var data:Object = super.getDataForXml();
			
			data['title'] = _name;
			data['range'] = _range;
			
			return data;
		}
		override protected function setDataFromXml(data:Object):Boolean{
			if(super.setDataFromXml(data)){
			
				if(data.hasOwnProperty('title')) _name = data['title'];
				if(data.hasOwnProperty('range')) _range = parseFloat(data['range']);
			
				return true;
			}
			return false;
		}
		
		//////////// Event Handlers functions ////////////

	}
	
}
