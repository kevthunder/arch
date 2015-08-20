<?php
class XmlLinkHelper extends AppHelper {
	var $helpers = array('Xml');
	
	function makeItemXml($items){
		$out = '';
		foreach($items as $modelName => $model){
			if(!empty($model)){
				$tagName = Inflector::underscore($modelName);
				if($tagName != 'data'){
					$tagName = Inflector::singularize($tagName);
				}
				unset($model['internal']);
				if(!Set::numeric(array_keys($model))){
					$model = array($model);
				}else{
					
				}
				foreach($model as $item){
					$attrib = array();
					if(is_array($item)){
						$subItems = null;
						unset($item['internal']);
						if(!empty($item['items'])){
							$subItems = $item['items'];
						}
						unset($item['items']);
						extract($this->dataSplitAttrib($item));
						//debug($attrib );
						$content = $this->makeItemXml($collections);
						if(!empty($subItems)){
							$content .= $this->Xml->elem('items',null,$this->makeItemXml($subItems));
						}
					}else{
						$content = $item;
					}
					$out .= $this->Xml->elem($tagName,$attrib,$content);
				}
			}
		}
		return $out;
	}
	
	function dataSplitAttrib($data){
		$collections = array();
		$attrib = array();
		if(!empty($data)){
			$collections = array_filter($data,'is_array');
			$attrib = array_diff_key($data,$collections);
		}
		return compact('attrib', 'collections');
	}
	
	function xmlAttrib($options, $exclude = NULL){
		return $this->_parseAttributes($options, $exclude);
	}
	
	function stripDebug($xml,&$stripped = ''){
		$tags = array('strong','pre');
		$tags_elems = array();
		foreach($tags as $tag){
			$tags_elems[$tag] = array('start'=>'<(?<'.$tag.'_start>'.$tag.')[^>]*?>','end'=>'<\/(?<'.$tag.'_end>'.$tag.')>');
		}
		$reg = '/'.implode('|',set::flatten($tags_elems)).'/sim';
		$stack = 0;
		$offset = 0;
		$start = 0;
		while(preg_match($reg, $xml, $matches, PREG_OFFSET_CAPTURE,$offset)) {
			$matche = $this->qualifyMatch($matches);
			if($matche['elem'] == 'start'){
				if($stack == 0){
					$start = $matche['pos'];
				}
				$stack++;
			}else{
				$stack--;
				if($stack == 0){
					$end = $matche['pos']+strlen($matche['string']);
					$stripped .= substr($xml,$start,$end - $start);
					$xml = substr($xml,0,$start).substr($xml,$end);
					$offset -= $end - $start;
					continue;
				}
			}
			$offset = $matche['pos']+strlen($matche['string']);
		}
		return $xml;
	}
	
	function qualifyMatch($matches){
		$res = array('string'=>$matches[0][0],'pos' => $matches[0][1]);
		foreach($matches as $key => $match){
			if(!is_numeric($key ) && $match[1] != -1){
				$parts = explode('_',$key);
				$res['tag'] = $parts[0];
				$res['elem'] = $parts[1];
			}
		}
		return $res ;
	}
}
?>
