<?php
function smarty_function_parse_url($paramer,$template){
	global $config,$seo;
	if(!$paramer['index'] && $paramer['m']=='member'){
		$index='member';
		unset($paramer['m']);
	}else{
		$index=$paramer['index'];
	}

    $paramer = array_filter(array_merge($_GET,$paramer));
     unset($paramer['index']);
     $url  =  get_url($paramer,$config,$seo,$paramer['m'],$index,$template);
	return $url;
}
?>