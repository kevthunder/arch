package com.flashkev.utils{
	public class XmlUtil{
		//////////// variables ////////////

		//////////// Constructor ////////////
		
		public function XmlUtil() {
		}

		//////////// Properties functions ////////////
		

		//////////// Public functions ////////////
		public static function filterLang( xml:XML, lang:String, clone:Boolean = true ):XML{
			if(clone){
				xml = xml.copy();
			}
			var children:XMLList = xml.children()
			for(var i = 0; i< children.length(); ){
				var child:XML = children[i];
				if(child.hasOwnProperty('@lang') && child.@lang.toString() != lang ){
					delete(children[i]);
				}else{
					if(child is XML){
						filterLang(child, lang, false);
					}
					i++;
				}
			}
			return xml;
		}
		
		//////////// Private functions ////////////
		
		
	}
}