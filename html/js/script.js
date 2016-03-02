/**
 * Created by Andrew on 3/1/2016.
 */
$(document).ready(function() {

    var order = getOrder();

    // set interval
    var tid = setInterval(mycode, 2000);
    function mycode() {
        // do some stuff...
        // no need to recall the function (it's an interval, it'll loop forever)
    }
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
        type: "POST",
        success: function (response) {
            updateMessages(response.messages);
        },
        error: function () {
            console.log('An error occurred');
        }
    });
}

function getOrder() {
    return 0;
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