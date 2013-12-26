<?php

require dirname(__FILE__).DIRECTORY_SEPARATOR.'functions.php';

$merged = 'merged.json';
$files = glob('magento-*-satis.json');
natsort($files);

$data = array();
if(file_exists($merged))
{
  $data = json_decode(file_get_contents($merged), true);
}

$new = array();
// if merged file exists
if(isset($data['repositories']))
{
  foreach($data['repositories'] as $_row)
  {
    $name = $_row['package']['name'];
    $version = $_row['package']['version'];
    $new[$name.'_'.$version] = $_row;
  }
  unset($data['repositories']);
}

foreach($files as $_file)
{
  $json = json_decode(file_get_contents($_file), true);
  if(empty($data))
  {
    $data = $json;
    if(isset($data['repositories']))
    {
      unset($data['repositories']);
    }
  }
  foreach($json['repositories'] as $_row)
  {
    $name = $_row['package']['name'];
    $version = $_row['package']['version'];
    $new[$name.'_'.$version] = $_row;
  }
  unset($json);
}
ksort($new);

$data['repositories'] = array();
foreach($new as $module)
{
  $data['repositories'][] = $module;
}

file_put_contents($merged,json_encode_readable($data));