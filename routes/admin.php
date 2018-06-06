<?php

$app->group('/dnsadmin', function () use ($app) {
  $app->get('', 'UserFrosting\Sprinkle\Dnsadmin\Controller\AdminController:pageZonesAdmin');
});

$app->group('/modals/dnsadmin', function () use ($app) {
  $app->get('/create-zone', 'UserFrosting\Sprinkle\Dnsadmin\Controller\AdminController:modalCreateZone');
});
