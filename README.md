# HGodBee Plugin

## ATENCAO NAO RODAR COMPOSER UPDATE ver item 3 @todo

## Todo

1. transformar as funções var_dump e file_data em uma classe especifica de debug e incluir outros métodos úteis,
2. criar função de criar template vazio no início do plugin,
3. MUITO IMPORTANTE merge a vendor/hgod/classes/class-hgod-admin.php com o github principal. MUITO IMPORTANTE
4. Mudar as validacões de seguranca e user capabilities, tirar das funcoes ajax e passar para o template
5. deprecate HGod_Loads from vendor

## Changelog

`0.9.0`

- multiple changes and adds
- start js eslint configuration

`0.8.0`

- deprecated some methods from class-hgodbee.php
- complete redesign of HB_Scripts
- improvements on the ui

`0.7.0`

- removed config/scripts-config.php
- removed config/styles-config.php
- added class/class-hb-scripts
- minors changes on class-hgodbee.php

`0.6.0`

- removed config/tax-config.php config/tag-config.php
- add class-hb-tax.php
- doc add in class/class-hb-cpt
- minor changes in class-hgobee

`0.5.2`

- removed includes from admin/class-hb-admin.php

`0.5.1`

- Removed config/cpt-config and minor changes in admin/class-hb-admin.php
- minor changes in class-hgodbee.php

`0.5.0`

- changes in core class
- add class/class-hb-cpt.php
- removed config/cpt-config.php
- irrelevant changes in admin/class-hb-admin.php

`0.4.0`

- removed ajax functions from core class and passed to a exclusive file,
- add admin/class-hb-admin.php,
- removed config/admin-config.php
