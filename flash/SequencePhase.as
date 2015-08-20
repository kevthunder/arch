package {
	import flash.display.MovieClip;
	import flash.display.FrameLabel;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.utils.getTimer;

	public class SequencePhase extends EventDispatcher{
		//////////// variables ////////////
		protected var _animator:SequencesAnimator;
		protected var _maxIterations:int = -1;
		protected var _curIterations:int = 0;
		protected var _maxTime:int = -1;
		protected var _statrTime:int = 0;
		protected var _sequence:String;
		
		public static const PHASE_END:String = 'phase_end';
		
		
		//////////// Constructor ////////////
        public function SequencePhase(sequence:String = null, maxIterations:int =  -1, maxTime:int = -1) {
			_sequence = sequence;
			_maxIterations = maxIterations;
			_maxTime = maxTime;
        }
		
		//////////// Properties functions ////////////
		public function get animator():SequencesAnimator{
			return _animator;
		}
		public function set animator(val:SequencesAnimator):void{
			if(_animator != val){
				if(_animator != null){
					_animator.removeEventListener(Event.EXIT_FRAME,exitFrameHandler);
					_animator.removeEventListener(SequencesAnimator.SEQUENCE_END,sequenceEndHandler);
				}
				_animator = val;
				if(_animator != null){
					_animator.setSequence(sequence);
					_animator.addEventListener(Event.EXIT_FRAME,exitFrameHandler);
					_animator.addEventListener(SequencesAnimator.SEQUENCE_END,sequenceEndHandler);
				}
			}
		}
		public function get maxIterations():int{
			return _maxIterations;
		}
		public function set maxIterations(val:int):void{
			_maxIterations = val
		}
		public function get maxTime():int{
			return _maxTime;
		}
		public function set maxTime(val:int):void{
			_maxTime = val
		}
		public function get sequence():String{
			return _sequence;
		}
		public function set sequence(val:String):void{
			_sequence = val
		}
		
		
		//////////// Public functions ////////////
		public function reset(){
			_statrTime = getTimer();
			_curIterations = 0;
		}
		
		//////////// Private functions ////////////
		private function sequenceEndHandler(e:Event){
			_curIterations++
			if(_maxIterations != -1 && _curIterations > _maxIterations){
				dispatchEvent(new Event(PHASE_END));
			}else{
				_animator.setSequence(sequence);
			}
		}
		private function exitFrameHandler(e:Event){
			if(_maxTime != -1 && getTimer()-_statrTime > _maxTime){
				dispatchEvent(new Event(PHASE_END));
			}
		}
		
	}
}