package  {
	import flash.events.Event;
	import flash.sampler.NewObjectSample;
	import xmlProps.SimpleXmlProp;
	
	[Event(name="castingStarted", type="SkillInstance")]
	public class SkillInstance extends SavedItem{

		//////////// variables ////////////
		protected var _caster:SavedItemRef = new SavedItemRef(0,'character');
		protected var _skill:SavedItemRef = new SavedItemRef(0,'skill');
		protected var _mainTarget:SavedItemAnyRef = new SavedItemAnyRef();
		protected var _castReq:ConnectionXMLRequest;
		
		//////////// Static variables ////////////
		public static const CASTING_STARTED:String = 'castingStarted';
		
		//////////// Constructor ////////////
        public function SkillInstance(skill:Skill = null,caster:Character = null,castReq:ConnectionXMLRequest = null) {
			_skill.addEventListener(Event.CHANGE,skillChangeHandler);
			_caster.item = caster;
			_skill.item = skill;
			if(castReq != null){
				_castReq = castReq;
				castReq.addEventListener(ConnectionXMLRequest.LOAD_ITEMS,reqLoadItemHandler);
				dispatchEvent(new Event(CASTING_STARTED));
			}
			_model = 'skill_instance';
        }
		
		//////////// Properties functions ////////////
		public function get caster():Character{
			return _caster.item as Character;
		}
		public function get skill():Skill{
			return _skill.item as Skill;
		}
		public function get mainTarget():SavedItem{
			return _mainTarget.item;
		}
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		public function getAttachements(key:String):Array{
			if(this.hasOwnProperty(key) && this[key] is SkinnedObject){
				return [this[key]];
			}
			if(key == 'target'){
				return [mainTarget];
			}
			return [];
		}
		
		//////////// Private functions ////////////
		override protected function xmlDataDefinition(){
			super.xmlDataDefinition();
			addXmlDataDefinitions([
					new SimpleXmlProp('caster_id'),
					new SimpleXmlProp('skill_id'),
					new SimpleXmlProp('main_target_model'),
					new SimpleXmlProp('main_target_key'),
				]);
		}
		
			
		override protected function getDataForXml():Object{
			var data:Object = super.getDataForXml();
			
			
			return data;
		}
		override protected function setDataFromXml(data:Object):Boolean{
			if(super.setDataFromXml(data)){
			
				if(data.hasOwnProperty('caster_id')) _caster.id = data['caster_id'];
				if(data.hasOwnProperty('skill_id')) _skill.id = data['skill_id'];
				if(data.hasOwnProperty('main_target_model')) _mainTarget.model = data['main_target_model'];
				if(data.hasOwnProperty('main_target_key')) _mainTarget.id = data['main_target_key'];
			
				return true;
			}
			return false;
		}
		
		//////////// Event Handlers functions ////////////
		protected function reqLoadItemHandler(e:Event){
			importData(_castReq.xmlResponse.items[0].skill_instance[0]);
		}
		protected function skillChangeHandler(e:Event){
			if(skill){
				skill.addInstance(this);
			}
		}


	}
	
}
