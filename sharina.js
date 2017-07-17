/**
 * Created by kh1m on 4/17/2017.
 */

// Dialog message
$('#registration-receipt-btn').click(function () {
    $('#registration-receipt-intro-dialog').dialog('open');
    return false;
});

$("#registration-receipt-intro-dialog").dialog({
    autoOpen: true,
    modal: true,

});

// not logged in
$("#not-logged-in").dialog({
    autoOpen:   true,
    modal:  true,
    width: 500,
    height: 150
});

$("#success-contribution").dialog({
    autoOpen:   true,
    modal:  true,
    width: 500,
    height: 220
});