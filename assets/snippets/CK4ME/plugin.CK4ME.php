<?php
/**
 * CK4ME clear cache Plugin
 *
 * @license GNU General Public License (GPL), http://www.gnu.org/copyleft/gpl.html
 * @author Agel_Nash <Agel_Nash@xaker.ru>
 */
if (!defined('MODX_BASE_PATH')) { die('HACK???'); }

if(!isset($modx->cache)){
	$modx->cache = array();
}

$dir = MODX_BASE_PATH.'assets/snippets/CK4ME/';
include_once($dir.'lib/Cache.php');

$configName = isset($configName) ? explode(',', $configName) : array();
foreach($configName as $value){
	if(file_exists($dir.$value.'.json')){
		$config = json_decode(file_get_contents($dir.$value.'.json'), true);
	}else{
		$config = array();
	}
	
	if(!isset($modx->cache[$value]) && is_array($config) && isset($config['driver'])){
		$modx->cache[$value] = Cache::instance($config['driver'], $config);
	}
	
	if(isset($modx->cache[$value])){
		$modx->cache[$value]->delete_all();
	}
}