<?php

require dirname(__FILE__).DIRECTORY_SEPARATOR.'functions.php';

$package_prefix = 'connect20/';

$data = array(
  'name'         => 'Magento Core Packages',
  'homepage'     => 'http://packages.web.loewenstark.de',
  'description'  => "Please visit https://github.com/magento-hackathon/composer-repository for further informaton.",
  'repositories' => array(),
  'require-all'  => 'true',
);

$predefined = array(
  $package_prefix.'lib_zf_locale' => array(
    '1.11.1.0' => 'https://raw.github.com/magento-hackathon/composer-repository/master/repairedConnectPackages/fixed_Lib_ZF_Locale-1.11.1.0.tgz'
  ),
  $package_prefix.'lib_zf' => array(
    '1.11.1.0' => 'https://raw.github.com/magento-hackathon/composer-repository/master/repairedConnectPackages/fixed_Lib_ZF-1.11.1.0.tgz'
  ),
  $package_prefix.'lib_phpseclib' => array(
    '1.5.0.0' => 'https://raw.github.com/magento-hackathon/composer-repository/master/repairedConnectPackages/fixed_Lib_Phpseclib-1.5.0.0.tgz'
  ),
  $package_prefix.'lib_google_checkout' => array(
    '1.5.0.0' => 'https://raw.github.com/magento-hackathon/composer-repository/master/repairedConnectPackages/fixed_Lib_Google_Checkout-1.5.0.0.tgz'
  ),
);

$loadedPkg = array();
$loadedPkgFile = dirname(__FILE__).DIRECTORY_SEPARATOR.'loadedpkg.json';
if(file_exists($loadedPkgFile))
{
  $loadedPkg = json_decode(file_get_contents($loadedPkgFile), true);
}

$packages = array();

foreach(glob('var/package/*.xml') as $file)
{
  $xml = simplexml_load_file($file);
  $version = parse_version((string)$xml->version);
  $name = (string)$xml->name;
  $modulename = $package_prefix.strtolower($name);
  
  $packages[$modulename] = $version['version'];
  $dlVersion = isset($version['version_normalized']) ? $version['version_normalized'] : $version['version'];
  
  if(isset($loadedPkg[$modulename.'_'.$dlVersion]))
  {
    echo "skipped: ". $modulename.'_'.$dlVersion."\n";
    continue;
  }
  
  $loadedPkg[$modulename.'_'.$dlVersion] = true;
  
  $url = 'http://connect20.magentocommerce.com/community/'.$name.'/'.$dlVersion.'/'.$name.'-'.$dlVersion.'.tgz';
  if(isset($predefined[$modulename][$dlVersion]))
  {
    $url = $predefined[$modulename][$dlVersion];
  }
  
  $info = array(
    'type' => 'package',
    'package' => array(
      'name' => $modulename,
      'version' => $version['version'],
      'dist' => array(
	'type' => 'tar',
	'url'  => $url,
	'reference' => null,
	'shasum' => null
      ),
      'require' => array(
	'magento-hackathon/magento-composer-installer' => '*',
      ),
      'type' => 'magento-module',
      'extra' => array(
	'package-xml' => 'package.xml',
      ),
    )
  );
  if(isset($version['version_normalized']))
  {
    //$info['package']['version'] = $version['version_normalized']; // disabled to many version issues with this tag
  }
  foreach($xml->dependencies->required->package as $pkg)
  {
    $key = $package_prefix.strtolower((string)$pkg->name);
    $info['package']['require'][$key] = '*';
  }
  
  ksort($info['package']);
  
  $data['repositories'][] = $info;
}

// set Versions of current package
foreach($data['repositories'] as $key=>$_row)
{
  foreach($_row['package']['require'] as $module=>$item)
  {
    if(isset($packages[$module]))
    {
      $data['repositories'][$key]['package']['require'][$module] = $packages[$module];
    }
  }
}

// var_dump($data);
// var_dump(json_encode($data));

// exit;
file_put_contents($loadedPkgFile, json_encode_readable($loadedPkg));
file_put_contents(dirname($loadedPkgFile).DIRECTORY_SEPARATOR.basename(getcwd()).'-satis.json', json_encode_readable($data));