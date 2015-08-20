package {
	import flash.display.MovieClip;
	import flash.events.Event;

	public class PersoSkin extends ViewSideSkin {
		//////////// variables ////////////
		public var model:MovieClip;
		
		//////////// Static variables ////////////
		
		//////////// Constructor ////////////
        public function PersoSkin() {
			addEventListener(Event.ADDED_TO_STAGE,addedToStageHandler);
			addEventListener(ConfigEvent.CHANGED,OwnerChangedHandler);
        }
		
		//////////// Properties functions ////////////
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		
		//////////// Private functions ////////////
		
		
		//////////// Event Handlers functions ////////////
		protected function addedToStageHandler(e:Event){
			model.gotoAndStop(2);
			animator.clip = model;
			animator.addPhase(new SequencePhase("stand"));
		}
		protected function OwnerChangedHandler(e:ConfigEvent){
			if(e.configName == 'owner'){
				var oldVal:Character = e.oldVal as Character;
				var newVal:Character = e.newVal as Character;
				if(oldVal != null){
					oldVal.removeEventListener(Character.DIED,diedHandler);
				}
				if(newVal != null){
					newVal.addEventListener(Character.DIED,diedHandler);
				}
			}
		}
		
		
		override protected function moveHandler(e:Event){
			super.moveHandler(e);
			if(displayed){
				if(animator.curPhase == null || animator.curPhase.sequence != "walk"){
					animator.addPhase(new SequencePhase("walk", -1, 200),0);
				}else{
					animator.curPhase.reset();
				}
			}
		}
		
		
		protected function diedHandler(e:EventFromSaved){
			animator.addPhase(new SequencePhase("death",0),0);
			animator.addPhase(new SequencePhase("deathend"),1);
		}
	}
}