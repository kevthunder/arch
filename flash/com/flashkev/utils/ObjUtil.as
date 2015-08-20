package com.flashkev.utils{
	import flash.utils.getQualifiedClassName;
	import flash.utils.getDefinitionByName;

	public class ObjUtil{
		//////////// variables ////////////

		//////////// Constructor ////////////
		
		public function ObjUtil() {
		}

		//////////// Properties functions ////////////
		

		//////////// Public functions ////////////
		public static function merge(... objs):Object{
			var res:Object = new Object
			for(var i:int = 0; i<objs.length; i++){
				for(var p:String in objs[i]){
					res[p] = objs[i][p];
				}
			}
			return res;
		}
		
		public static function simpleExtract(path:String,target:Object/*,aliased:Boolean = true*/){
			var aliasPart:String = path;
			var rest:String = ""; 
			/*var aliases:Object = new Object();
			if(aliased && target is IPropAliased){
				aliases = (target as IPropAliased).propAliases
			}*/
			var ppos:int = path.indexOf('.');
			if(ppos != -1){
				aliasPart = path.substr(0,ppos);
				rest = path.substr(ppos);
			}
			/*if(aliased && aliases.hasOwnProperty(aliasPart)){
				return extractVal(aliases[aliasPart]+rest,target,false);
			}else */if(ppos != -1){
				if(!target.hasOwnProperty(aliasPart)){
					return null
				}
				return simpleExtract(rest.substr(1),target[aliasPart]);
			}else if(target.hasOwnProperty(aliasPart)){
				return target[aliasPart];
			}
			return null;
		}
		public static function extractValues(obj:Object):Array{
			var arr = new Array();
			for(var key:String in obj){
				arr.push(obj[key]);
			}
			return arr;
		}
		
		
		public static function copyValues(obj:Object):Object{
			var cpy = new Object();
			for(var key:String in obj){
				cpy[key] = obj[key];
			}
			return cpy;
		}
		
		public static function loadObjectByClassName(classname:String,parentType:Class = null,replacement:Object = null):Object{
			if(replacement && classname == getQualifiedClassName(replacement)){
				return replacement;
			}
			var c:Class;
			try{
				c = getDefinitionByName(classname) as Class;
			}catch(e){
				
			}
			if(c){
				var val:Object = new c();
				if(parentType) {
					val = val as parentType;
				}
				if(val){
					return val;
				}
			}
			return null;
		}
		//////////// Private functions ////////////
		
		
	}
}