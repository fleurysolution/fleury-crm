<?php
require 'public/index.php'; // Boot CodeIgniter
$items = (new \App\Models\EstimateItemModel())->where('id', 1)->findAll();
print_r(array_keys($items[0] ?? []));
