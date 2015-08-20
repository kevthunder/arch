package com.flashkev.snap{
    import flash.display.MovieClip;

	public class SnapClip extends MovieClip implements ISnappable{
		//////////// variables ////////////
		public var _snapManager:SnapManager = null;
		
		
		//////////// Constructor ////////////
		public function SnapClip(){
			_snapManager = new SnapManager(this);
		}
      
		//////////// Properties functions ////////////
		public function get snapManager():SnapManager{
			return _snapManager;
		}
		
		public function set snapOptions(val:Array){
			_snapManager.snapOptions = val
		}
		public function get snapOptions():Array{
			return _snapManager.snapOptions;
		}
		
		[Inspectable(enumeration="none,left,right,center,rel_left,rel_right,rel_center,rel_to_width", defaultValue="none")]
		public function set xSnap(val:String){
			_snapManager.xSnap = val
		}
		public function get xSnap():String{
			return _snapManager.xSnap;
		}
		
		public function set xSnapOptions(val:SnapOption){
			_snapManager.xSnapOptions = val
		}
		public function get xSnapOptions():SnapOption{
			return _snapManager.xSnapOptions;
		}
		
		[Inspectable(enumeration="none,bottom,top,middle,rel_bottom,rel_top,rel_middle,rel_to_height", defaultValue="none")]
		public function set ySnap(val:String){
			_snapManager.ySnap = val
		}
		public function get ySnap():String{
			return _snapManager.ySnap;
		}
		
		public function set ySnapOptions(val:SnapOption){
			_snapManager.ySnapOptions = val
		}
		public function get ySnapOptions():SnapOption{
			return _snapManager.ySnapOptions;
		}
		
		[Inspectable(enumeration="none,full_width,stretch_width,rel_width", defaultValue="none")]
		public function set widthSnap(val:String){
			_snapManager.widthSnap = val
		}
		public function get widthSnap():String{
			return _snapManager.widthSnap;
		}
		
		public function set widthSnapOptions(val:SnapOption){
			_snapManager.widthSnapOptions = val
		}
		public function get widthSnapOptions():SnapOption{
			return _snapManager.widthSnapOptions;
		}
		
		[Inspectable(enumeration="none,full_height,stretch_height,rel_height", defaultValue="none")]
		public function set heightSnap(val:String){
			_snapManager.heightSnap = val
		}
		public function get heightSnap():String{
			return _snapManager.heightSnap;
		}
		
		public function set heightSnapOptions(val:SnapOption){
			_snapManager.heightSnapOptions = val
		}
		public function get heightSnapOptions():SnapOption{
			return _snapManager.heightSnapOptions;
		}
		
		[Inspectable(enumeration="none,full_screen,full_screen_crop,stretch,stretch_crop,relative,relative_crop", defaultValue="none")]
		public function set generalSnapOptions(val:String){
			_snapManager.generalSnapOptions = val
		}
		public function get generalSnapOptions():String{
			return _snapManager.generalSnapOptions;
		}
		
		public function set snapEnabled(val:Boolean){
			_snapManager.snapEnabled = val
		}
		public function get snapEnabled():Boolean{
			return _snapManager.snapEnabled;
		}
		
		
		public function get originX():Number{
			return _snapManager.originX;
		}
		public function set originX(val:Number){
			_snapManager.originX = val;
		}
		public function get originY():Number{
			return _snapManager.originY;
		}
		public function set originY(val:Number){
			_snapManager.originY = val;
		}
		//////////// Public functions ////////////
		
		//////////// Private functions ////////////
		
	}
}