package com.flashkev.display{
	import flash.display.MovieClip;
	import flash.events.MouseEvent;
	import flash.events.Event;
	import flash.display.FrameLabel;

	public class HoverButton extends MovieClip {
		//////////// variables ////////////
		protected var _active:Boolean;
		protected var _up:Boolean;

		//////////// Constructor ////////////
		
		public function HoverButton() {
			addEventListener(MouseEvent.MOUSE_OVER,mouseOverHandler);
			addEventListener(MouseEvent.MOUSE_OUT,mouseOutHandler);
			buttonMode = true;
			mouseChildren = false;
			stop();
		}

		
		//////////// Properties functions ////////////
		public function get active():Boolean{
			return _active;
		}
		public function set active(val:Boolean):void{
			if(_active != val){
				_active = val;
				updateState();
			}
		}
		
		public function get up():Boolean{
			return _up;
		}
		public function set up(val:Boolean):void{
			if(_up != val){
				_up = val;
				updateState();
			}
		}
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		public function getLabel(name:String):FrameLabel{
			for(var i:int = 0; i< this.currentLabels.length ;i++){
				var label:FrameLabel = this.currentLabels[i] as FrameLabel;
				if(label && label.name == name){ 
					return label;
				}
			}
			return null
		}
		
		//////////// Private functions ////////////
		protected function updateState(){
			var labels:Array;
			if(up){
				labels = ['up',2];
			}else{
				labels = ['down',1];
			}
			if(_active){
				labels.unshift('active'+(up?'Up':'Down'));
			}
			gotoLabels(labels);
		}
		
		//////////// Event Handlers functions ////////////
		protected function mouseOverHandler(e:Event = null){
			up = true;
		}
		protected function mouseOutHandler(e:Event = null){
			up = false;
		}
		
		function gotoLabels(labels:Array){
			var i:int = 0;
			while(i < labels.length && parseInt(labels[i]) != labels[i] && getLabel(labels[i]) == null){
				i++;
			}
			if(i < labels.length){
				gotoAndStop(labels[i]);
			}
		}
		
	}
}