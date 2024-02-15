<?php

use Drupal\niklan\Helper\PathHelper;

$connection = \Drupal::database();

//$query = $connection
//  ->select('node__external_content', 'ecf')
//  ->condition('ecf.bundle', 'blog_entry')
//  ->fields('ecf', ['entity_id'])
//  ->where(
//    snippet: "JSON_CONTAINS(ecf.external_content_data, :search_value, :json_path)",
//    args: [
//      ':json_path' => '$.source_pathname',
//      ':search_value' => '"private:\/\/content\/blog\/2015\/11\/26\/d8-create-entities-programmatically\/index.ru.md"',
//    ])
//  ->range(0, 1);

//dump($query->execute()->fetchField());

$path = 'private://foo//bar/../baz/foo/test.txt';
dump(PathHelper::normalizePath($path));
