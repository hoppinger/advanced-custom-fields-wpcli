<?php
require_once('vendor/wpreadme2markdown/wpreadme2markdown/src/Converter.php');

$wp_readme_parts = array(
  'pluginInformation',
  'description',
  'installation',
  'filters',
  'command',
  'upgradeNotice',
  'changelog'
);

$github_readme_parts = array(
  'description',
  'installation',
  'command',
  'filters',
  'unitTest',
  'upgradeNotice',
  'changelog'
);

$wordpress_readme = combine_parts($wp_readme_parts);
$github_readme = txt_to_md(combine_parts($github_readme_parts));

$github_readme = replace_codeblocks($github_readme);

create_readme("readme.txt", $wordpress_readme);
create_readme("readme.md", $github_readme);

function replace_codeblocks($readme) {
  $readme =  str_replace('<pre><code>', '```', $readme);
  $readme =  str_replace('</code></pre>', '```', $readme);

  return $readme;
}

function combine_parts($parts) {
  $output = '';

  foreach($parts as $p) {
    $output .= file_get_contents("readme/{$p}.txt");
  }

  return $output;
}

function txt_to_md($readme) {
   return \WPReadme2Markdown\Converter::convert($readme);
}

function create_readme($filename, $content) {
  file_put_contents ( $filename , $content );
  echo "{$filename} created! \n";
}
