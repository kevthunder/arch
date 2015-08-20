package com.flashkev.customList {
	import fl.controls.listClasses.CellRenderer;
	import fl.controls.listClasses.ListData;
	import flash.display.DisplayObject;
	import flash.utils.getDefinitionByName;
	import flash.text.*
	import fl.controls.*;
	import fl.core.UIComponent;
	import fl.core.InvalidationType;
	
	import com.flashkev.state.State;
	import com.flashkev.state.StateGroup
	import com.flashkev.state.CompoundState;
	import com.flashkev.state.IStated;
	import com.flashkev.state.StateEvent;
	import com.flashkev.utils.Margins;
	import flash.geom.Point;
	import flash.geom.Rectangle;
	
	[Style(name="autoSize", type="string")]
	[Style(name="textPaddingH", type="number")]
	
	public class CustomCellBase extends CellRenderer implements IAutoSizeCell, IStated{
		//////////// variables ////////////
		private static var defaultStyles:Object = {
			"autoSize":AUTO_BOTH,
			"margins":null,
			"displayField":"label"
		};
		
		protected var _minWidth:Number = 1;
		protected var _minHeight:Number = 1;
		protected var _maxWidth:Number = 4096;
		protected var _maxHeight:Number = 4096;
		
		protected var _insideRect:Rectangle;
		
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
		
		public static const ALIGN_TOP:String = 'top';
		public static const ALIGN_LEFT:String = 'left';
		public static const ALIGN_CENTER:String = 'center';
		public static const ALIGN_BOTTOM:String = 'bottom';
		public static const ALIGN_RIGHT:String = 'right';
		//////////// Constructor ////////////
        public function CustomCellBase() {
			_states.owner = this;
			_states.addEventListener(StateEvent.STATE_CHANGE,stateChangeHandler);
        }
		
		//////////// Properties functions ////////////
		override public function set listData(value:ListData):void {
			_listData = value;
			if(data.hasOwnProperty(getStyleValue("displayField"))){
				label = data[getStyleValue("displayField")];
			}else{
				label = _listData.label;
			}
			if(_listData.icon !== null){ // if null, icon wont change; if false icon will be removed
				setStyle("icon", _listData.icon);
			}
		}
		
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
		
		public function get insideRect():Rectangle{
			return _insideRect;
		}
		
		public function get curMouseState():String{
			return mouseState;
		}
		
		public function get states():StateGroup{
			return _states;
		}
		
		//////////// Public functions ////////////
		public static function getStyleDefinition():Object { 
			return UIComponent.mergeStyles(defaultStyles, CellRenderer.getStyleDefinition());
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
		override protected function drawLayout():void {
			updateContent();
			updateBackground();
		}
		
		
		protected function updateBackground():void {
			// Size background
			if(background){
				background.width = width;
				background.height = height;
				
				if(background is UIComponent){
					var uiBackground:UIComponent = background as UIComponent;
					uiBackground.drawNow();
				}
			}
		}
		
		protected function updateContent():void {
			updateSize();
		}
		protected function getAutoSize(autoSize:String,margins:Margins):Point {
			return null;
		}
		protected function updateSize():void {
			var autoSize = String(getStyleValue("autoSize"));
			var margins:Margins = getStyleValue("margins") as Margins;
			if(!margins){
				margins = new Margins(0,0,0,0);
			}
			var auto:Point = getAutoSize(autoSize,margins);
			if(!auto){
				auto = new Point(100,100);
			}
			auto = margins.addToPoint(auto);
			if(autoSize == AUTO_HORIZONTAL || autoSize == AUTO_BOTH){
				width = Math.max(_minWidth,Math.min(_maxWidth,auto.x));
			} 
			textField.wordWrap = Boolean(getStyleValue("wordWrap")); 
			if(autoSize == AUTO_VERTICAL || autoSize == AUTO_BOTH){
				height = Math.max(_minHeight,Math.min(_maxHeight,auto.y));
			}
			_insideRect = margins.addToRect(new Rectangle(0,0,width,height),true,false);
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
		
		protected function getTextFormat():TextFormat {
			// get avalable textFormats
			var strings:Array = getStateStrings("textFormat");
			var formats:Array = new Array();
			for (var i:int = strings.length-1;i>=0;i--){
				var style:Object = getStyleValue(strings[i]);
				if(style is TextFormat){
					formats.push(style);
				}
			}
			
			//default textformat
			var uiStyles:Object = UIComponent.getStyleDefinition();
			var defaultTF:TextFormat = enabled ? uiStyles.defaultTextFormat as TextFormat : uiStyles.defaultDisabledTextFormat as TextFormat;
			formats.push(defaultTF);
			
			// merge textFormats
			var tf:TextFormat = mergeTextFormats(formats);
			
			return tf;
		}
		
		protected function mergeTextFormats(formats:Array):TextFormat {
			var properties:Array = ['font', 'size', 'color', 'bold', 'italic', 'underline', 'url', 'target', 'align', 'leftMargin', 'rightMargin', 'indent', 'leading'];
			var format:TextFormat = new TextFormat();
			for(var p:int = 0; p<properties.length; p++){
				var prop:String = properties[p];
				var val = null;
				var i:int = 0;
				while(i<formats.length && val == null){
					if(formats[i] as TextFormat != null){
						val = formats[i][prop];
					}
					i++;
				}
				format[prop] = val;
			}
			return format;
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
		
		override protected function drawBackground():void {
			background = loadSkin(getStateStyle("skin",true),background,0);
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
		protected function stateChangeHandler(e:StateEvent){
			invalidate(InvalidationType.STATE);
			dispatchEvent(new StateEvent(StateEvent.STATE_CHANGE,e.eventName));
		}
	}
}