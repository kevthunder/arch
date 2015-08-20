package  {
	import flash.events.Event;
	import flash.sampler.NewObjectSample;
	import xmlProps.TimeXmlProp;
	import xmlProps.SimpleXmlProp;
	
	public class SavedEvent extends SavedItem{

		//////////// variables ////////////
		protected var _time:uint;
		protected var _aro:SavedItemAnyRef = new SavedItemAnyRef();
		protected var _aco:SavedItemAnyRef = new SavedItemAnyRef();
		protected var _requester_alias:String;
		protected var _controlled_alias:String;
		protected var _triggered:Boolean;
		
		//////////// Static variables ////////////
		
		//////////// Constructor ////////////
        public function SavedEvent() {
			_model = 'timed_event';
        }
		
		//////////// Properties functions ////////////
		public function get time():uint{
			return _time;
		}
		public function get aro():SavedItem{
			return _aro.item;
		}
		public function get aco():SavedItem{
			return _aco.item;
		}
		public function get requester_alias():String{
			return _requester_alias;
		}
		public function get controlled_alias():String{
			return _controlled_alias;
		}
		public function get triggered():Boolean{
			return _triggered;
		}
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		
		//////////// Private functions ////////////
		override protected function xmlDataDefinition(){
			super.xmlDataDefinition();
			addXmlDataDefinitions([
					new TimeXmlProp('time'),
					new SimpleXmlProp('aro_model'),
					new SimpleXmlProp('aro_key'),
					new SimpleXmlProp('aco_model'),
					new SimpleXmlProp('aco_key'),
					new SimpleXmlProp('requester_alias'),
					new SimpleXmlProp('controlled_alias')
				]);
		}
		
			
		override protected function getDataForXml():Object{
			var data:Object = super.getDataForXml();
			
			
			return data;
		}
		override protected function setDataFromXml(data:Object):Boolean{
			if(super.setDataFromXml(data)){
			
				if(data.hasOwnProperty('aro_model')) _aro.model = data['aro_model'];
				if(data.hasOwnProperty('aro_key')) _aro.id = data['aro_key'];
				if(data.hasOwnProperty('aco_model')) _aco.model = data['aco_model'];
				if(data.hasOwnProperty('aco_key')) _aco.id = data['aco_key'];
				if(data.hasOwnProperty('requester_alias')) _requester_alias = data['requester_alias'];
				if(data.hasOwnProperty('controlled_alias')) _controlled_alias = data['controlled_alias'];
				if(data.hasOwnProperty('time')) {
					_time = data['time'];
					var main:ClientMain = ClientMain.instance;
					main.addEventListener(Event.ENTER_FRAME,enterFrameHandler);
				};
			
				return true;
			}
			return false;
		}
		protected function triggerNow(){
			if(aro && requester_alias){
				aro.dispatchEvent(new EventFromSaved(requester_alias,this));
			}
			if(aco && controlled_alias){
				aco.dispatchEvent(new EventFromSaved(controlled_alias,this));
			}
			_triggered = true;
			
			var main:ClientMain = ClientMain.instance;
			main.removeEventListener(Event.ENTER_FRAME,enterFrameHandler);
		}
		
		//////////// Event Handlers functions ////////////
		protected function enterFrameHandler(e:Event){
			var main:ClientMain = ClientMain.instance;
			var curtime = main.getTimer();
			if(!triggered && time<curtime ){
				triggerNow();
			}
		}


	}
	
}
