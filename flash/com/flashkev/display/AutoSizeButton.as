package com.flashkev.display {
	import flash.display.DisplayObject;
	import flash.display.Sprite;
	import flash.utils.getDefinitionByName;
	import flash.text.TextField;
	import flash.text.TextFieldType;
	import flash.text.TextFormat;
	import flash.text.AntiAliasType;
	import flash.text.GridFitType;
	import flash.geom.Point;
	import flash.events.Event;
	import flash.events.KeyboardEvent;
	import flash.events.MouseEvent;
	import flash.ui.Keyboard;
	
	import fl.core.UIComponent;
	import fl.core.InvalidationType;
	import fl.controls.BaseButton;	
	import fl.controls.ButtonLabelPlacement;
	import fl.events.ComponentEvent;

	import com.flashkev.state.State;
	import com.flashkev.state.StateGroup
	import com.flashkev.state.CompoundState;
	import com.flashkev.state.IStated;
	import com.flashkev.state.StateEvent;
	import com.flashkev.utils.Margins;
	
	
	[Event(name="click", type="flash.events.MouseEvent")]
	[Event(name="labelChange", type="fl.events.ComponentEvent")]
	
    [Style(name="disabledSkin", type="Class")]
	[Style(name="upSkin", type="Class")]
	[Style(name="downSkin", type="Class")]
 	[Style(name="overSkin", type="Class")]
	[Style(name="selectedDisabledSkin", type="Class")]
	[Style(name="selectedUpSkin", type="Class")]
	[Style(name="selectedDownSkin", type="Class")]
	[Style(name="selectedOverSkin", type="Class")]
	[Style(name="textPadding", type="Number", format="Length")]
    [Style(name="repeatDelay", type="Number", format="Time")]
    [Style(name="repeatInterval", type="Number", format="Time")]
    [Style(name="icon", type="Class")]
    [Style(name="upIcon", type="Class")]
    [Style(name="downIcon", type="Class")]
    [Style(name="overIcon", type="Class")]
    [Style(name="disabledIcon", type="Class")]
    [Style(name="selectedDisabledIcon", type="Class")]
    [Style(name="selectedUpIcon", type="Class")]
    [Style(name="selectedDownIcon", type="Class")]
    [Style(name="selectedOverIcon", type="Class")]
	[Style(name="embedFonts", type="Boolean")]
	
	[Style(name="autoSize", type="string")]
	[Style(name="textPaddingH", type="number")]
	public class AutoSizeButton extends BaseButton implements IStated{
		//////////// variables ////////////
		
		
		private static var defaultStyles:Object = {
			icon:null,
		  	upIcon:null,downIcon:null,overIcon:null,disabledIcon:null,
		  	selectedDisabledIcon:null,selectedUpIcon:null,selectedDownIcon:null,selectedOverIcon:null,
		  	textFormat:null, disabledTextFormat:null,
		  	textPadding:5, embedFonts:false,
			"autoSize":AUTO_BOTH,
			"margins":null,
			"textPaddingH":1,
			"textAlign":ALIGN_LEFT,
			"textVAlign":ALIGN_CENTER,
			"multiline":true,
			"wordWrap":true,
			"iconAlign":ICONALIGN_MIDDLE,
			"inmarginIcon":false
		};

		public var textField:TextField;
		protected var _labelPlacement:String = ButtonLabelPlacement.RIGHT;
		protected var _toggle:Boolean = false;
		protected var icon:DisplayObject;
		protected var oldMouseState:String;
		protected var _label:String = "Label";
		protected var _states:StateGroup;
		protected var _minWidth:Number = 1;
		protected var _minHeight:Number = 1;
		protected var _maxWidth:Number = 4096;
		protected var _maxHeight:Number = 4096;
		
		protected var txtMargins:Margins;
		
		public static const AUTO_HORIZONTAL:String = 'horizontal';
		public static const AUTO_VERTICAL:String = 'vertical';
		public static const AUTO_BOTH:String = 'both';
		public static const AUTO_NONE:String = 'none';
		
		public static const ALIGN_TOP:String = 'top';
		public static const ALIGN_LEFT:String = 'left';
		public static const ALIGN_CENTER:String = 'center';
		public static const ALIGN_BOTTOM:String = 'bottom';
		public static const ALIGN_RIGHT:String = 'right';
		
		public static const ICONALIGN_TOP:String = 'top';
		public static const ICONALIGN_MIDDLE:String = 'middle';
		public static const ICONALIGN_BOTTOM:String = 'bottom';
		public static const ICONALIGN_BASELINE:String = 'baseline';
		//////////// Constructor ////////////
        public function AutoSizeButton() {
			_states = StateGroup.newButtonStates(this);
        }
		//////////// Properties functions ////////////
		[Inspectable(defaultValue="Label")]
		public function get label():String {
			return _label;
		}
		public function set label(value:String):void {
			_label = value;
			if (textField.text != _label) {
				textField.text = _label;
				dispatchEvent(new ComponentEvent(ComponentEvent.LABEL_CHANGE));
				invalidate(InvalidationType.SIZE);
				invalidate(InvalidationType.STYLES);
				//setWidth();
			}
		}	
		
		[Inspectable(enumeration="left,right,top,bottom", defaultValue="right", name="labelPlacement")]       
		public function get labelPlacement():String {
			return _labelPlacement;
		}
		public function set labelPlacement(value:String):void {
			_labelPlacement = value;
			invalidate(InvalidationType.SIZE);
		}	
		
		[Inspectable(defaultValue=false)]
		public function get toggle():Boolean {
			return _toggle;
		}
		public function set toggle(value:Boolean):void {
			if (!value && super.selected) { selected = false; }
			_toggle = value;
			if (_toggle) { addEventListener(MouseEvent.CLICK,toggleSelected,false,0,true); }
			else { removeEventListener(MouseEvent.CLICK,toggleSelected); }
			invalidate(InvalidationType.STATE);
		}
		
		[Inspectable(defaultValue=false)]
		override public function get selected():Boolean {
			return (_toggle) ? _selected : false;
		}
		override public function set selected(value:Boolean):void {
			_selected = value;
			if (_toggle) {
				invalidate(InvalidationType.STATE);
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
		
		public function get curMouseState():String{
			return mouseState;
		}
		
		public function get states():StateGroup{
			return _states;
		}
		
		//////////// Public functions ////////////
		public static function getStyleDefinition():Object { 
			return UIComponent.mergeStyles(BaseButton.getStyleDefinition(), defaultStyles);
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
			txtMargins = calculMargins();
			updateSize();
			positionIcon();
			positionLabel();
			super.drawLayout();
		}
		
		protected function calculMargins():Margins {
			var txtPad:Number = Number(getStyleValue("textPadding"));
			var txtPadH:Number = Number(getStyleValue("textPaddingH"));
			var txtMargins:Margins = (getStyleValue("margins") as Margins).clone();
			if(!txtMargins){
				txtMargins = new Margins(txtPadH,txtPad,txtPadH,txtPad);
			}
			if (icon != null) {
				var inmarginIcon:Boolean = Boolean(getStyleValue("inmarginIcon"));
				if(!inmarginIcon){
					switch(_labelPlacement){
						case ButtonLabelPlacement.BOTTOM :
							txtMargins.top += icon.height + txtPadH;
							break;
						case ButtonLabelPlacement.LEFT :
							txtMargins.right += icon.width + txtPad;
							break;
						case ButtonLabelPlacement.TOP :
							txtMargins.bottom += icon.height + txtPadH;
							break;
						case ButtonLabelPlacement.RIGHT :
						default:
							txtMargins.left += icon.width + txtPad;
							break;
					}
				}
			}
			return txtMargins;
		}
		protected function toggleSelected(event:MouseEvent):void {
			selected = !selected;
			dispatchEvent(new Event(Event.CHANGE, true));
		}
		
		protected function updateSize():void {
			textField.multiline = Boolean(getStyleValue("multiline"));
			textField.wordWrap = false;
			var autoSize = String(getStyleValue("autoSize"));
			if(autoSize == AUTO_HORIZONTAL || autoSize == AUTO_BOTH){
				var txtW:Number = textField.textWidth+5;
				width = Math.max(_minWidth,Math.min(_maxWidth,txtW + txtMargins.left + txtMargins.right));
			} 
			textField.wordWrap = Boolean(getStyleValue("wordWrap")); 
			if(autoSize == AUTO_VERTICAL || autoSize == AUTO_BOTH){
				textField.width = width - txtMargins.left - txtMargins.right;
				var txtH:Number = textField.textHeight+5;
				height = Math.max(_minHeight,Math.min(_maxHeight,txtH + txtMargins.top + txtMargins.bottom));
			}
			
		}

		override protected function configUI():void {
			super.configUI();
			
			textField = new TextField();
			textField.type = TextFieldType.DYNAMIC;
			textField.selectable = false;
			textField.antiAliasType=AntiAliasType.ADVANCED;
            textField.gridFitType=GridFitType.PIXEL;
            textField.sharpness=-200;
			addChild(textField);
		}
		
		override protected function draw():void {
			if (textField.text != _label) { 
				label = _label;
			}
			
			if (isInvalid(InvalidationType.STYLES,InvalidationType.STATE)) {
				drawBackground();
				drawIcon();
				drawTextFormat();
				
				invalidate(InvalidationType.SIZE,false);
			}
			if (isInvalid(InvalidationType.SIZE)) {
				drawLayout();
			}
			if (isInvalid(InvalidationType.SIZE,InvalidationType.STYLES)) {
				if (isFocused && focusManager.showFocusIndicator) { drawFocus(true); }
			}
			validate(); // because we're not calling super.draw
		}
		
		protected function drawIcon():void {			
			icon = loadSkin(getStateStyle("icon",true),icon);
		}
		
		
		protected function positionIcon(){
			if (icon != null) {
				var txtPad:Number = Number(getStyleValue("textPadding"));
				var txtPadH:Number = Number(getStyleValue("textPaddingH"));
				var iconAlign:String = getStyleValue("iconAlign").toString();
				switch(iconAlign){
					case ICONALIGN_TOP :
						icon.x = txtPad;
						icon.y = txtPadH;
						break;
					case ICONALIGN_BOTTOM :
						break;
						icon.x = width-icon.width-txtPad;
						icon.y = height-icon.height-txtPadH;
					case ICONALIGN_BASELINE :
						icon.x = Math.round((width-icon.width)/2);
						icon.y = textField.getLineMetrics(0).ascent+3-icon.height+txtPadH;
						break;
					case ICONALIGN_MIDDLE :
					default :
						icon.x = Math.round((width-icon.width)/2);
						icon.y = Math.round((height-icon.height)/2);
						break;
				}
				var placement:String = (icon == null) ? ButtonLabelPlacement.TOP : _labelPlacement;
				switch(placement){
					case ButtonLabelPlacement.BOTTOM :
						icon.y = Math.round(txtMargins.top-icon.height-txtPadH);
						break;
					case ButtonLabelPlacement.TOP :
						break;
						icon.y = Math.round(height-txtMargins.bottom+txtPadH);
					case ButtonLabelPlacement.LEFT :
						icon.x = Math.round(width-txtMargins.right+txtPad);
						break;
					case ButtonLabelPlacement.RIGHT :
					default :
						icon.x = Math.round(txtMargins.left-icon.width-txtPad);
						break;
				}
				
			}
		}
		protected function positionLabel():void {
			if(label.length > 0){
				var textAlign:String = String(getStyleValue("textAlign"));
				var textVAlign:String = String(getStyleValue("textVAlign"));
				var txtW:Number = textField.textWidth+5;
				var txtH:Number = textField.textHeight+5;
				
				textField.visible = true;
				
				switch(textAlign){
					case ALIGN_LEFT :
						textField.x = txtMargins.left;
						break;
					case ALIGN_RIGHT :
						textField.x = width - txtMargins.right - txtW;
						break;
					case ALIGN_CENTER :
					default :
						textField.x = (width - txtMargins.left - txtMargins.right - txtW)/2 + txtMargins.left;
						break;
				}
				
				switch(textVAlign){
					case ALIGN_TOP :
						textField.y = txtMargins.top;
						break;
					case ALIGN_BOTTOM :
						textField.y = height - txtMargins.bottom - txtH;
						break;
					case ALIGN_CENTER :
					default :
						textField.y = (height - txtMargins.top - txtMargins.bottom - txtH)/2 + txtMargins.top;
						break;
				}
				
				textField.width = width - txtMargins.left - txtMargins.right;
				textField.height = height - txtMargins.top - txtMargins.bottom;
			}else{
				textField.visible = false;
			}
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

		protected function drawTextFormat():void {
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
			var tf = mergeTextFormats(formats);
			
			// apply textFormats
			textField.setTextFormat(tf);
			textField.defaultTextFormat = tf;
			
			setEmbedFont();
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
		
		protected function setEmbedFont() {
			var embed:Object = getStyleValue("embedFonts");
			if (embed != null) {
				textField.embedFonts = embed;
			}	
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
		override protected function keyDownHandler(event:KeyboardEvent):void {
			if (!enabled) { return; }
			if (event.keyCode == Keyboard.SPACE) {
				if(oldMouseState == null) {
					oldMouseState = mouseState;
				}

				setMouseState("down");
				startPress();
			}
		}
		
		override protected function keyUpHandler(event:KeyboardEvent):void {
			if (!enabled) { return; }
			if (event.keyCode == Keyboard.SPACE) {
				setMouseState(oldMouseState);
				oldMouseState = null;
				endPress();
				dispatchEvent(new MouseEvent(MouseEvent.CLICK));
			}
		}
	}
}