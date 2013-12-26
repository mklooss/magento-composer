<?php

define('DS', DIRECTORY_SEPARATOR);

function parse_version($version, $retrunVersion = false)
{
  if(count((array)explode('.',$version)) > 3 && !preg_match('/^([0-9]+)\.([0-9]+)\.([0-9]+)\.([0-9]+)$/i', $version))
  {
    $verr = array();
    preg_match('/^([0-9]+)\.([0-9]+)\.([0-9]+)\.([0-9]+)(.*)/', $version, $verr);
    $new = '';
    if(isset($verr[0]))
    {
      unset($verr[0]);
    }
    for($i=1; $i <= 3; $i++)
    {
      if(isset($verr[$i]))
      {
	if($i!=1)
	{
	  $new .='.';
	}
	$new .= $verr[$i];
	unset($verr[$i]);
      }
    }
    $add = '';
    if(count($verr))
    {
      $add = str_replace('.','',implode('', $verr));
      if(substr($add, 0, 1) != '-')
      {
	$add = '.'.$add;
      }
    }
    if($retrunVersion)
    {
      return $new.$add;
    }
    return array('version' => $new.$add, 'version_normalized' => $version);
  }
  if($retrunVersion)
  {
    return $version;
  }
  return array('version' => $version);
}

function json_encode_readable($data){

  if(defined('JSON_PRETTY_PRINT'))
  {
    return json_encode($data, JSON_PRETTY_PRINT);
  }
  $json = json_encode($data);
  $json = str_replace('\\/','/', $json);
  $tc = 0;        //tab count
  $r = '';        //result
  $q = false;     //quotes
  $t = "  ";      //tab
  $nl = "\n";     //new line

  for($i=0;$i<strlen($json);$i++){
    $c = $json[$i];
    if($c=='"' && $json[$i-1]!='\\') $q = !$q;
    if($q){
      $r .= $c;
      continue;
    }
    switch($c){
      case '{':
      case '[':
	  $r .= $c . $nl . str_repeat($t, ++$tc);
	  break;
      case '}':
      case ']':
	  $r .= $nl . str_repeat($t, --$tc) . $c;
	  break;
      case ',':
	  $r .= $c;
	  if($json[$i+1]!='{' && $json[$i+1]!='[') $r .= $nl . str_repeat($t, $tc);
	  break;
      case ':':
	  $r .= $c . ' ';
	  break;
      default:
	  $r .= $c;
    }
  }
  return $r;
}