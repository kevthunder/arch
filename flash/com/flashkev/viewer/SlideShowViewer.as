package com.flashkev.viewer{
	import flash.events.Event;
	import flash.events.TimerEvent;
	import flash.utils.Timer;
	
	import fl.core.UIComponent;
	import fl.core.InvalidationType;

	public class SlideShowViewer extends Viewer{
		//////////// variables ////////////
		protected var _playlist:Array;
		protected var _playlistPos:int = 0;
		protected var _playing:Boolean = false;
		protected var timer:Timer;
		
		private static var defaultStyles:Object = {
				delay:3000,
				autoPlay:true
			};
			
		//////////// Constructor ////////////
        public function SlideShowViewer() {
			timer= new Timer(3000);
			timer.addEventListener(TimerEvent.TIMER,timerHandler);
        }
		//////////// Properties functions ////////////
		protected function get playlist():Array{
			return _playlist;
		}
		
		protected function get playlistPos():int{
			return _playlistPos;
		}
		protected function set playlistPos(val:int):void{
			if(_playlistPos != val){
				_playlistPos = val;
				displayPlaylistItem(_playlistPos);
			}
		}
		
		protected function get playing():Boolean{
			return _playing;
		}
		protected function set playing(val:Boolean):void{
			if(!_playlist){
				val = false
			}
			if(_playing != val){
				_playing = val;
				if(_playing){
					timer.delay = Number(getStyleValue("delay"));
					timer.start();
				}else{
					timer.reset();
					timer.stop();
				}
			}
		}
		
		//////////// Public functions ////////////
		public static function getStyleDefinition():Object { 
			return UIComponent.mergeStyles(defaultStyles, Viewer.getStyleDefinition());
		}
		
		override public function display(source:*,type:String = null){
			if(source is Array){
				displayPlaylist(source);
			}else{
				super.display(source,type);
			}
		}
		
		public function displayPlaylist(playlist:Array){
			if(playlist && !playlist.length){
				playlist = null;
			}
			playing = false;
			_playlistPos = 0;
			_playlist = playlist;
			if(_playlist){
				displayPlaylistItem(0);
				var autoPlay:Boolean = Boolean(getStyleValue("autoPlay"));
				playing = autoPlay;
			}
		}
		public function nextPlaylistItem(){
			if(_playlist){
				if(playlistPos+1<_playlist.length){
					playlistPos++;
				}else{
					playlistPos = 0;
				}
			}
		}
		public function prevPlaylistItem(){
			if(_playlist){
				if(playlistPos>0){
					playlistPos--;
				}else{
					playlistPos = _playlist.length -1;
				}
			}
		}
		
		//////////// Private functions ////////////
		
		private function displayPlaylistItem(index:int){
			if(_playlist){
				display(_playlist[index]);
			}
		}
		
		//////////// Event Handlers functions ////////////
		protected function timerHandler(e:TimerEvent){
			nextPlaylistItem();
		}
		
		
	}
}