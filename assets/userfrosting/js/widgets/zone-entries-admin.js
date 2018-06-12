function onEntryTypeChange() {
  if (this.value == 'NS') {
    $('#zone_caption').hide();
  } else {
    $('#zone_caption').show();
  }
}

function bindZoneEntryTableButtons(el) {
  el.find('.js-zone-entry-edit').click(function () {
    $('body').ufModal({
      sourceUrl : site.uri.public + '/modals/dnsadmin/edit-zone-entry',
      ajaxParams : {
        id : $(this).data('zoneEntry')
      },
      msgTarget : $('#alerts-page')
    });

    $('body').on("renderSuccess.ufModal", function (data) {
      $("#form-zone-entry-edit").ufForm({
        msgTarget : $('#form-zone-entry-alerts'),
        validators : page.validators.editEntry
      }).on("submitSuccess.ufForm", function(event, data, textStatus, jqXHR) {
        window.location.reload();
      });
    });
  });

  el.find('.js-zone-entry-delete').click(function () {
    $('body').ufModal({
      sourceUrl : site.uri.public + '/modals/dnsadmin/delete-zone-entry',
      ajaxParams : {
        id : $(this).data('zoneEntry')
      },
      msgTarget : $('#alerts-page')
    });

    $('body').on("renderSuccess.ufModal", function (data) {
      $("#form-zone-entry-delete").ufForm({
        msgTarget : $('#form-zone-entry-delete-alerts'),
      }).on("submitSuccess.ufForm", function(event, data, textStatus, jqXHR) {
        window.location.reload();
      });
    });
  });

}

$(document).ready(function() {
  $('#zone-entries-table').ufTable({
    dataUrl: site.uri.public + '/api/dns/zones/z/'+page.zone.id+'/entries'
  });

  $("#zone-entries-table").on("pagerComplete.ufTable", function () {
    bindZoneEntryTableButtons($(this));
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
        validators: page.validators.createEntry
      }).on("submitSuccess.ufForm", function(event, data, textStatus, jqXHR) {
        window.location.reload();
      });

      onEntryTypeChange();
    });
  });

  $('#btn-export-zone').click(function () {
    $('body').ufModal({
      sourceUrl : site.uri.public + '/modals/dnsadmin/export-zone',
      ajaxParams : {
        id : page.zone.id
      },
      msgTarget : $('#alerts-page')
    });

    $('body').on("renderSuccess.ufModal", function (data) {

      $('#form-export-zone').ufForm({
        msgTarget: $('#form-export-zone-alerts'),
      }).on("submitSuccess.ufForm", function(event, data, textStatus, jqXHR) {
        window.location.reload();
      });


    });
  });
});
