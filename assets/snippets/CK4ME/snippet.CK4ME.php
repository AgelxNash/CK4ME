<?php
/**
 * DocLister snippet
 *
 * @license GNU General Public License (GPL), http://www.gnu.org/copyleft/gpl.html
 * @author Agel_Nash <Agel_Nash@xaker.ru>
 */
if (!defined('MODX_BASE_PATH')) {
    die('HACK???');
}
$ck4me_snippet = isset($ck4me_snippet) ? (string)$ck4me_snippet : '';
$ck4me_key = isset($ck4me_key) ? (string)$ck4me_key : '';
$ck4me_config = isset($ck4me_config) ? (string)$ck4me_config : '';

$dir = MODX_BASE_PATH.'assets/snippets/CK4ME/';
include_once($dir.'Cache.php');
if(!empty($ck4me_snippet) && !empty($ck4me_key) && !empty($ck4me_config) && $file_exists($dir.$ck4me_config.'.json')){
	$config = json_decode(file_get_contents($dir.$ck4me_config.'.json'));
	/*
		$config = array(
			'driver'             => 'sqlite',
			'default_expire'     => 3600,
			'database'           => MODX_BASE_PATH.'assets/cache/cache.sql3',
			'schema'             => 'CREATE TABLE caches(id VARCHAR(127) PRIMARY KEY, tags VARCHAR(255), expiration INTEGER, cache TEXT)',
		);
	*/
}else{
	$config = array();
}

if(!isset($modx->cache)){
	$modx->cache = array();
}

if(!isset($modx->cache[$ck4me_config]) && is_array($config) && isset($config['driver'])){
	$modx->cache[$ck4me_config] = Cache::instance($config['driver'], $config);
}

if(isset($modx->cache[$ck4me_config])){
	$data = $modx->cache->get($ck4me_key);
	if(!$data){
		$data = $modx->runSnippet($ck4me_snippet, $modx->Event->params);
		$modx->cache->set($ck4me_key, $data);
	}
}