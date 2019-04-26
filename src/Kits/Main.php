<?php
namespace Kits;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\item\Item;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\inventory\PlayerInventory;
use pocketmine\inventory\BaseInventory;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat as C;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener{
    
    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this,$this);
        
        if(!is_dir($this->getDataFolder())){
			@mkdir($this->getDataFolder());
		}
		if(!file_exists($this->getDataFolder() . "config.yml")){
			$this->saveDefaultConfig();
		}
		//sample config format
		/**$this->world = $this->getConfig()->get("World", "prk1");
		$this->minutes = $this->getConfig()->get("Minutes", 5);**/
				
    }
    
	public function onCommand(CommandSender $sender, Command $cmd, string $label,array $args) : bool {
	if(($cmd->getName()) == "kits"){
	//if($sender instanceOf Player){
	If(!(isset($args[0]))){
	$sender->sendMessage(C::RED.C::UNDERLINE."/kits (kit name)|list|view|add|del"); 
	return true;
	}
	switch($args[0]){
	case "list": 
	    $cf = new Config($this->getDataFolder() . "config.yml");
	    $nm = $cf->get("name");
	    $sender->sendMessage("Kits: ".implode(",",$nm));
	    return true;
	    break;
	    
	case "view":
	  $cf = new Config($this->getDataFolder() . "config.yml");
	  if(($cf->get($args[1])) == null){
	  $sender->sendMessage(C::RED.C::UNDERLINE."No kit with that name");
	  return true;
	  }
	  $list = [];
	  $ar = $cf->get($args[1]);
	  foreach($ar as $a){
	  $n = explode(",",$a);
	  if(!is_numeric($n[0]) or $n[0] > 1000 or $n[1] > 100 or $n[2] > 200){
	  $sender->sendMessage(C::RED.C::UNDERLINE."Encountered Problem with the kit"); 
	  return true;
	  }
	  array_push($list,(Item::get((int)$n[0],(int)$n[1],(int)$n[2])->getName())." x".(int)$n[2]);
	  }
	  $sender->sendMessage(C::YELLOW.C::UNDERLINE."Viewing kit: ".$args[1]."\n- ".implode("\n- ", $list));
	    break;
	    
	case "add":
	  if(isset($args[1]) and isset($args[2])){
	  $cf = new Config($this->getDataFolder() . "config.yml");
	  if(($cf->get($args[1])) != null){
	  $sender->sendMessage(C::RED.C::UNDERLINE."There is an existing kit with that name");   
	  return true;
	  }
	  $arr = [];
	  $nme = $args[1];
	  unset($args[1]);
	  unset($args[0]);
	  foreach ($args as $ar){
	  array_push($arr,$ar);   
	     
	  }
	    $cf->set($nme,$arr);
	    $list = $cf->get("name");
	    array_push($list,$nme);
	    $cf->set("name",$list);
	    $cf->save();
	    $sender->sendMessage(C::GREEN.C::UNDERLINE."Kit Created!");
	  }else{
	  $sender->sendMessage(C::RED.C::UNDERLINE."/kits add (name) (items=> ID,META/DMG,COUNT)");  
	  }
	  break;
	  
	  case "del":
	   if(isset($args[1])){
	   $cf = new Config($this->getDataFolder() . "config.yml");    
	   $ar = $cf->get("name");
	   if(array_search($args[1],$ar) == null){
	   $sender->sendMessage(C::RED.C::UNDERLINE."No kit with that name");
	   return true;
	   }
	   unset($ar[(int)array_search($args[1],$ar)]);
	   $cf->set("name",$ar);
	   $d = $cf->getall();
	   unset($d[$args[1]]);
	   $cf->setAll($d);
	   $cf->save();
	   $sender->sendMessage(C::GREEN.C::UNDERLINE."Succesfully deleted kit");
	   }else{
	    $sender->sendMessage(C::RED.C::UNDERLINE."/kits del (kit)");  
	   }   
	      break;
	
	default:
	$kit = $this->getConfig()->get($args[0]);
    if(!$kit == null){
	foreach($kit as $i){
    $n = explode(",",$i);
    if(!is_numeric($n[0]) or $n[0] > 1000 or $n[1] > 100 or $n[2] > 200){
    $sender->sendMessage(C::RED.C::UNDERLINE."Encountered problem with the kit");  
    return true;
    }
    if($sender instanceOf Player){
	$sender->getPlayer()->getInventory()->addItem(Item::get(((int)$n[0]),((int)$n[1]),((int)$n[2])));
	$sender->sendMessage(C::GREEN.C::UNDERLINE."You got Kit ".$args[1]);
    }
	}
	}else{
	$sender->sendMessage(C::RED.C::UNDERLINE."No available kit with that name!");  
	}
	}
	
	
	
	
	
	
	
}
return true;
	}
    
    public function onDisable(){
     $this->getLogger()->info("§cOffline");
    }
}
