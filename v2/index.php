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
    <a href="javascript:add()">Ekle</a>
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
    //Ajax.php'den tüm veriyi load ediyoruz.
    $('#entries').load('ajax.php');

    //Form'un submit'ini set ediyoruz.
    $('form').submit(function(e) {
        var that = this;
        $.post('ajax.php', $(this).serialize(), function(data) {
            $('#entries').html(data);
            $('form').slideUp(300).find(':input').val('');
        });
        e.preventDefault();
        return false;
    });
    function add() {
        $('form').slideDown(600);
    }
</script>
</body>
</html>