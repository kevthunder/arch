package  {
	import flash.geom.Point;
	
	public class CastedAbility extends TileAbility{
		//////////// variables ////////////
		
		//////////// Static variables ////////////
		
		//////////// Constructor ////////////
        public function CastedAbility(name = null) {
			super(name);
        }
		
		//////////// Properties functions ////////////
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		
		//////////// Private functions ////////////
		override protected function useOnTile(tile:Tile,inst:AbilityInstance){
			var req:ConnectionXMLRequest = new ConnectionXMLRequest('skills','cast');
			req.params = {
				skill_id : id,
				tile_id : tile.id
			}
			req.priority = 1;
			
			var skillInstance:SkillInstance = new SkillInstance(inst.ability as Skill, inst.user, req);
			
			return ClientMain.instance.connection.addXmlRequest(req);
			
			return false;
			
		}
		
		//////////// Event Handlers functions ////////////

	}
	
}
