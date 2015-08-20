// Copyright 2007. Adobe Systems Incorporated. All Rights Reserved.
package com.flashkev.test {

	import flash.display.DisplayObject;
	import flash.display.DisplayObjectContainer;
	import flash.display.InteractiveObject;
	import flash.display.Sprite;
	import flash.display.Stage;
	import flash.events.Event;
	import flash.events.EventPhase;
	import flash.events.FocusEvent;
	import flash.events.KeyboardEvent;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import flash.text.TextField;
	import flash.text.TextFormat;
	import flash.text.TextFormatAlign;
	import flash.utils.Dictionary;
	import flash.utils.getDefinitionByName;
	import flash.utils.getQualifiedClassName;
	import flash.system.Capabilities;
	import flash.system.IME;
	import flash.system.IMEConversionMode;
	import flash.utils.getQualifiedClassName;
	import fl.core.InvalidationType;
	import fl.events.ComponentEvent;
	import fl.managers.FocusManager;
	import fl.managers.IFocusManager;
	import fl.managers.IFocusManagerComponent;
	//import fl.managers.StyleManager;
	
  
	public class UIComponent extends fl.core.UIComponent {

        
		public function UIComponent() {
			super();
			

			StyleManager.registerInstance(this);

		}
		public static function getStyleDefinition():Object { 
			return fl.core.UIComponent.getStyleDefinition();
		}
		public static function mergeStyles(...list:Array):Object {
			return fl.core.UIComponent.mergeStyles.call(fl.core.UIComponent,list);
		}


	}

}