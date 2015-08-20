package com.flashkev.snap{
    import flash.display.DisplayObject;
	import flash.geom.Point;
	import flash.geom.Rectangle;
	import flash.events.Event;
	
	import com.flashkev.docroot.ResizableRoot;

	public class SnapManager extends Object{
		//////////// variables ////////////
		public var snapRect:Rectangle;
		public var originRect:Rectangle;
		public var originPos:Point;
		
		public var _owner:DisplayObject = null;
		
		public var _snapOptions:Array = new Array();
		
		public var _xSnapOptions:SnapOption;
		public var _ySnapOptions:SnapOption;
		public var _widthSnapOptions:SnapOption;
		public var _heightSnapOptions:SnapOption;
		public var _generalSnapOptions:String;
		
		public var _snapEnabled:Boolean = true;
		
		
		//////////// Constructor ////////////
		public function SnapManager(owner:DisplayObject){
			this.owner = owner;
		}
      
		//////////// Properties functions ////////////
		public function get owner():DisplayObject{
			return _owner;
		}
		public function set owner(val:DisplayObject){
			if(_owner != val){
				_owner = val;
				if(_owner){
					if(_owner.stage){
						init();
					}else{
						_owner.addEventListener(Event.ADDED_TO_STAGE,addedToStageHandler);
					}
				}
			}
		}
		
		public function set snapOptions(val:Array){
			for(var i in val){
				var opt = val[i];
				if(getValProperty(SnapOption.GENERAL_SNAP_OPTIONS,opt)){
					generalSnapOptions = opt;
				}
				if(getValProperty(SnapOption.X_SNAP_OPTIONS,opt)){
					xSnapOptions = opt;
				}
				if(getValProperty(SnapOption.Y_SNAP_OPTIONS,opt)){
					ySnapOptions = opt;
				}
				if(getValProperty(SnapOption.WIDTH_SNAP_OPTIONS,opt)){
					widthSnapOptions = opt;
				}
				if(getValProperty(SnapOption.HEIGHT_SNAP_OPTIONS,opt)){
					heightSnapOptions = opt;
				}
			}
		}
		public function get snapOptions():Array{
			var options:Array = new Array();
			if(xSnapOptions != null){
				options.push(xSnapOptions);
			}
			if(ySnapOptions != null){
				options.push(ySnapOptions);
			}
			if(widthSnapOptions != null){
				options.push(widthSnapOptions);
			}
			if(heightSnapOptions != null){
				options.push(heightSnapOptions);
			}
			if(generalSnapOptions != null && generalSnapOptions != "none"){
				options.push(generalSnapOptions);
			}
			return options;
		}
		
		[Inspectable(enumeration="none,left,right,center,rel_left,rel_right,rel_center,rel_to_width", defaultValue="none")]
		public function set xSnap(val:String){
			if(SnapOption.X_SNAP_OPTIONS.hasOwnProperty(val)){
				xSnapOptions = SnapOption.X_SNAP_OPTIONS[val];
			}
		}
		public function get xSnap():String{
			return getValProperty(SnapOption.X_SNAP_OPTIONS,xSnapOptions);
		}
		
		public function set xSnapOptions(val:SnapOption){
			_xSnapOptions = val;
			update();
		}
		public function get xSnapOptions():SnapOption{
			return _xSnapOptions;
		}
		
		[Inspectable(enumeration="none,bottom,top,middle,rel_bottom,rel_top,rel_middle,rel_to_height", defaultValue="none")]
		public function set ySnap(val:String){
			if(SnapOption.Y_SNAP_OPTIONS.hasOwnProperty(val)){
				ySnapOptions = SnapOption.Y_SNAP_OPTIONS[val];
			}
		}
		public function get ySnap():String{
			return getValProperty(SnapOption.Y_SNAP_OPTIONS,ySnapOptions);
		}
		
		public function set ySnapOptions(val:SnapOption){
			_ySnapOptions = val;
			update();
		}
		public function get ySnapOptions():SnapOption{
			return _ySnapOptions;
		}
		
		[Inspectable(enumeration="none,full_width,stretch_width,rel_width", defaultValue="none")]
		public function set widthSnap(val:String){
			if(SnapOption.WIDTH_SNAP_OPTIONS.hasOwnProperty(val)){
				widthSnapOptions = SnapOption.WIDTH_SNAP_OPTIONS[val];
			}
		}
		public function get widthSnap():String{
			return getValProperty(SnapOption.WIDTH_SNAP_OPTIONS,widthSnapOptions);
		}
		
		public function set widthSnapOptions(val:SnapOption){
			_widthSnapOptions = val;
			update();
		}
		public function get widthSnapOptions():SnapOption{
			return _widthSnapOptions;
		}
		
		[Inspectable(enumeration="none,full_height,stretch_height,rel_height", defaultValue="none")]
		public function set heightSnap(val:String){
			if(SnapOption.HEIGHT_SNAP_OPTIONS.hasOwnProperty(val)){
				heightSnapOptions = SnapOption.HEIGHT_SNAP_OPTIONS[val];
			}
		}
		public function get heightSnap():String{
			return getValProperty(SnapOption.HEIGHT_SNAP_OPTIONS,heightSnapOptions);
		}
		
		public function set heightSnapOptions(val:SnapOption){
			_heightSnapOptions = val;
			update();
		}
		public function get heightSnapOptions():SnapOption{
			return _heightSnapOptions;
		}
		
		[Inspectable(enumeration="none,full_screen,full_screen_crop,stretch,stretch_crop,relative,relative_crop", defaultValue="none")]
		public function set generalSnapOptions(val:String){
			_generalSnapOptions = val;
			update();
		}
		public function get generalSnapOptions():String{
			return _generalSnapOptions;
		}
		
		public function set snapEnabled(val:Boolean){
			_snapEnabled = val;
		}
		public function get snapEnabled():Boolean{
			return _snapEnabled;
		}
		
		public function get originX():Number{
			return originPos.x;
		}
		public function set originX(val:Number){
			originPos.x = val;
			update();
		}
		public function get originY():Number{
			return originPos.y;
		}
		public function set originY(val:Number){
			originPos.y = val;
			update();
		}
		
		//////////// Public functions ////////////
		public function curRectAsOrigin(){
			//originRect = getRect(parent);
			originRect = new Rectangle(owner.x,owner.y,owner.width,owner.height);
			originPos = new Point(owner.x,owner.y);
			//trace(originRect);
		}
		public function update(){
			if(_snapEnabled && owner.stage){
				var root = owner.root as ResizableRoot;
				if(root!=null && snapOptions.length && originRect != null){
					var updatedRect:Rectangle;
					if(snapRect!=null){
						updatedRect = snapRect;
					}else{
						//updatedRect = getRect(this);
						updatedRect = new Rectangle(owner.x,owner.y,owner.width,owner.height);
					}
					var individualDim:Boolean = false;
					switch(generalSnapOptions){
						case SnapOption.NONE :
							individualDim = true;
							break;
						case SnapOption.FULL_SCREEN :
							break;
						case SnapOption.FULL_SCREEN_CROP :
							break;
						case SnapOption.STRETCH :
							break;
						case SnapOption.STRETCH_CROP :
							break;
						case SnapOption.RELATIVE :
							break;
						case SnapOption.RELATIVE_CROP :
							break;
						default : 
							individualDim = true;
							break;
					}
					//trace(individualDim,generalSnapOptions,xSnapOptions,ySnapOptions,widthSnapOptions,heightSnapOptions)
					if(individualDim){
						if(xSnapOptions){
							owner.x = calculateDim(xSnapOptions,root.stageRect.x, root.stageRect.width, originPos.x, root.originalStageRect.x, root.originalStageRect.width);
						}
						if(ySnapOptions){
							owner.y = calculateDim(ySnapOptions,root.stageRect.y, root.stageRect.height, originPos.y, root.originalStageRect.y, root.originalStageRect.height);
						}
						if(widthSnapOptions){
							owner.width = calculateDim(widthSnapOptions, 0, root.stageRect.width, originRect.width, 0, root.originalStageRect.width);
						}
						if(heightSnapOptions){
							owner.height = calculateDim(heightSnapOptions, 0, root.stageRect.height, originRect.height, 0, root.originalStageRect.height);
						}
					}
				}
			}
		}
		
		//////////// Private functions ////////////
		protected function init(){
			if(originRect==null){
				curRectAsOrigin();
			}
			owner.stage.addEventListener(Event.RESIZE,resizeHandler);
			update();
		}
		
		protected function calculateDim(opt:SnapOption,min:Number,length:Number,origin:Number,originMin:Number,originLength:Number){
			return (length)*opt.prc+
				((length-originLength)*opt.relToPrc+origin)*opt.relPrc+
				(length+opt.stretchAdjust)/(originLength+opt.stretchAdjust)*origin*opt.stretchPrc+
				min+opt.adjust;
		}
		protected function getValProperty(obj:Object,val:*):String{
			for(var prop in obj){
				if(obj[prop]===val){
					return prop;
				}
			}
			return null
		}
		
		//////////// Event Handlers functions ////////////
		private function addedToStageHandler(e:Event){
			init();
		}
		
		
		private function resizeHandler(e:Event){
			update();
		}
		
		
	}
}