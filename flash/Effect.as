package  {
	import flash.events.Event;
	import xmlProps.SimpleXmlProp;
	
	
	[Event(name="showEffect", type="SkillEvent")]
	public class Effect extends SavedItem{

		//////////// variables ////////////
		protected var _skill:SavedItemRef = new SavedItemRef(0,'skill');
		protected var _skin:SavedItemRef = new SavedItemRef(0,'skin');
		protected var _attachment:String;
		protected var _eventName:String;
		protected var _endEventName:String;
		
		//////////// Static variables ////////////
		
		//////////// Constructor ////////////
        public function Effect() {
			_model = 'effect';
			_skill.addEventListener(Event.CHANGE,skillChangeHandler);
        }
		
		//////////// Properties functions ////////////
		public function get skill():Skill{
			return _skill.item as Skill;
		}
		public function get skin():SkinDefinition{
			return _skin.item as SkinDefinition;
		}
		public function get attachment():String{
			return _attachment;
		}
		public function get eventName():String{
			return _eventName;
		}
		public function get endEventName():String{
			return _endEventName;
		}
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		
		//////////// Private functions ////////////
		override protected function xmlDataDefinition(){
			super.xmlDataDefinition();
			addXmlDataDefinitions([
					new SimpleXmlProp('skill_id'),
					new SimpleXmlProp('skin_id'),
					new SimpleXmlProp('attachment'),
					new SimpleXmlProp('event_name'),
					new SimpleXmlProp('end_event_name'),
				]);
		}
		
			
		override protected function getDataForXml():Object{
			var data:Object = super.getDataForXml();
			
			
			return data;
		}
		override protected function setDataFromXml(data:Object):Boolean{
			if(super.setDataFromXml(data)){
			
				if(data.hasOwnProperty('skill_id')) _skill.id = data['skill_id'];
				if(data.hasOwnProperty('skin_id')) _skin.id = data['skin_id'];
				if(data.hasOwnProperty('attachment')) _attachment = data['attachment'];
				if(data.hasOwnProperty('event_name')) _eventName = data['event_name'];
				if(data.hasOwnProperty('end_event_name')) _endEventName = data['end_event_name'];
			
				return true;
			}
			return false;
		}
		
		
		//////////// Event Handlers functions ////////////
		protected function skillChangeHandler(e:Event){
			if(_skill.id){
				skill.addEventListener(SkillEvent.INSTANCE_ADDED,instanceAddedHandler);
			}
		}
		protected function instanceAddedHandler(e:SkillEvent){
			if(eventName != null){
				e.skillInstance.addEventListener(eventName,showEffectHandler);
			}
		}
		protected function showEffectHandler(e:Event){
			dispatchEvent(new SkillEvent(SkillEvent.SHOW_EFFECT,e.target as SkillInstance));
			var attachements:Array = e.target.getAttachements(attachment);
			for (var i = 0; i < attachements.length; i++) {
				var attach:SkinnedObject = attachements[i] as SkinnedObject;
				var skinInst:EffectSkin = skin.getInstance() as EffectSkin;
				skinInst.setupEffect(this,e.target as SkillInstance);
				skinInst.zOffset = 1;
				skinInst.target = attach.skin;
				skinInst.isMain = false;
				skinInst.owner = attach;
			}
		}

	}
	
}
