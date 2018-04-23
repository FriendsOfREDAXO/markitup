// Url-Parameter bereitstellen
function getUrlParameter(sParam, url) {
  var sPageURL = window.location.search.substring(1);
  if (url) sPageURL = url;
  var sURLVariables = sPageURL.split("&");
  for (var i = 0; i < sURLVariables.length; i++) {
    var sParameterName = sURLVariables[i].split("=");
    if (sParameterName[0] == sParam) {
      return sParameterName[1];
    }
  }
  return "";
}

// Back/Next Browser Button
window.onpopstate = function (e) {
  var character = e.state;
  if (character == null) {
    $document = getUrlParameter("document_file", document.location.href);
    if ($document == "") {
      elem = document.createElement("a");
      elem.setAttribute(
        "href",
        document.location.href + "&document_file=main_intro.md"
      );
      load_doc(elem, false);
    }
  } else {
    elem = document.createElement("a");
    elem.setAttribute("href", document.location.href);
    load_doc(elem, false);
  }
};

// Dokument per Ajax laden
function load_doc(elem, dopush) {
  $("div.addon_documentation-navi li").removeClass("current");
  $("div.addon_documentation-navi a").removeClass("current");
  $(elem).addClass("current");
  $(elem).parent().addClass("current");
  $("html, body").animate({
    scrollTop: 0
  }, 200);

  $url = $(elem).attr("href") + "&ajax=true";
  $target = $("div.addon_documentation-content").find("div.panel-body");
  $doctitle = $("div.addon_documentation-content").find("#doc-file-name");
  $document = getUrlParameter("document_file", $url);
  $($doctitle).html($document);

  $($target).html(
    '<div style="text-align:center;color:#9ba4b1;"><i class="fa fa-cog fa-spin fa-2x fa-fw"></i></div>'
  );

  $.ajax({
    type: "GET",
    url: $url,
    cache: true,
    dataType: "html",
    success: function (response) {
      $($target).html(response);
      // Externe Links in neuem Fenster
      $("section.addon_documentation")
        .find("a")
        .each(function () {
          var a = new RegExp("/" + window.location.host + "/");
          if (!a.test(this.href) && this.href) {
            $(this).addClass("extern");
            $(this).click(function (event) {
              event.preventDefault();
              event.stopPropagation();
              window.open(this.href, "_blank");
            });
          }
        });
      // Bei internen Links Dokument laden
      $("div.addon_documentation-content a.doclink").on("click", function (
        event,
        container
      ) {
        if (!$(this).hasClass("extern")) {
          load_doc($(this), true);
        }
        event.preventDefault();
        return false;
      });
      // Link in der Navigaton hervorheben
      $("div.addon_documentation-navi")
        .find('a[href$="' + $document + '"]')
        .each(function () {
          if ($(this).html() != "") {
            $(this).addClass("current");
            $(this).parent().addClass("current");
          }
        });
      if (dopush) {
        history.pushState($document, $document, $(elem).attr("href"));
      }
    }
  }).fail(function (jqXHR, textStatus) {
    $($target).html(
      '<div class="alert alert-danger">AJAX-Error: ' + textStatus + '<br>' + $url + "</div>"
    );
  });
}

$(document).on("rex:ready", function (event, container) {
  // Externe Links in neuem Fenster
  $("section.addon_documentation")
    .find("a")
    .each(function () {
      var a = new RegExp("/" + window.location.host + "/");
      if (!a.test(this.href) && this.href) {
        $(this).addClass("extern");
        $(this).click(function (event) {
          event.preventDefault();
          event.stopPropagation();
          window.open(this.href, "_blank");
        });
      }
    });

  // Links in der Navigation, interne Links in Dokumenten
  $(
    "div.addon_documentation-navi a, div.addon_documentation-content a.doclink"
  ).on("click", function (event, container) {
    if (!$(this).hasClass("extern")) {
      load_doc($(this), true);
    }
    event.preventDefault();
    return false;
  });
  // Link in Navigation hervorheben
  $("div.addon_documentation-navi a.current").each(function () {
    $(this).parent().addClass("current");
  });

  // Language select
  $("#doclang").change(function () {
    this.form.submit();
  });

}); // end rex:ready
