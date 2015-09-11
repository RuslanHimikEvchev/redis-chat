/**
 * Created by rooty on 10.09.15.
 */
$(document).ready(function() {
    setInterval(get_session, 1000);
    setInterval(get_chatdata, 1000);
    setTimeout(scroll, 2000);
    setInterval(scroll, 3000);
    setTimeout(
        setInterval(get_rooms, 4000),
        2000);
    $("#login").click(function () {
        var name = $("#name").val();
        set_session(name);
        $(".chat").show();
        $("#user_name").hide();
    });
    $(window).keydown(function (e) {
        if (e.which == 13) {
            push();
        }
        $("#sent").click(function () {
            push();
        })
    });

    //$("#write").

});

function get_session()
{
    //var username = $("#hide_name").val();
    $.ajax({
        type: 'get',
        url: "chat.php",
        data: {session: 1},
        success: function (data) {
            //console.log(data.error);
            data = JSON.parse(data);
            if(data.error == 1)
            {
                $(".chat").hide();
                $("#user_name").show();
            }
            if(data.ok == 1)
            {
                $(".chat").show();
                $("#user_name").hide();
            }
        }
    });
}

function scroll()
{
    $('#chat_block').animate({
        scrollTop: $('#chat_block').get(0).scrollHeight}, 2000);
}

function set_session(name)
{
    $.ajax({
        type: 'get',
        url: "chat.php",
        data: {name: name},
        success: function (data) {
        }
    })
}

function get_chatdata()
{
    var div = $("#chat_block");
    $.ajax({
        type: 'get',
        url: "chat.php",
        data: {view: 1},
        success: function (data) {
            //data = JSON.stringify(data);
            data = JSON.parse(data);
            //console.log(data);
            div.empty('');
            for(var i = 0; i < data.length; i++)
            {
                var blok = '<div style="margin-left: 10px;margin-right: 10px;margin-top: 10px; margin-bottom: 10px;"><b>' + data[i].user_name + '</b>: ' + data[i].date + ' : ' + data[i].message + '</div>';
                div.prepend(blok);
            }
        }
    })
}

function push() {
    var mess = $("#write").val();
    $("#write").val('');
    $.ajax({
        type: 'get',
        url: "chat.php",
        data: {write: mess},
        success: function (data) {

        }
    })
}

function get_rooms()
{
    var rooms_block = $("#rooms");
    $.ajax({
        type: 'get',
        url: "chat.php",
        data: {rooms: 1},
        success: function(data){
            data = JSON.parse(data);
            rooms_block.empty();
            for(var i = 0; i < data.length; i++)
            {
                var block = '<div style="margin-left: 10px;margin-right: 10px;margin-top: 10px; margin-bottom: 10px;"><b>' +
                    '<input type="button" class="rooms_change" value="' + data[i] + '"></div>';
                rooms_block.prepend(block);
            }
            var script = '<script>' +
                '               $(".rooms_change").click(function(){ '+
                                'var room_name = $(this).val();' +
                                 'change_room(room_name);' +
                                    'console.log(room_name)' +
                                 '});' +
                            '</script>'
            rooms_block.prepend(script);

        }
    })
}

function change_room(room)
{
    $.ajax({
        type: 'get',
        url: 'chat.php',
        data: {change_room: room},
        success: function(data) {

        }
    })
}