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
$ck4me_element = isset($ck4me_element) ? (string)$ck4me_element : '';
$ck4me_name = isset($ck4me_name) ? (string)$ck4me_name : '';
$ck4me_key = isset($ck4me_key) ? (string)$ck4me_key : '';
$ck4me_config = isset($ck4me_config) ? (string)$ck4me_config : '';

$dir = MODX_BASE_PATH.'assets/snippets/CK4ME/';
include_once($dir.'lib/Cache.php');
if(!empty($ck4me_element) && !empty($ck4me_name) && !empty($ck4me_key) && !empty($ck4me_config) && file_exists($dir.$ck4me_config.'.json')){
	$config = json_decode(file_get_contents($dir.$ck4me_config.'.json'), true);
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
	$data = $modx->cache[$ck4me_config]->get($ck4me_key);
	if(!$data){
		switch($ck4me_element){
			case 'chunk':{
				$tmp = $modx->documentOutput;
				$minParserPasses= empty ($modx->minParserPasses) ? 2 : $modx->minParserPasses;
				$maxParserPasses= empty ($modx->maxParserPasses) ? 10 : $modx->maxParserPasses;
				$data = $modx->getChunk($ck4me_name);

				for ($i= 0; $i < $minParserPasses; $i++) {
					if ($i == ($minParserPasses -1)) $st= strlen($data);

					$modx->documentOutput = $data;
					$this->invokeEvent("OnParseDocument");
					$data = $modx->documentOutput;

					$data = $modx->mergeSettingsContent($data);
					$data = $modx->mergeDocumentContent($data);
					$data = $modx->mergeSettingsContent($data);
					$data = $modx->mergeChunkContent($data);
					$data = $modx->evalSnippets($data); //Only [[ ]]

					if(isset($modx->config['show_meta']) && $modx->config['show_meta']==1) {
						$data = $modx->mergeDocumentMETATags($data);
					}
					$data = $modx->mergeSettingsContent($data);
					if ($i == ($minParserPasses -1) && $i < ($this->maxParserPasses - 1)) {
						// check if source length was changed
						$et= strlen($source);
						if ($st != $et)
							$minParserPasses++; // if content change then increase passes because
					}
				}
				$modx->documentOutput = $tmp;
				break;
			}
			case 'snippet':{
				$data = $modx->runSnippet($ck4me_name, $modx->Event->params);
				break;
			}
		}
		$modx->cache[$ck4me_config]->set($ck4me_key, $data);
	}
}
$data = str_replace('[!', '[[', $data);
$data = str_replace('!]', ']]', $data);
return $data;
?>