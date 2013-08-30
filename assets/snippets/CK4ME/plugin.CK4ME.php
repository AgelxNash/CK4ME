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
include_once($dir.'Cache.php');

$configName = isset($configName) ? explode(',', $configName) : array();
foreach($configName as $value){
	if(file_exists($dir.$value.'.json')){
		$config = json_decode(file_get_contents($dir.$value.'.json'));
		/*
		$config = array(
			'driver'             => 'sqlite',
			'default_expire'     => 3600,
			'database'           => MODX_BASE_PATH.'assets/cache/cache.sql3',
			'schema'             => 'CREATE TABLE caches(id VARCHAR(127) PRIMARY KEY, tags VARCHAR(255), expiration INTEGER, cache TEXT)',
		)
		*/
	}else{
		$config = array();
	}
	
	if(!isset($modx->cache[$value]) && is_array($config) && isset($config['driver'])){
		$modx->cache[$value] = Cache::instance($config['driver'], $config);
	}
	
	if(isset($modx->cache[$ck4me_config])){
		$modx->cache[$ck4me_config]->delete_all();
	}
}