// JavaScript Document.
M.block_profile_field_identifier = {};
M.block_profile_field_identifier.init = function(Y) {
// Load Message on Message Body
// End Load Message.
    var ftid = Y.one('#id_ftid');
    var cid = Y.one('#id_cid');
    var showuser = Y.one("#btnajax");
    var showuser_empty_field = Y.one("#btnajax2");
    var sendnotice = Y.one("#sendnotice");
    sendnotice.hide();
    ftid.on('change', function() {
        var ftidvalue = ftid.get('value') + 'a';
        Y.io('customfield.php?id=' + ftidvalue, {
            on: {
                start: function(id, args) {
                    // userlist.hide();
                    // alert('<img src="Loading.gif" id="load-users" style="margin-left:6cm;" />');
                    // userlist.set('innerHTML','<img src="Loading.gif" id="load-users" style="margin-left:6cm;" />');
                },
                complete: function(id, e) {
                    var fid = Y.one('#id_fid');
                    var json = e.responseText;
                    console.log(json);
                    var test = json.split("^");
                    var asd = "";
                    var index = new Array('picture', 'skype', 'url', 'icq', 'aim', 'yahoo', 'msn', 'idnumber', 'institution', 'department', 'phone1', 'phone2', 'address');
                    for (i = 0; i < test.length - 1; i++)
                    {
                        var sep = test[i].split("~");
                        asd += '<option value = ' + sep[0] + '>' + sep[1] + '</option>';
                    }
                    fid.set('innerHTML', asd);
                }
            }
        });
    });
    cid.on('change', function() {
        var cidValue = cid.get('value');
        Y.io('customfield.php?id=' + cidValue, {
            on: {
                start: function(id, args) {
                    // userlist.hide();
                    // alert('<img src="Loading.gif" id="load-users" style="margin-left:6cm;" />');
                    // userlist.set('innerHTML','<img src="Loading.gif" id="load-users" style="margin-left:6cm;" />');
                },
                complete: function(id, e) {
                    var rid = Y.one('#id_rid');
                    var json = e.responseText;
                    console.log(json);
                    var test = json.split("^");
                    var asd = "";
                    for (i = 0; i < test.length - 1; i++) {
                        var sep = test[i].split("~");
                        asd += '<option value = ' + sep[0] + '>' + sep[1] + '</option>';
                    }
                    rid.set('innerHTML', asd);
                }
            }
        });
    });
    showuser_empty_field.on('click', function() {
        var id_fid = Y.one("#id_fid").get('value');
        var id_cid = Y.one("#id_cid").get('value');
        var id_rid = Y.one("#id_rid").get('value');
        var id_ftid = Y.one("#id_ftid").get('value');
        var userlist = Y.one("#table-change");
        sendnotice.show();
        // alert(showuser_empty_field.get('id'));
        Y.io('showlist.php?id_fid=' + id_fid + '&id_cid=' + id_cid + '&id_rid=' + id_rid + '&id_ftid=' + id_ftid + '&id_btn=' + showuser_empty_field.get('id'), {
            on: {
                start: function(id, args) {
                    userlist.set('innerHTML', '<img src="Loading.gif" id="load-users" style="margin-left:6cm;" />');
                },
                complete: function(id, e) {
                    var json = e.responseText;
                    console.log(json);
                    userlist.set('innerHTML', json);
                }
            }
        });
    });
    showuser.on('click', function() {
        var id_fid = Y.one("#id_fid").get('value');
        var id_cid = Y.one("#id_cid").get('value');
        var id_rid = Y.one("#id_rid").get('value');
        var id_ftid = Y.one("#id_ftid").get('value');
        var userlist = Y.one("#table-change");
        sendnotice.show();
        // alert(showuser.get('id'));
        Y.io('showlist.php?id_fid=' + id_fid + '&id_cid=' + id_cid + '&id_rid=' + id_rid + '&id_ftid=' + id_ftid + '&id_btn=' + showuser.get('id'), {
            on: {
                start: function(id, args) {
                    userlist.set('innerHTML', '<img src="Loading.gif" id="load-users" style="margin-left:6cm;" />');
                },
                complete: function(id, e) {
                    var json = e.responseText;
                    console.log(json);
                    userlist.set('innerHTML', json);
                }
            }
        });
    });
    var sendnotice = Y.one('#sendnotice');
    sendnotice.on('click', function() {
        var msg = Y.one("#id_msg").get('value');
        Y.one("#notification_msg").set('value', msg);
        msg = Y.one("#id_fid").get('value');
        Y.one("#field").set('value', msg);
    });
}