<?php

$app->group('/api/dns', function () use ($app) {
  $app->get('/zones', 'UserFrosting\Sprinkle\Dnsadmin\Controller\ApiController:getZones');

  $app->get('/z/{zone_name}', 'UserFrosting\Sprinkle\Dnsadmin\Controller\ApiController');
  $app->post('/z/{zone_name}', 'UserFrosting\Sprinkle\Dnsadmin\Controller\ApiController');
  $app->put('/z/{zone_name}', 'UserFrosting\Sprinkle\Dnsadmin\Controller\ApiController');
  $app->delete('/z/{zone_name}', 'UserFrosting\Sprinkle\Dnsadmin\Controller\ApiController');



  $app->get('/lookup/{lookup_type}/{lookup_value}', 'UserFrosting\Sprinkle\Dnsadmin\Controller\ApiController');

  $app->get('/a/{lookup_value}', 'UserFrosting\Sprinkle\Dnsadmin\Controller\ApiController');
  $app->get('/aaaa/{lookup_value}', 'UserFrosting\Sprinkle\Dnsadmin\Controller\ApiController');
  $app->get('/mx/{lookup_value}', 'UserFrosting\Sprinkle\Dnsadmin\Controller\ApiController');



});
