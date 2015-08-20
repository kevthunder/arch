//v0.1.3.0
package com.flashkev.viewer{
	import flash.events.Event;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.display.DisplayObject;
	import flash.display.Bitmap;
	import flash.display.BitmapData;
	import flash.geom.Point;
	import flash.net.URLRequest;
	import flash.utils.getDefinitionByName;
	
	import fl.core.UIComponent;
	//import com.flashkev.test.UIComponent;
	import fl.core.InvalidationType;
	//import fl.video.FLVPlayback;
	
	import br.com.stimuli.loading.BulkLoader;
	import br.com.stimuli.loading.loadingtypes.LoadingItem;
	
	import caurina.transitions.Tweener;
	
	import com.flashkev.utils.Margins

	public class Viewer extends UIComponent{
		//////////// variables ////////////
		private var request:URLRequest;
		private var container:Sprite;
		private var containerMask:DisplayObject;
		private var loader:MovieClip;
		private var background:MovieClip;
		private var foreground:MovieClip;
		private var oldContent:DisplayObject;
		
		private var _contentOriginSize:Point;
		private var _type:String;
		private var _content:DisplayObject;
		private var _isLoading:Boolean;
		private var _contentScaleX:Number;
		private var _contentScaleY:Number;
		private var _bulkLoader:BulkLoader;
		private var contentW:Number;
		private var contentH:Number;
		
		public static var defaultBulkLoader:BulkLoader;
		
		private static var defaultStyles:Object = {
				margins:null,
				autoSize:AUTO_NONE,
				mode:MODE_CROP,
				align:ALIGN_CENTER,
				vAlign:ALIGN_CENTER,
				allowStretch:true,
				maskSkin:Square,
				backgroundSkin:null,
				foregroundSkin:null,
				loaderSkin:null,
				fadeIn:{init:{alpha:0},alpha:1,time:1},
				fadeOut:null,
				videoPlayer:null/*FLVPlayback*/,
				videoPlayerOptions:null
			};
			
		
		public static const CONTENT_LOADED:String = "img loaded";
		
		public static const MODE_CROP:String = "crop";
		public static const MODE_RESIZE:String = "resize";
		
		public static const AUTO_HORIZONTAL:String = 'horizontal';
		public static const AUTO_VERTICAL:String = 'vertical';
		public static const AUTO_BOTH:String = 'both';
		public static const AUTO_NONE:String = 'none';
		
		public static const ALIGN_TOP:String = 'top';
		public static const ALIGN_LEFT:String = 'left';
		public static const ALIGN_CENTER:String = 'center';
		public static const ALIGN_BOTTOM:String = 'bottom';
		public static const ALIGN_RIGHT:String = 'right';
		
		public static const TYPE_IMAGE:String = "img";
		public static const TYPE_SWF:String = "swf";
		public static const TYPE_VIDEO:String = "video";
		public static const TYPE_AUDIO:String = "audio";
		public static const TYPES_PARAM:Object = {
			"img":{
				"extensions":["jpg", "jpeg", "gif", "png"]
			},
			"swf":{
				"extensions":['swf']
			},
			"video":{
				"extensions":["flv", "f4v", "f4p", "mp4"]
			},
			"audio":{
				"extensions":["mp3", "f4a", "f4b"]
			}
		}
		//////////// Constructor ////////////
        public function Viewer() {
			if(defaultBulkLoader){
				bulkLoader = defaultBulkLoader;
			}else{
				bulkLoader = BulkLoader.getLoader("ImgViewerLoader");
				if(!bulkLoader){
					bulkLoader = new BulkLoader("ImgViewerLoader");
				}
				bulkLoader.start();
			}
        }
		//////////// Properties functions ////////////
		public function get bulkLoader():BulkLoader{
			return _bulkLoader;
		}
		public function set bulkLoader(val:BulkLoader){
			_bulkLoader = val;
		}
		
		public function get content():DisplayObject{
			return _content;
		}
		public function get contentOriginWidth():Number{
			if(_contentOriginSize){
				return _contentOriginSize.x;
			}else{
				return 0;
			}
		}
		public function get contentOriginHeight():Number{
			if(_contentOriginSize){
				return _contentOriginSize.y;
			}else{
				return 0;
			}
		}
		
		public function get isLoading():Boolean{
			return _isLoading;
		}
		
		public function get type():String{
			return _type;
		}
		
		
		public function get contentScaleX():Number{
			return _contentScaleX;
		}
		public function set contentScaleX(val:Number){
			if(_contentScaleX != val){
				_contentScaleX = val;
				invalidate(InvalidationType.SIZE);
			}
		}
		
		public function get contentScaleY():Number{
			return _contentScaleY;
		}
		public function set contentScaleY(val:Number){
			if(_contentScaleY != val){
				_contentScaleY = val;
				invalidate(InvalidationType.SIZE);
			}
		}
		
		//////////// Public functions ////////////
		public static function getStyleDefinition():Object { 
			var test = null
			//trace('getStyleDefinition',test.wtf);
			return UIComponent.mergeStyles(defaultStyles, UIComponent.getStyleDefinition());
		}
		
		public function display(source:*,type:String = null){
			if(source is String){
				displayUrl(source,type);
			}else if(source is URLRequest){
				displayRequest(source);
			}else if(source is BitmapData){
				displayBitmapData(source);
			}else if(source is DisplayObject){
				displayDisplayObj(source);
			}
		}
		
		public function displayUrl(source:String,type:String = null){
			if(!type){
				type = getType(source);
			}
			if(type == TYPE_VIDEO){
				displayVideo(source);
			}else{
				displayRequest(new URLRequest(source),type);
			}
		}
		public function displayRequest(source:URLRequest,type:String = null){
			request = source;
			_type = type;
			var loadingItem:LoadingItem = bulkLoader.get(source);
			if(loadingItem){
				if(loadingItem.isLoaded){
					_isLoading = false;
					if(!type){
						if(loadingItem.content is Bitmap){
							_type = TYPE_IMAGE;
						}else if(loadingItem.content is DisplayObject){
							_type = TYPE_SWF;
						}
					}
					if(loadingItem.content is DisplayObject){
						if(_type == TYPE_IMAGE){
							var bitmap:Bitmap = loadingItem.content as Bitmap;
							displayBitmapData(bitmap.bitmapData);
						}else{
							displayDisplayObj(loadingItem.content,_type);
						}
					}
				}else{
					_isLoading = true;
					loadingItem.removeEventListener(BulkLoader.COMPLETE, contentLoaded);
					loadingItem.addEventListener(BulkLoader.COMPLETE, contentLoaded);
				}
			}else{
				_isLoading = true;
				bulkLoader.add(source);
				bulkLoader.get(source).addEventListener(BulkLoader.COMPLETE, contentLoaded);
			}
			invalidate(InvalidationType.DATA);
		}
		public function displayBitmapData(source:BitmapData){
			_type = TYPE_IMAGE;
			var bitmapContent:Bitmap = _content as Bitmap
			if(!bitmapContent || bitmapContent.bitmapData != source){
				var bitmap:Bitmap = new Bitmap(source);
				bitmap.smoothing = true;
				displayDisplayObj(bitmap,_type);
			}
		}
		public function displayDisplayObj(source:DisplayObject,type:String = null){
			if(!_content || _content != source){
				if(!type){
					type = TYPE_SWF;
				}
				_type = type;
				_isLoading = false;
				oldContent = _content;
				_content = source;
				
				startFade();
				_contentOriginSize = new Point(source.width,source.height);
				
				
				invalidate(InvalidationType.DATA);
				dispatchEvent(new Event(CONTENT_LOADED));
			}
		}
		
		public function displayVideo(source:String){
			_type = TYPE_VIDEO;
			_content =  getAvailableDisplayObjectInstance(getStyleValue("videoPlayer"),[_content]) as DisplayObject;
			var options:Object = getStyleValue("videoPlayerOptions");
			for(var opt:String in options){
				if(_content.hasOwnProperty(opt)){
					_content[opt] = options[opt];
				}
			}
			container.addChild(_content);
			//_content.opaqueBackground = 0x000000;
			_content["source"] = source;
			
			invalidate(InvalidationType.DATA);
			dispatchEvent(new Event(CONTENT_LOADED));
		}
		
		public function getType(url:String):String{
			var q:RegExp = /^[^?]+\.([a-z0-9]+)(?:\?.+)?/i
			var res = q.exec(url);
			for(var type:String in TYPES_PARAM){
				if(TYPES_PARAM[type].extensions.indexOf(res[1]) != -1){
					return type;
				}
			}
			return null;
		}
		
		public function clear(){
			_type = null;
			_isLoading = false;
			oldContent = _content;
			_content = null;
			
			startFade();
		}
		
		//////////// Private functions ////////////
		override protected function configUI():void {
			super.configUI();
			
			container = new Sprite();
			addChild(container);
		}
		protected function startFade(){
			var fadeIn:Object = getStyleValue('fadeIn') as Object;
			var fadeOut:Object = getStyleValue('fadeOut') as Object;
			if(_content != null){
				if(fadeOut){
					container.addChildAt(_content,0);
				}else{
					container.addChild(_content);
				}
				if(fadeIn){
					attachFade(_content,fadeIn,!fadeOut);
				}
			}
			if(oldContent!= null){
				if(fadeOut){
					attachFade(oldContent,fadeOut,true);
				}else if(!fadeIn || !_content){
					container.removeChild(oldContent);
				}
			}
		}
		protected function attachFade(img:DisplayObject,params:Object,removeOld:Boolean=false){
			var prop:String;
			if(params.hasOwnProperty("init")){
				for(prop in params.init){
					if(img.hasOwnProperty(prop)){
						img[prop] = params.init[prop];
					}
				}
			}
			var tweenParams:Object = new Object();
			for(prop in params){
				if(prop != "init"){
					tweenParams[prop] = params[prop];
				}
			}
			var imgToRemove:DisplayObject = oldContent;
			tweenParams.onComplete = function(){
				if(imgToRemove && container.contains(imgToRemove)){
					container.removeChild(imgToRemove)
				}
			};
			Tweener.addTween(img,tweenParams);
		}
		override protected function draw():void {
			if (isInvalid(InvalidationType.SIZE,InvalidationType.STYLES,InvalidationType.DATA)) {
				drawMask();
				drawBackground();
				drawForeground();
				var size:Point = getContentSize();
				contentW = size.x;
				contentH = size.y;
				resizeContainer();
				drawImg();
				drawLoader();
			}
			
			super.draw();
		}
		
		protected function loadSkin(skin:Object,oldSkin:DisplayObject = null,pos:int = int.MAX_VALUE){
			var newSkin:DisplayObject = getAvailableDisplayObjectInstance(skin,[oldSkin]) as DisplayObject;
			if (oldSkin != newSkin) {
				if(newSkin != null){
					pos = Math.min(pos,numChildren);
					addChildAt(newSkin,pos);
				}
				if(oldSkin != null){
					removeChild(oldSkin); 
				}
			}
			return newSkin;
		}
		
		protected function drawMask(){
			var oldMask:DisplayObject = containerMask;
			containerMask = getAvailableDisplayObjectInstance(getStyleValue("maskSkin"),[oldMask]) as DisplayObject;
			if (oldMask != containerMask) {
				if(containerMask != null){
					addChild(containerMask);
					container.mask = containerMask;
				}
				if(oldMask != null){
					removeChild(oldMask); 
				}
			}
		}
		
		protected function drawBackground(){
			background = loadSkin(getStyleValue("backgroundSkin"),background,0);
		}
		
		protected function drawForeground(){
			foreground = loadSkin(getStyleValue("foregroundSkin"),foreground);
		}
		protected function drawLoader(){
			var oldLoader:MovieClip = loader;
			if(_isLoading){
				loader = getAvailableDisplayObjectInstance(getStyleValue("loaderSkin"),[loader]) as MovieClip;
			}else{
				loader = null;
			}
			if (oldLoader != loader) {
				if(loader != null){
					addChild(loader);
				}
				if (oldLoader != null){
					removeChild(oldLoader); 
				}
			}
			if(loader != null){
				loader.x = width/2;
				loader.y = height/2;
			}
		}
		
		protected function getContentSize(){
			var contentW:Number = 0;
			var contentH:Number = 0;
			var contentMargins:Margins = getStyleValue("margins") as Margins;
			if(!contentMargins){
				contentMargins = new Margins(0,0,0,0);
			}
			var autoSize:String = getStyleValue('autoSize').toString();
			var allowStretch:String = getStyleValue('allowStretch').toString();
			var w:Number = width - contentMargins.left - contentMargins.right;
			var h:Number = height - contentMargins.top - contentMargins.bottom;
			contentW = w;
			contentH = h;
			if(_content!= null){
				if(!allowStretch){
					contentW = contentOriginWidth;
					contentH = contentOriginHeight;
				}else{
					var mode:String = getStyleValue('mode').toString();
					var contentRatio = _content.width/_content.height;
					if(isNaN(contentScaleX) && isNaN(contentScaleY)){
						if(autoSize == AUTO_BOTH){
							contentW = contentOriginWidth;
							contentH = contentOriginHeight;
						}else if(autoSize == AUTO_HORIZONTAL){
							contentH = h;
							contentW = contentH*contentRatio;
						}else{
							contentW = w;
							contentH = contentW/contentRatio;
						}
					}else if(isNaN(contentScaleX) || (!isNaN(contentScaleY) && mode == MODE_RESIZE && autoSize ==  AUTO_VERTICAL)){
						contentH = contentOriginHeight*contentScaleY;
						if(mode == MODE_RESIZE && autoSize == AUTO_HORIZONTAL){
							contentH = Math.min(contentH,h);
						}
						contentW = contentH*contentRatio;
					}else if(isNaN(contentScaleY) || (mode == MODE_RESIZE && autoSize ==  AUTO_VERTICAL)){
						contentW = contentOriginWidth*contentScaleX;
						if(mode == MODE_RESIZE && autoSize == AUTO_VERTICAL){
							contentW = Math.min(contentW,w);
						}
						contentH = contentW/contentRatio;
					}else{
						contentW = contentOriginWidth*contentScaleX;
						contentH = contentOriginHeight*contentScaleY;
					}
				}
			}
			return new Point(contentW,contentH);
		}
		
		protected function resizeContainer(){
			var contentMargins:Margins = getStyleValue("margins") as Margins;
			if(!contentMargins){
				contentMargins = new Margins(0,0,0,0);
			}
			var autoSize:String = getStyleValue('autoSize').toString();
			var w:Number = width - contentMargins.left - contentMargins.right;
			var h:Number = height - contentMargins.top - contentMargins.bottom;
			if(autoSize != AUTO_NONE){
				if(autoSize == AUTO_HORIZONTAL || autoSize == AUTO_BOTH){
					w = contentW + contentMargins.left + contentMargins.right;
				} 
				if(autoSize == AUTO_VERTICAL || autoSize == AUTO_BOTH){
					h = contentH + contentMargins.top + contentMargins.bottom;
				}
				width = w + contentMargins.left + contentMargins.right;
				height = h + contentMargins.top + contentMargins.bottom;
			}
			if(containerMask){
				containerMask.x = contentMargins.left;
				containerMask.y = contentMargins.top;
				containerMask.width = w;
				containerMask.height = h;
			}
			if(background){
				background.width = width;
				background.height = height;
			}
			if(foreground){
				foreground.width = width;
				foreground.height = height;
			}
		}
		protected function drawImg(){
			if(_content!= null){
				if(_type == TYPE_IMAGE || _type == TYPE_SWF){
					if(!getStyleValue('allowStretch')){
						_content.scaleX = 1
						_content.scaleY = 1
					}else{
						var mode:String = getStyleValue('mode').toString();
						var fixedScale:Boolean = !isNaN(contentScaleX) || !isNaN(contentScaleX);
						var tooBig:Boolean = contentW>containerMask.width || contentH>containerMask.height;
						if(!fixedScale || (mode == MODE_RESIZE && tooBig)){
							var imgRatio = contentW/contentH;
							var ratio = containerMask.width/containerMask.height;
							if((ratio > imgRatio) == (mode != MODE_RESIZE)){
								_content.width = containerMask.width;
								_content.height = _content.width/imgRatio;
							}else{
								_content.height = containerMask.height;
								_content.width = _content.height*imgRatio;
							}
						}else{
							_content.width = contentW;
							_content.height = contentH;
						}
					}
				}else{
					_content.height = containerMask.height;
					_content.width = containerMask.width;
				}
				
				var align:String = String(getStyleValue("align"));
				var vAlign:String = String(getStyleValue("vAlign"));
				
				switch(align){
					case ALIGN_LEFT :
						_content.x = containerMask.x
						break;
					case ALIGN_RIGHT :
						_content.x = containerMask.width-_content.width + containerMask.x
						break;
					case ALIGN_CENTER :
					default :
						_content.x = (containerMask.width-_content.width)/2 + containerMask.x
						break;
				}
				
				switch(vAlign){
					case ALIGN_TOP :
						_content.y = containerMask.y
						break;
					case ALIGN_BOTTOM :
						_content.y = containerMask.height-_content.height + containerMask.y
						break;
					case ALIGN_CENTER :
					default :
						_content.y = (containerMask.height-_content.height)/2 + containerMask.y
						break;
				}
			}
		}
		
		protected function getAvailableDisplayObjectInstance(skin:Object,availableObjects:Array=null,elseNew:Boolean=true):DisplayObject {
			var classDef:Class = null;
			var found:DisplayObject = null;
			if (skin is Class) { 
				classDef = skin as Class; 
			} else if (skin is DisplayObject) {
				(skin as DisplayObject).x = 0;
				(skin as DisplayObject).y = 0;
				return skin as DisplayObject;
			}
			
			try {
				classDef = getDefinitionByName(skin.toString()) as Class;
			} catch(e:Error) {
				try {
					classDef = loaderInfo.applicationDomain.getDefinition(skin.toString()) as Class;
				} catch (e:Error) {
					// Nothing
				}
			}
			
			if (classDef == null) {
				return null;
			}
			
			if(availableObjects){
				for(var i:int = 0;i<availableObjects.length && found == null;i++){
					if(availableObjects[i] is classDef){
						found = availableObjects[i] as DisplayObject;
					}
				}
			}
			
			if(found){
				return found;
			}else if(elseNew){
				return (new classDef()) as DisplayObject;
			}else{
				return null;
			}
			
		}
		
		//////////// Event Handlers functions ////////////
		private function contentLoaded(e:Event) : void{
			displayRequest(request,_type);
		}
		
		
	}
}