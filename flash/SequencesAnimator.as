package {
	import flash.display.MovieClip;
	import flash.display.FrameLabel;
	import flash.events.Event;
	import flash.events.EventDispatcher;

	[Event(name="sequenceEnd", type="SequencesAnimator")]
	public class SequencesAnimator extends EventDispatcher{
		//////////// variables ////////////
		protected var _clip:MovieClip;
		protected var _label:String;
		protected var _curPhase:SequencePhase;
		protected var _phases:Array;
		protected var _playing:Boolean;
		
		public static const SEQUENCE_END:String = 'sequenceEnd';
		
		
		//////////// Constructor ////////////
        public function SequencesAnimator() {
			_phases = new Array();
        }
		
		//////////// Properties functions ////////////
		public function get clip():MovieClip{
			return _clip;
		}
		public function set clip(val:MovieClip):void{
			if(_clip != val){
				if(_clip != null){
					_clip.removeEventListener(Event.EXIT_FRAME,exitFrameHandler);
				}
				_clip = val;
				if(_clip != null){
					_clip.addEventListener(Event.EXIT_FRAME,exitFrameHandler);
				}
			}
		}
		public function get curPhase():SequencePhase{
			return _curPhase;
		}
		public function get playing():Boolean{
			return _playing;
		}
		
		//////////// Public functions ////////////
		public function destroy(){
			clip = null;
			updateCurPhase(null);
		}
		public function addPhase(phase:SequencePhase,at:int = int.MAX_VALUE):Boolean{
			if(getPhaseIndex(phase) == -1){
				at = Math.min(at,_phases.length);
				_phases.splice(at,0,phase);
				if(at == 0){
					updateCurPhase(phase);
				}
				//dispatchEvent(new CollectionEvent(CollectionEvent.ADDED,phase));
				return true;
			}
			return false;
		}
		public function removePhase(phase:SequencePhase):Boolean{
			var index:int = getPhaseIndex(phase);
			return removePhaseAt(index);
		}
		
		public function hasPhase(phase:SequencePhase):Boolean{
			return getPhaseIndex(phase) != -1;
		}
		public function getPhaseIndex(phase:SequencePhase,start:int=0):int{
			for(var i:int = start; i<_phases.length; i++){
				if(_phases[i] == phase){
					return i;
				}
			}
			return -1;
		}
		protected function removePhaseAt(index:int):Boolean{
			if(index != -1){
				var phase = _phases[index];
				_phases.splice(index,1);
				if(index == 0){
					if(_phases.length == 0){
						updateCurPhase(null);
					}else{
						updateCurPhase(_phases[0]);
					}
				}
				//dispatchEvent(new CollectionEvent(CollectionEvent.REMOVED,phase));
				return true;
			}
			return false;
		}
		
		
		
		public function setSequence(label:String){
			if(_clip){
				if(parseInt(label).toString() == label){
					_clip.gotoAndPlay(label);
					_playing = true;
				}else{
					var pos:int = this.getLabelPos(label);
					if(pos != -1){
						_playing = getLabelAt(pos+1) == label;
						if(_playing){
							_clip.gotoAndPlay(label);
						}else{
							_clip.gotoAndStop(label);
						}
					}
				}
			}
		}
		
		//////////// Private functions ////////////
		protected function updateCurPhase(val:SequencePhase):void{
			if(_curPhase != val){
				if(_curPhase != null){
					_curPhase.animator = null;
					_curPhase.removeEventListener(SequencePhase.PHASE_END,phaseEndHandler);
				}
				_curPhase = val;
				if(_curPhase != null){
					_curPhase.reset();
					_curPhase.animator = this;
					_curPhase.addEventListener(SequencePhase.PHASE_END,phaseEndHandler);
				}else{
					_clip.stop();
					_playing = false;
				}
			}
		}
		protected function getLabelAt(frame:int):String{
			if(_clip.currentLabels.length != 0 && frame <= _clip.totalFrames){
				for (var i:int = _clip.currentLabels.length-1; i >= 0; i--) {
					if(_clip.currentLabels[i].frame <= frame){
						return _clip.currentLabels[i].name ;
					}
				}
			}
			return null;
		}
		protected function getLabelPos(label:String):int{
			if(_clip.currentLabels.length != 0){
				for (var i:int = 0; i < _clip.currentLabels.length; i++) {
					if(_clip.currentLabels[i].name == label) return _clip.currentLabels[i].frame;
				}
			}
			return -1;
		}
		private function phaseEndHandler(e:Event){
			removePhaseAt(0);
		}
		private function exitFrameHandler(e:Event){
			if(_playing && (_clip.currentFrame == _clip.totalFrames || getLabelAt(_clip.currentFrame+1) != _clip.currentLabel)){
				dispatchEvent(new Event(SEQUENCE_END));
			}
			dispatchEvent(e.clone());
		}
		
		
	}
}