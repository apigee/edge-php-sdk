#!/bin/bash

# Creates the contents of an autoload file.
#
# In the case of Drupal 8, all dependencies listed in composer.json are already
# fulfilled, so we don't need to rely on composer's autoloader.

echo "<?php"
echo "spl_autoload_register(function (\$class) {"
echo "  switch (\$class) {"
for x in $(find Apigee -type f | grep -v "Apigee/test/"); do
  y=$(echo $x | sed 's:/:\\:g' | sed -E 's:.php$::g')
  echo "    case '${y}':"
  echo "      require_once __DIR__ . '/${x}';"
  echo "      break;"
done;
echo "  }"
echo "});"

