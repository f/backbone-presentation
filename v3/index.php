<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Misafir Defteri</title>
    <link rel="stylesheet" href="../style.css">
    <script src="//code.jquery.com/jquery-latest.js"></script>
</head>
<body>
<div>
    <h1>Misafir Defteri <?php echo time() ?></h1>
    <ul id="entries">Yükleniyor...</ul>
    <a href="#add">Ekle</a>
    <form method="post" action="" style="display: none;">
        <table>
            <tr>
                <td>İsim:</td>
                <td><input name="name" type="text"></td>
            </tr>
            <tr>
                <td>Mesaj:</td>
                <td><textarea name="message"></textarea></td>
            </tr>
            <tr>
                <td></td>
                <td><button type="submit">Gönder</button></td>
            </tr>
        </table>
    </form>
</div>
<script>
    function loadData(data) {
        $('#entries').html('');
        for (var i in data) {
            $('#entries').append(
                $('<li/>').html('<span>'+data[i].name + '</span>: ' + data[i].message)
            );
        }
    }
    $.get('ajax.php', {}, function(data) {
        loadData(data);
    }, 'json');

    $('form').submit(function(e) {
        var that = this;
        var _data = $(this).serializeArray();
        $.post('ajax.php', $(this).serialize(), function(data) {
            var d = new Date();
            $('#entries').prepend(
                $('<li/>').html('<span>'+_data[0].value + ':</span> ' + _data[1].value)
            );
            $('form').slideUp(300).find(':input').val('');
o        });
        e.preventDefault();
        return false;
    });
    function check() {
        if (location.hash == '#add')
            $('form').slideDown(600);
    }
    check();
    window.addEventListener('hashchange', check);
</script>
</body>
</html>