<?php

$app->group('/api/dns', function () use ($app) {
  $app->get('/zones', 'UserFrosting\Sprinkle\Dnsadmin\Controller\ApiController:getZones');
  $app->post('/zones', 'UserFrosting\Sprinkle\Dnsadmin\Controller\ApiController:createZone');
  $app->put('/zones/z/{id}', 'UserFrosting\Sprinkle\Dnsadmin\Controller\ApiController:editZone');
  $app->delete('/zones/z/{id}', 'UserFrosting\Sprinkle\Dnsadmin\Controller\ApiController:deleteZone');

  $app->get('/zones/z/{id}/entries', 'UserFrosting\Sprinkle\Dnsadmin\Controller\ApiController:getZoneEntries');
  $app->post('/zones/z/{id}/entries', 'UserFrosting\Sprinkle\Dnsadmin\Controller\ApiController:createZoneEntry');



});
