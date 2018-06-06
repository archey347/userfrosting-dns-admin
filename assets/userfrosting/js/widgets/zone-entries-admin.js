$(document).ready(function() {
  $('#zone-entries-table').ufTable({
    dataUrl: site.uri.public + '/api/dns/zones/z/'+page.zone.id+'/entries'
  });

  $("#zones-table").on("pagerComplete.ufTable", function () {
    //bindZoneTableButtons($(this));
  });
});
