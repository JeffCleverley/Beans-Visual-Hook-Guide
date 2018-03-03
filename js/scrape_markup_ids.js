jQuery(document).ready(function(){

  var allDataMarkupIdValuesNodeList = document.querySelectorAll("[data-markup-id]");
  var markupIdArray = new Array();

  for ( var i = 0; i < allDataMarkupIdValuesNodeList.length; i++) {

    var markupId = allDataMarkupIdValuesNodeList[i].dataset.markupId;
    var elementByMarkupId = document.querySelectorAll("[data-markup-id='" + markupId + "']");
    markupIdArray[i] = markupId;
    elementByMarkupId[0].className += " " + markupId;
  }

  jQuery.ajax({
    type: 'POST',   // Adding Post method
    url: myAjax.ajaxurl,
    data: {
      action: "bhavhg_pass_markup_id_array",
      security: myAjax.nonce, // Including ajax file
      markup: markupIdArray,
    }
  });
});






