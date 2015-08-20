package  {
	import flash.events.EventDispatcher;
	import xmlProps.*;
	import flash.utils.getDefinitionByName;
	
	public class Message extends SavedItem {
		//////////// variables ////////////
		protected var _user_id:int;
		protected var _character:SavedItemRef = new SavedItemRef(0,'character');
		protected var _text:String;
		protected var _x:Number;
		protected var _y:Number;
		protected var _time:Number;
		
		//////////// Static variables ////////////
		
		//////////// Constructor ////////////
        public function Message() {
			_model = 'message';
        }
		
		//////////// Properties functions ////////////
		public function get text():String{
			return _text;
		}
		public function get character():Character{
			return _character.item as Character;
		}
		public function get user_id():int{
			return _user_id;
		}
		public function get x():Number{
			return _x;
		}
		public function get y():Number{
			return _y;
		}
		public function get time():Number{
			return _time;
		}
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		
		//////////// Private functions ////////////
		override protected function xmlDataDefinition(){
			super.xmlDataDefinition();
			addXmlDataDefinitions([
					new SimpleXmlProp('text'),
					new SimpleXmlProp('user_id'),
					new SimpleXmlProp('character_id'),
					new SimpleXmlProp('x'),
					new SimpleXmlProp('y'),
					new TimeXmlProp('time'),
				]);
		}
		
			
		override protected function getDataForXml():Object{
			var data:Object = super.getDataForXml();
			
			//data['text'] = _name;
			
			return data;
		}
		override protected function setDataFromXml(data:Object):Boolean{
			if(super.setDataFromXml(data)){
			
				if(data.hasOwnProperty('text')) _text = data['text'];
				if(data.hasOwnProperty('user_id')) _user_id = data['user_id'];
				if(data.hasOwnProperty('character_id')) _character.id = data['character_id'];
				if(data.hasOwnProperty('x')) _x = data['x'];
				if(data.hasOwnProperty('y')) _y = data['y'];
				if(data.hasOwnProperty('time')) _time = data['time'];
			
				return true;
			}
			return false;
		}
		
		//////////// Event Handlers functions ////////////

	}
	
}
