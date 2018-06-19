<?php

$app->group('/dnsadmin', function () use ($app) {
  $app->get('', 'UserFrosting\Sprinkle\Dnsadmin\Controller\AdminController:pageZonesAdmin');
  $app->get('/zones/z/{id}', 'UserFrosting\Sprinkle\Dnsadmin\Controller\AdminController:pageZoneEntriesAdmin');

});

$app->group('/modals/dnsadmin', function () use ($app) {
  $app->get('/create-zone', 'UserFrosting\Sprinkle\Dnsadmin\Controller\AdminController:modalCreateZone');
  $app->get('/edit-zone', 'UserFrosting\Sprinkle\Dnsadmin\Controller\AdminController:modalEditZone');
  $app->get('/delete-zone', 'UserFrosting\Sprinkle\Dnsadmin\Controller\AdminController:modalDeleteZone');
  $app->get('/export-zone', 'UserFrosting\Sprinkle\Dnsadmin\Controller\AdminController:modalExportZoneEntry');


  $app->get('/create-zone-entry', 'UserFrosting\Sprinkle\Dnsadmin\Controller\AdminController:modalCreateZoneEntry');
  $app->get('/edit-zone-entry', 'UserFrosting\Sprinkle\Dnsadmin\Controller\AdminController:modalEditZoneEntry');
  $app->get('/delete-zone-entry', 'UserFrosting\Sprinkle\Dnsadmin\Controller\AdminController:modalDeleteZoneEntry');



});
