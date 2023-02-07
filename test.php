<?php

use Symfony\Component\DomCrawler\Crawler;

$html = <<<'HTML'
<p>Hello, <a href="#">World</a>! <strong>Bold!</strong></p>

<p><strong><em>Hello</em>, <a href="#">World</a>!</strong></p>

<YouTube id="abc"></YouTube>

<Aside>123</Aside>

<aside>321</aside>
HTML;

$structure = [];

// Native DOMDOcument doesn't supports HTML5 elements, add a lot of noise
// elements (like line breaks) and etc. Crawler it's an easy and fast way to
// solve every problem.
$crawler = new Crawler($html);
$crawler = $crawler->filter('body');
foreach ($crawler->children() as $element) {
  parse($element, $structure);
}

function parse(DOMNode $element, &$structure) {
  $structure_item = [
    'element' => $element->nodeName,
  ];
  if ($element->hasChildNodes()) {
    $children_structure = [];
    foreach ($element->childNodes as $childNode) {
      parse($childNode, $children_structure);
    }
    $structure_item['children'] = $children_structure;
  }
  else {
    $structure_item['value'] = $element->nodeValue;
  }

  $structure[] = $structure_item;
}

dump($structure);

