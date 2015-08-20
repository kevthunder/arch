package com.flashkev.customList {
	import flash.display.DisplayObject;
	import flash.events.Event;
	import flash.utils.getDefinitionByName;
	
	import fl.core.UIComponent;
	
	import com.flashkev.test.UIComponent;
	import fl.core.InvalidationType;
	import fl.controls.listClasses.CellRenderer;
	import fl.controls.listClasses.ListData;
	import fl.controls.listClasses.TileListData;
	
	import com.flashkev.viewer.Viewer;
	
	import com.flashkev.state.State;
	import com.flashkev.state.StateGroup
	import com.flashkev.state.CompoundState;
	import com.flashkev.state.IStated;
	import com.flashkev.state.StateEvent;
	
	public class CustomImageCell extends CellRenderer implements IAutoSizeCell, IStated{
		//////////// variables ////////////
		public var viewer:Viewer;
		public var foreground:DisplayObject;
		
		private var _minWidth:Number = 1;
		private var _minHeight:Number = 1;
		private var _maxWidth:Number = 4096;
		private var _maxHeight:Number = 4096;
		private var _source:Object = null;
		
		private static var defaultStyles:Object = {
			"autoSize":AUTO_BOTH,
			"mode":Viewer.MODE_RESIZE
		};
		
		protected static const VIEWER_STYLES:Object = {
				"margins":"margins",
				"mode":"mode",
				"allowStretch":"allowStretch",
				"loaderSkin":"loaderSkin",
				"maskSkin":"maskSkin",
				"fadeIn":"fadeIn",
				"fadeOut":"fadeOut",
				"videoPlayer":"videoPlayer",
				"videoPlayerOptions":"videoPlayerOptions"
			};
			
		protected var _states:StateGroup = new StateGroup([
				new CompoundState(
					new State("disabled","enabled",{"true":"","false":"disabled"}),
					new State("mouse","curMouseState")
					),
				new State("selected","selected")
			]);
			
		public static const AUTO_HORIZONTAL:String = 'horizontal';
		public static const AUTO_VERTICAL:String = 'vertical';
		public static const AUTO_BOTH:String = 'both';
		public static const AUTO_NONE:String = 'none';
		//////////// Constructor ////////////
        public function CustomImageCell() {
			_states.owner = this;
			_states.addEventListener(StateEvent.STATE_CHANGE,stateChangeHandler);
        }
		//////////// Properties functions ////////////
		public function get minWidth():Number{
			return _minWidth;
		}
		public function set minWidth(val:Number):void{
			if(_minWidth != val){
				_minWidth = val;
				invalidate(InvalidationType.SIZE);
			}
		}
		
		public function get minHeight():Number{
			return _minHeight;
		}
		public function set minHeight(val:Number):void{
			if(_minHeight != val){
				_minHeight = val;
				invalidate(InvalidationType.SIZE);
			}
		}
		
		public function get maxWidth():Number{
			return _maxWidth;
		}
		public function set maxWidth(val:Number):void{
			if(_maxWidth != val){
				_maxWidth = val;
				invalidate(InvalidationType.SIZE);
			}
		}
		
		public function get maxHeight():Number{
			return _maxHeight;
		}
		public function set maxHeight(val:Number):void{
			if(_maxHeight != val){
				_maxHeight = val;
				invalidate(InvalidationType.SIZE);
			}
		}
		
		override public function set listData(value:ListData):void {
			super.listData = value
			source = (value as TileListData).source;
		}
		
		public function get source():Object{
			return _source;
		}
		public function set source(val:Object){
			if(_source != val){
				_source = val;
				viewer.display(_source);
			}
		}
		
		public function get curMouseState():String{
			return mouseState;
		}
		
		public function get states():StateGroup{
			return _states;
		}
		
		//////////// Public functions ////////////
		public static function getStyleDefinition():Object { 
			return UIComponent.mergeStyles(CellRenderer.getStyleDefinition(), defaultStyles);
		}
		
		public function setStateVal(stateName:String,val:Object):Boolean{
			var found = _states.setStateVal(stateName,val)
			if(found){
				invalidate(InvalidationType.STATE);
			}
			return found;
		}
		
		public function getStateVal(stateName:String):Object{
			return _states.getStateVal(stateName);
		}
		
		//////////// Private functions ////////////
		override protected function configUI():void {
			super.configUI();
			viewer = new Viewer();
			viewer.addEventListener(Viewer.CONTENT_LOADED,contentLoadedHandler)
			addChild(viewer);
		}
		
		override protected function draw():void {
			if (isInvalid(InvalidationType.STYLES,InvalidationType.STATE)) {
				drawForeground();
			}
			super.draw();
		}
		
		protected function getStateStrings(StyleName:String):Array{
			return _states.getStrings(StyleName);
		}
		protected function getStateStyle(StyleName:String,excludeEmpty:Boolean=false,validateClass:Class=null):Object{
			var strings:Array = getStateStrings(StyleName);
			var i:int = strings.length-1;
			var style:Object;
			while(style === null && i>=0){
				style = getStyleValue(strings[i]);
				if(excludeEmpty && !style){
					style = null;
				}else if(validateClass){
					style = style as validateClass;
				}
				i--;
			}
			
			return style;
		}
		
		protected function loadSkin(skin:Object,oldSkin:DisplayObject = null,pos:int = int.MAX_VALUE):DisplayObject{
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
		
		protected function drawForeground(){
			foreground = loadSkin(getStateStyle("foregroundSkin"),foreground);
		}
		
		override protected function drawLayout():void {
			updateSize();
			
			super.drawLayout();
			copyStylesToChild(viewer, VIEWER_STYLES);
			viewer.width = width;
			viewer.height = height;
			viewer.drawNow();
			
			if(foreground){
				foreground.width = width;
				foreground.height = height;
			}
		}
		
		protected function updateSize():void {
			var H:Number = height;
			var W:Number = width;
			var autoSize = String(getStyleValue("autoSize"));
			if(autoSize == AUTO_HORIZONTAL || autoSize == AUTO_BOTH){
				W = viewer.contentOriginWidth;
				W = Math.max(_minWidth,Math.min(_maxWidth,W));
			} 
			if(autoSize == AUTO_VERTICAL || autoSize == AUTO_BOTH){
				H = viewer.contentOriginHeight;
				H = Math.max(_minHeight,Math.min(_maxHeight,H));
			}
			if(W != width || H != height){
				height = H;
				width = W;
				listData.owner.invalidate(InvalidationType.SIZE);
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
		private function contentLoadedHandler(e:Event){
			invalidate(InvalidationType.SIZE);
			updateSize();
		}
		
		protected function stateChangeHandler(e:StateEvent){
			invalidate(InvalidationType.STATE);
			dispatchEvent(new StateEvent(StateEvent.STATE_CHANGE,e.eventName));
		}
		
	}
}