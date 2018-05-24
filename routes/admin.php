<?php

$app->group('/dnsadmin', function () use ($app) {
  $app->get('', 'UserFrosting\Sprinkle\Dnsadmin\Controller\AdminController:pageAdmin');
});
