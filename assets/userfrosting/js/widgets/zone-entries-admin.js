$(document).ready(function() {
  $('#zone-entries-table').ufTable({
    dataUrl: site.uri.public + '/api/dns/zones/z/'+page.zone.id+'/entries'
  });

  $("#zones-table").on("pagerComplete.ufTable", function () {
    //bindZoneTableButtons($(this));
  });

  $('#btn-create-zone-entry').click(function () {
    $('#zone-entries-table').ufModal({
      sourceUrl : site.uri.public + '/modals/dnsadmin/create-zone-entry',
      ajaxParams : {
        id : page.zone.id
      },
      msgTarget : $('#alerts-page')
    });

    $('body').on("renderSuccess.ufModal", function (data) {
      $('#form-zone-entry-create').ufForm({
        msgTarget: $('#form-zone-entry-alerts'),
        validator: page.validators.createEntry
      }).on("submitSuccess.ufForm", function(event, data, textStatus, jqXHR) {
        window.location.reload();
      });
    });
  });
});
