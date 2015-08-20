package com.flashkev.docroot {
    import flash.display.*;
    import flash.text.TextField;
	import flash.events.*;
	
	import br.com.stimuli.loading.BulkLoader;
	
	public class SimpleRootLoader extends KRoot{
		//////////// variables ////////////
		public var _mcLoader:MovieClip;
		public var _txtLoader:TextField;
		public var initBulkLoader:BulkLoader = null
		
		public var min_prc:Number = 1;
		public var playAtLoaded:Boolean = true;
		public var estimatedSize:Number = 10000;
		
		private var scriptStartBytes:int = 0;
		private var _prcLoaded:Number = 0;
		private var _loadedGoto:String;
		
		public static const DOC_LOADED:String = 'doc_loaded';
		public static const INIT_LOADER:String = 'init loader';
		//////////// Constructor ////////////
		public function SimpleRootLoader(){
			initBulkLoader = BulkLoader.getLoader("init_loader");
			if(!initBulkLoader){
				initBulkLoader = new BulkLoader("init_loader");
			}
			addEventListener(Event.ENTER_FRAME,firstFrameHandler);
			if(stage){
				scriptStartBytes = stage.loaderInfo.bytesLoaded;
			}
			addEventListener(Event.ENTER_FRAME,loadingEnterFrameHandler);
			initBulkLoader.addEventListener(BulkLoader.COMPLETE,loadingEnterFrameHandler);
			stop();
			initBulkLoader.start();
		}
      
		//////////// Properties functions ////////////
		public function get prcLoaded():Number{
			return _prcLoaded;
		}
		
		public function set loadedGoto(val:String):void{
			_loadedGoto = val;
		}
		public function get loadedGoto():String{
			if(_loadedGoto != null){
				return _loadedGoto;
			}else if(currentLabels.indexOf('loaded')!==-1){
				return 'loaded';
			}else{
				return '2';
			}
			
		}
		
		public function set mcLoader(val:MovieClip):void{
			_mcLoader = val;
		}
		public function get mcLoader():MovieClip{
			return _mcLoader;
		}
		public function set txtLoader(val:TextField):void{
			_txtLoader = val;
		}
		public function get txtLoader():TextField{
			return _txtLoader;
		}
		
		//////////// Public functions ////////////
		
		//////////// Private functions ////////////
		protected function formatPrc(prc:Number):String{
			return Math.floor(prc*100).toString()
		}
		
		//////////// Event Handlers functions ////////////
		private function firstFrameHandler(event:Event):void {
			removeEventListener(Event.ENTER_FRAME,firstFrameHandler);
			dispatchEvent(new Event(INIT_LOADER));
		}
		
		private function loadingEnterFrameHandler(event:Event):void {
			
			var bytesLoaded:int = loaderInfo.bytesLoaded;
			var bytesTotal:int = loaderInfo.bytesTotal;
			if((initBulkLoader.isRunning || initBulkLoader.isFinished)){
				if(initBulkLoader.bytesTotal > 0){
					bytesLoaded += initBulkLoader.bytesLoaded;
					bytesTotal += initBulkLoader.bytesTotal;
				}else if(initBulkLoader.loadedRatio > 0){
					bytesLoaded += estimatedSize*initBulkLoader.loadedRatio;
					bytesTotal += estimatedSize;
				}
			}
			if(bytesTotal-scriptStartBytes > 0){
				_prcLoaded = (bytesLoaded-scriptStartBytes)/(bytesTotal-scriptStartBytes);
			}else{
				_prcLoaded = 1;
			}
			if(!initBulkLoader.isFinished && initBulkLoader.itemsTotal>0){
				_prcLoaded = Math.min(0.99,_prcLoaded);
			}
			if(mcLoader != null){
				mcLoader.gotoAndStop(Math.floor(_prcLoaded/min_prc*mcLoader.totalFrames)+1);
				if(mcLoader.hasOwnProperty('txt_prc')){
					mcLoader.txt_prc.text = formatPrc(_prcLoaded);
				}
				if(mcLoader.hasOwnProperty('clip_prc') && mcLoader.clip_prc.hasOwnProperty('txt_prc')){
					mcLoader.clip_prc.txt_prc.text = formatPrc(_prcLoaded);
				}
			}
			if(txtLoader != null){
				txtLoader.text = formatPrc(_prcLoaded);
			}
			if(_prcLoaded>=min_prc){
				removeEventListener(Event.ENTER_FRAME, loadingEnterFrameHandler);
				removeEventListener(BulkLoader.COMPLETE, loadingEnterFrameHandler);
				if(loadedGoto !== ""){
					if(playAtLoaded){
						gotoAndPlay(loadedGoto);
					}else{
						gotoAndStop(loadedGoto);
					}
				}
				dispatchEvent(new Event(DOC_LOADED));
			}
		}
		
		
	}
}