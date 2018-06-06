<?php

$app->group('/api/dns', function () use ($app) {
  $app->get('/zones', 'UserFrosting\Sprinkle\Dnsadmin\Controller\ApiController:getZones');
  $app->post('/zones', 'UserFrosting\Sprinkle\Dnsadmin\Controller\ApiController:createZone');
  $app->put('/zones/z/{id}', 'UserFrosting\Sprinkle\Dnsadmin\Controller\ApiController:editZone');

});
