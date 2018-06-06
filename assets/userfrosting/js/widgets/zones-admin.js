
function fInt(num, length) {
    var r = "" + num;
    while (r.length < length) {
        r = "0" + r;
    }
    return r;
}

function bindZoneTableButtons(el) {

}

function getTimestamp() {
  now = new Date();

  return fInt(now.getFullYear(), 4) + fInt(now.getMonth(), 2) + fInt(now.getDate().toString(), 2) + fInt(now.getHours(), 2) + fInt(now.getMinutes(), 2) + fInt(now.getSeconds(), 2)
}

$(document).ready(function() {
  $('#zones-table').ufTable({
    dataUrl: site.uri.public + '/api/dns/zones'
  });

  $("#zones-table").on("pagerComplete.ufTable", function () {
    bindZoneTableButtons($(this));
  });

  $("#btn-create-zone").click(function() {
      $('body').ufModal({
        sourceUrl : site.uri.public + '/modals/dnsadmin/create-zone',
        msgTarget: $('#alerts-page')
      });

      $('body').on("renderSuccess.ufModal", function (data) {
        var modal = $(this).ufModal('getModal');

        var form = new Vue({
          el: '#form-zone-create',
          data: {
            "zone_type" : "normal",
            "show_examples" : false,
            "domain" : "",
            "serial_number_mode" : "timestamp",
            "serial_number" : getTimestamp(),
            "ip_examples" : [
              {
                "start" : "192.168.0.1",
                "end" : "192.168.0.254",
                "subnet" : "255.255.255.0 /24",
                "input" : "192.168.0"
              },
              {
                "start" : "74.0.0.0",
                "end" : "74.15.254.254",
                "subnet" : "255.240.0.0 /12",
                "input" : "74"
              },
              {
                "start": "2001:0db8::1",
                "end" : " 2001:db8:ffff:ffff:ffff:ffff:ffff:ffff",
                "subnet" : "/32",
                "input" : " 2001:db8"
              }
            ]
          },
          methods: {
            updateSerialNumber: function () {
                if (this.serial_number_mode == "timestamp") {
                  this.serial_number = getTimestamp();
                } else {
                  this.serial_number = 1;
                }
            }
          }
        });

        $('#form-zone-create').ufForm({
          msgTarget: $('#form-zone-alerts'),
          validator: page.validators.createZone
        }).on("submitSuccess.ufForm", function(event, data, textStatus, jqXHR) {
          window.location.reload();
        });
      });
  });


});
