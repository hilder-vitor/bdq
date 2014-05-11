#!/bin/bash

# baixa o composer
php -r "readfile('https://getcomposer.org/installer');" | php

mkdir composer/

mv composer.phar composer/

