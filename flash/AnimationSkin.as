package {
	import flash.events.Event;
	import flash.display.MovieClip;

	public class AnimationSkin extends EffectSkin{
		//////////// variables ////////////
		public var model:MovieClip;
		
		//////////// Static variables ////////////
		
		//////////// Constructor ////////////
        public function AnimationSkin() {
			addEventListener(Event.ADDED_TO_STAGE,addedToStageHandler);
        }
		
		//////////// Properties functions ////////////
		
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		
		
		//////////// Private functions ////////////
		
		//////////// Event Handlers functions ////////////
		protected function addedToStageHandler(e:Event){
			animator.clip = model;
			animator.addEventListener(SequencesAnimator.SEQUENCE_END,animationEndHandler);
			animator.setSequence('1');
		}
		
		protected function animationEndHandler(e:Event){
			if(parent == null){
				trace('garbage collection error');
			}
			destroy();
		}
	}
}