package {
	import flash.events.Event;
	import flash.display.MovieClip;

	public class EffectSkin extends BoundSkin{
		//////////// variables ////////////
		protected var _effect:Effect;
		protected var _skillInst:SkillInstance;
		
		//////////// Static variables ////////////
		
		//////////// Constructor ////////////
        public function EffectSkin() {
			
        }
		
		//////////// Properties functions ////////////
		public function get effect():Effect{
			return _effect;
		}
		public function get skillInst():SkillInstance{
			return _skillInst;
		}
		
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		public function setupEffect(effect:Effect, skillInst:SkillInstance){
			if(_effect == null){
				_effect = effect;
				_skillInst = skillInst;
				if(effect.endEventName != null){
					skillInst.addEventListener(effect.endEventName,removeEffectHandler);
				}
			}
		}
		
		override public function destroy(){
			super.destroy();
			if(effect != null && effect.endEventName != null){
				skillInst.removeEventListener(effect.endEventName,removeEffectHandler);
			}
			_effect = null;
			_skillInst = null;
		}
		
		//////////// Private functions ////////////
		
		//////////// Event Handlers functions ////////////
		protected function removeEffectHandler(e:Event){
			destroy();
		}
		
	}
}