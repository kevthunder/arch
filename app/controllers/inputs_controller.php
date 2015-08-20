<?php
class InputsController extends AppController {

	var $name = 'Inputs';
	var $helpers = array('Xml','XmlLink.XmlLink');
	var $uses = array('User','Event','TimedEvent');
	var $components = array('Link');

	function save() {
		$warnings = array();
		$this->autoRender = false;
		$dom = new DomDocument;
		$dom->preserveWhiteSpace = FALSE;
		if(empty($this->user)){
			$warnings[] = 300;
		}
		if($dom->loadXML($_POST['xml'])){
			foreach($dom->firstChild->childNodes as $child){
				if(in_array($child->nodeName,$this->Link->linkedModels)){
					$flip =array_flip($this->Link->linkedModels);
					$modelName = $flip[$child->nodeName];
					$model = ClassRegistry::init($modelName);
					if($model){
						$data = $model->xmlToData($child->ownerDocument->saveXML($child));
						$model->create();
						$aro = array();
						if(!empty($this->user)){
							$aro[] = $this->User->myNodeRef($this->user['User']['id']/*,true*/);
						}
						if($model->triggerAction('save',array($data),$aro)){
							echo $modelName.' saved';
						}else{
							echo 'can\'t save '.$modelName;
						}
					}else{
						echo 'cant init '.$modelName;
					}
				}else{
					echo $child->nodeName . ' cant be saved';
				}
			}
			echo 'Received';
		}else{
			echo 'Malformatted XML';
		}
		$this->set('warnings', $warnings);
	}
	function test(){
		//$this->Link->disableCache = true;
		$testXml = '<architecturers>';
		//$testXml = '<architecturers><request no="1" handler="test" action="test">test</request></architecturers>';
		//$testXml = '<architecturers><request no="1" handler="characters" action="my_characters">test</request></architecturers>';
		//$testXml .= '<request no="5" handler="skills" action="my_listing"/>';
		//$testXml .= '<request no="1" handler="commun" action="test"></request>';
		$testXml .= '<request no="2" handler="tiles" action="keep_updated" x="-5" y="-5" zone_id="1" width="10" height="10">test</request>';
		$testXml .= '</architecturers>';
		$this->parse($testXml);
		
	}
	
	function test2(){
		
		$testXml = '<architecturers>';
		//$testXml = '<architecturers><request no="1" handler="test" action="test">test</request></architecturers>';
		//$testXml = '<architecturers><request no="1" handler="characters" action="my_characters">test</request></architecturers>';
		//$testXml = '<architecturers><request no="1" handler="tiles" action="keep_updated" x="-5" y="-5" zone_id="1" width="10" height="10">test</request></architecturers>';
		$testXml .= '<request no="1" handler="commun" action="reset_buffer" character_id="1">test</request>';
		$testXml .= '<request no="2" handler="characters" action="select" character_id="2">test</request>';
		$testXml .= '</architecturers>';
		$this->parse($testXml);
		
	}
	
	function test3(){
		
		/*$testXml = '<architecturers>
					  <request no="4" handler="commun" action="save">
						<path start_tile_id="7" end_tile_id="84" character_id="1" id="-1">
						  <step entry_point_x="0.4990000000000001" entry_point_y="0.5" exit_point_x="1" exit_point_y="0.5" tile_id="7" length="0.5009999999999999"/>
						  <step entry_point_x="0" entry_point_y="0.5" exit_point_x="0.5" exit_point_y="1" tile_id="6" length="0.7071067811865476"/>
						  <step entry_point_x="0.5" entry_point_y="0" exit_point_x="0.5" exit_point_y="1" tile_id="3" length="1"/>
						  <step entry_point_x="0.5" entry_point_y="0" exit_point_x="0.5" exit_point_y="1" tile_id="2" length="1"/>
						  <step entry_point_x="0.5" entry_point_y="0" exit_point_x="0.5" exit_point_y="0.5" tile_id="84" length="0.5"/>
						</path>
					  </request>
					  <request no="5" handler="tiles" action="keep_updated" x="-5" y="-5" zone_id="1" width="10" height="10">test</request>
					</architecturers>';*/
		/*$testXml = '<architecturers>
					  <request no="5" handler="items" action="my_inventory" />
					</architecturers>';*/
					
		$testXml = '<architecturers>';
		/*$testXml .= '	<request no="7" handler="skills" action="move" skill_id="-3" tile_id="368">
							<path start_tile_id="2" end_tile_id="368" character_id="1" id="-2">
							  <step entry_point_x="0.5" entry_point_y="0.49375253169417377" exit_point_x="0.5" exit_point_y="0" tile_id="2" length="0.49375253169417377"/>
							  <step entry_point_x="0.5" entry_point_y="1" exit_point_x="0.5" exit_point_y="0" tile_id="3" length="1"/>
							  <step entry_point_x="0.5" entry_point_y="1" exit_point_x="0.5" exit_point_y="0" tile_id="6" length="1"/>
							  <step entry_point_x="0.5" entry_point_y="1" exit_point_x="0.5" exit_point_y="0" tile_id="13" length="1"/>
							  <step entry_point_x="0.5" entry_point_y="1" exit_point_x="0.5" exit_point_y="0" tile_id="64" length="1"/>
							  <step entry_point_x="0.5" entry_point_y="1" exit_point_x="1" exit_point_y="0.5" tile_id="65" length="0.7071067811865476"/>
							  <step entry_point_x="0" entry_point_y="0.5" exit_point_x="0.5" exit_point_y="0" tile_id="66" length="0.7071067811865476"/>
							  <step entry_point_x="0.5" entry_point_y="1" exit_point_x="0.5" exit_point_y="0" tile_id="83" length="1"/>
							  <step entry_point_x="0.5" entry_point_y="1" exit_point_x="0.5" exit_point_y="0" tile_id="89" length="1"/>
							  <step entry_point_x="0.5" entry_point_y="1" exit_point_x="0.5" exit_point_y="0" tile_id="90" length="1"/>
							  <step entry_point_x="0.5" entry_point_y="1" exit_point_x="0" exit_point_y="0.5" tile_id="91" length="0.7071067811865476"/>
							  <step entry_point_x="1" entry_point_y="0.5" exit_point_x="0" exit_point_y="0.5" tile_id="128" length="1"/>
							  <step entry_point_x="1" entry_point_y="0.5" exit_point_x="0" exit_point_y="0.5" tile_id="124" length="1"/>
							  <step entry_point_x="1" entry_point_y="0.5" exit_point_x="0" exit_point_y="0.5" tile_id="123" length="1"/>
							  <step entry_point_x="1" entry_point_y="0.5" exit_point_x="0" exit_point_y="0.5" tile_id="126" length="1"/>
							  <step entry_point_x="1" entry_point_y="0.5" exit_point_x="0" exit_point_y="0.5" tile_id="245" length="1"/>
							  <step entry_point_x="1" entry_point_y="0.5" exit_point_x="0" exit_point_y="0.5" tile_id="234" length="1"/>
							  <step entry_point_x="1" entry_point_y="0.5" exit_point_x="0" exit_point_y="0.5" tile_id="223" length="1"/>
							  <step entry_point_x="1" entry_point_y="0.5" exit_point_x="0" exit_point_y="0.5" tile_id="216" length="1"/>
							  <step entry_point_x="1" entry_point_y="0.5" exit_point_x="0" exit_point_y="0.5" tile_id="151" length="1"/>
							  <step entry_point_x="1" entry_point_y="0.5" exit_point_x="0" exit_point_y="0.5" tile_id="154" length="1"/>
							  <step entry_point_x="1" entry_point_y="0.5" exit_point_x="0.5" exit_point_y="0" tile_id="158" length="0.7071067811865476"/>
							  <step entry_point_x="0.5" entry_point_y="1" exit_point_x="0" exit_point_y="0.5" tile_id="155" length="0.7071067811865476"/>
							  <step entry_point_x="1" entry_point_y="0.5" exit_point_x="0.5" exit_point_y="0" tile_id="153" length="0.7071067811865476"/>
							  <step entry_point_x="0.5" entry_point_y="1" exit_point_x="0.5" exit_point_y="0.5" tile_id="152" length="0.5"/>
							</path>
						  </request>';
		$testXml .= '	<request no="8" handler="skills" action="cast" skill_id="3" tile_id="368"/>';*/
		$testXml .= '	<request no="7" handler="skills" action="move" skill_id="-2" tile_id="91">
							<path start_tile_id="95" end_tile_id="91" character_id="1" id="-1">
							  <step entry_point_x="0.5" entry_point_y="0.5" exit_point_x="0.5" exit_point_y="1" tile_id="95" length="0.5"/>
							  <step entry_point_x="0.5" entry_point_y="0" exit_point_x="0.5" exit_point_y="0.5" tile_id="94" length="0.5"/>
							</path>
						  </request>';
		//$testXml .= '	<request no="8" handler="skills" action="cast" skill_id="3" tile_id="91"/>';
		$testXml .= '</architecturers>';
		$this->parse($testXml);
		
	}
	
	
	function test4(){
		
		/*$testXml = '<architecturers>
					  <request no="4" handler="skills" action="my_listing">
					  </request>
					</architecturers>';*/
		/*$testXml = '<architecturers>
					  <request no="32" handler="skills" action="cast" tile_id="263" skill_id="4"/>
					</architecturers>';*/
		/*$testXml = '<architecturers>
					  <request no="5" handler="skills" action="cast" tile_id="7" skill_id="5"/>
					</architecturers>';*/
		/*$testXml = '<architecturers>
					  <request no="5" handler="skills" action="cast" tile_id="442" skill_id="1"/>
					</architecturers>';*/
		/*$testXml = '<architecturers>
					  <request no="30" handler="skills" action="cast" skill_id="3" tile_id="263"/>
					</architecturers>';//Fertilize */
		/*$testXml = '<architecturers>
					  <request no="7" handler="skills" action="cast" tile_id="369" skill_id="1"/>
					</architecturers>';//create tile */ 

		$testXml = '<architecturers>';
		/*$testXml .= '	<request no="7" handler="skills" action="move" skill_id="-2" tile_id="91">
							<path start_tile_id="95" end_tile_id="91" character_id="1" id="-1">
							  <step entry_point_x="0.5" entry_point_y="0.5" exit_point_x="0.5" exit_point_y="1" tile_id="95" length="0.5"/>
							  <step entry_point_x="0.5" entry_point_y="0" exit_point_x="0.5" exit_point_y="0.5" tile_id="94" length="0.5"/>
							</path>
						  </request>';
		$testXml .= '	<request no="8" handler="skills" action="cast" skill_id="3" tile_id="91"/>';*/
		//$testXml .= '	<request no="6" handler="skills" action="cast" tile_id="48" skill_id="6"/>';
		//$testXml .= '	<request no="8" handler="skills" action="cast" skill_id="3" tile_id="37"/>';
		//$testXml .= '	<request no="9" handler="skills" action="cast" tile_id="60" skill_id="4"/>';
		$testXml .= '	<request no="2" handler="tiles" action="keep_updated" x="-15" width="30" y="-25"  zone_id="1" height="25"/>';
		/*$testXml = '<architecturers>
					  <request no="6" handler="skills" action="cast" skill_id="4" tile_id="159"/>
					  <request no="2" handler="tiles" action="keep_updated" x="-27" width="30" zone_id="1" y="-22" height="25"/>
					</architecturers>';//Fertilize */
		$testXml .= '</architecturers>';
		$this->parse($testXml);
	}
	
	function test5(){
		/*$testXml = '<architecturers>
					  <request no="6" handler="messages" action="post" text="Hello world!"/>
					</architecturers>';*/
		$testXml = '<architecturers>
					  <request no="7" handler="messages" action="keep_updated" y="-0.5" exclude="7" range="10" x="-3.5" zone_id="1" since="1"/>
					</architecturers>';
		$this->parse($testXml);
	}
	
	function test6(){
		$testXml = '<architecturers>';
		/*$testXml .= '	<request no="6" handler="skills" action="move" tile_id="323" skill_id="-2">
							<path start_tile_id="52" end_tile_id="514" character_id="1" id="-1">
							  <step entry_point_x="0.5" entry_point_y="0.5" exit_point_x="0" exit_point_y="0.5" tile_id="52" length="0.5"/>
							  <step entry_point_x="1" entry_point_y="0.5" exit_point_x="0" exit_point_y="0.5" tile_id="77" length="1"/>
							  <step entry_point_x="1" entry_point_y="0.5" exit_point_x="0" exit_point_y="0.5" tile_id="78" length="1"/>
							  <step entry_point_x="1" entry_point_y="0.5" exit_point_x="0.5" exit_point_y="1" tile_id="81" length="0.7071067811865476"/>
							  <step entry_point_x="0.5" entry_point_y="0" exit_point_x="0.5" exit_point_y="0.5" tile_id="231" length="0.5"/>
							</path>
						</request>';*/
		$testXml .= '	<request no="7" handler="skills" action="move" tile_id="384" skill_id="-3">
					      <path start_tile_id="120" end_tile_id="384" character_id="1" id="-2">
							  <step entry_point_x="0.5114610306789231" entry_point_y="0.5" exit_point_x="1" exit_point_y="0.5" tile_id="120" length="0.4885389693210769"/>
							  <step entry_point_x="0" entry_point_y="0.5" exit_point_x="0.5" exit_point_y="0" tile_id="59" length="0.7071067811865476"/>
							  <step entry_point_x="0.5" entry_point_y="1" exit_point_x="0.5" exit_point_y="0" tile_id="58" length="1"/>
							  <step entry_point_x="0.5" entry_point_y="1" exit_point_x="0" exit_point_y="0.5" tile_id="137" length="0.7071067811865476"/>
							  <step entry_point_x="1" entry_point_y="0.5" exit_point_x="0.5" exit_point_y="0" tile_id="138" length="0.7071067811865476"/>
							  <step entry_point_x="0.5" entry_point_y="1" exit_point_x="0.5" exit_point_y="0.5" tile_id="139" length="0.5"/>
							</path>
						</request>';
		$testXml .= '	<request no="8" handler="skills" action="cast" tile_id="384" skill_id="1"/>';
		//$testXml .= '  <request no="8" handler="skills" action="cast" skill_id="1" tile_id="514"/>';*/
		//$testXml .= '  <request no="2" handler="tiles" action="keep_updated" x="-26" width="30" zone_id="1" y="-9" height="25"/>';
		//$testXml .= '  <request no="3" handler="messages" action="keep_updated" since="1346108718.0359" x="0.5" range="10" zone_id="1" exclude="" y="6.5"/>';
		$testXml .= '</architecturers>';
		$this->parse($testXml);
	}
	
	function parse($xml = null){
		if(empty($this->params['named']['debug'])){
			Configure::write("debug",0);
		}
		if(!empty($_POST['xml'])){
			$xml = $_POST['xml'];
		}
		if(!empty($xml)){
			$dom = new DomDocument;
			$dom->preserveWhiteSpace = FALSE;
			if($dom->loadXML($xml)){
				$this->TimedEvent->triggerUnlocalizedEvents();
		
				unset($xml);  // free ressources
				$responses = array();
				foreach($dom->firstChild->childNodes as $child){
					if($child->nodeName == 'request'){
						$responses[] = $this->_parseRequest($child);
					}
				}
				//debug($responses);
				$this->set('responses',$responses);
				if(!empty($this->params['named']['debug'])){
					$this->layout = 'debug_xml';
				}else{
					$this->layout = 'xml/default';
					header ("content-type: text/xml");
				}
				$this->set('debugMsgs',ob_get_contents());
				ob_end_clean();
				$this->render('responses');
			}else{
				$this->autoRender = false;
				echo 'Malformatted XML';
			}
		}else{
			$this->autoRender = false;
			echo 'Empty request';
		}
		//$this->render(false);
	}
	
	function _parseRequest($xmlnode){
		$handlerName = $xmlnode->getAttribute('handler');
		App::import('Lib', 'ClassCollection'); 
		$handler = ClassCollection::getObject('linkAction',$handlerName);
		if($handler){
			$aro = array();
			if(!empty($this->user)){
				$aro[] = $this->User->myNodeRef($this->user['User']['id']/*,true*/);
			}
			$handler->defaultAro = $aro;
			$handler->controller = $this;
			unset($aro, $handlerName);  // free ressources
			return $handler->execute($xmlnode);
		}else{
			return LinkAction::invalidRequest($xmlnode,404);
		}
	}
	
	function refresh(){
		$this->autoRender = false;
		
	}

}
