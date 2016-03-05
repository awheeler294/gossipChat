/**
 * Created by Andrew on 3/1/2016.
 */
var order = 0;
$(document).ready(function() {

    getOrder();

    // set interval
    var tid = setInterval(pollForMessages, 500);

    function abortTimer() { // to be called when you want to stop the timer
        clearInterval(tid);
    }

    $("#send-message-form").submit(sendChatMessage);

});

function sendChatMessage(event) {
    event.preventDefault();

    var url = "/gossip/html/ajax.php?function=saveMessage";
    var messageOrder = order;
    var text = $('#chat-input').val();
    $('#chat-input').val('');

    order++;

    $.ajax({
        url: url,
        data: {text: text, order: messageOrder},
        dataType: "json",
        type: "POST",
        success: function (response) {
            updateMessages(response.messages);
        },
        error: function () {
            console.log('An error occurred');
        }
    });
}

function pollForMessages() {
    var url = "/gossip/html/ajax.php?function=pollForMessages";

    $.ajax({
        url: url,
        data: {},
        dataType: "json",
        type: "GET",
        success: function (response) {
            updateMessages(response.messages);
        },
        error: function () {
            console.log('An error occurred');
        }
    });
}

function getOrder() {

    var url = "/gossip/html/ajax.php?function=getOrder";

    $.ajax({
        url: url,
        data: {},
        dataType: "json",
        type: "GET",
        success: function (response) {
            order = response.order;
        },
        error: function () {
            console.log('An error occurred');
        }
    });
}

function updateMessages(messages) {
    var messageHTML = '';
    $(messages).each(function() {
        messageHTML += '<div class="message">' +
                            '<strong>' + this.Originator + ': </strong>'  + this.Text +
                        '</div>';
    });
    $('.messages-area').html(messageHTML);
}