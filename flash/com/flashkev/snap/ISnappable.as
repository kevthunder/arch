package com.flashkev.snap {
	public interface ISnappable{ 
		function get snapManager():SnapManager;
		
		function set snapOptions(val:Array);
		function get snapOptions():Array;
		function set xSnap(val:String);
		function get xSnap():String;
		function set xSnapOptions(val:SnapOption);
		function get xSnapOptions():SnapOption;
		function set ySnap(val:String);
		function get ySnap():String;
		function set ySnapOptions(val:SnapOption);
		function get ySnapOptions():SnapOption;
		function set widthSnap(val:String);
		function get widthSnap():String;
		function set widthSnapOptions(val:SnapOption);
		function get widthSnapOptions():SnapOption;
		function set heightSnap(val:String);
		function get heightSnap():String;
		function set heightSnapOptions(val:SnapOption);
		function get heightSnapOptions():SnapOption;
		function set generalSnapOptions(val:String);
		function get generalSnapOptions():String;
		function set snapEnabled(val:Boolean);
		function get snapEnabled():Boolean;
	}
}