<?php
/**
 * Bu kısım MVC framework'ü taklit ediyor.
 */
mysql_connect("localhost", "root", "");
mysql_select_db('misafir_defteri');

$entries = mysql_query("select * from entry order by create_date desc");
$entryArray = array();
while (false !== ($row = mysql_fetch_assoc($entries))) $entryArray[] = $row;

$template = <<<TEMPLATE
    <span>{{ name }}:</span> {{ message }}
TEMPLATE;

/**
 * MVC Template emulatoru.
 */
function template($template, array $vars = array()) {
    $_vars = array();
    array_walk($vars, function($v,$k) use (&$_vars) {
        $_vars["{{ ".$k." }}"] = $v;
    });
    return strtr($template, $_vars);
}

?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Misafir Defteri</title>
    <link rel="stylesheet" href="../style.css">
    <script src="//code.jquery.com/jquery-latest.js"></script>
    <script src="underscore.js"></script>
    <script src="backbone.js"></script>
</head>
<body>
<div>
    <h1>Misafir Defteri <?php echo time() ?></h1>
    <ul id="entries">
    <?php foreach ($entryArray as $entry): ?>
    <li><?php echo template($template, array('name' => $entry['name'], 'message' => $entry['message'])) ?></li>
    <?php endforeach ?>
    </ul>
    <a href="?add" id="add">Ekle</a>

    <form method="post" action="" style="display: <?php echo isset($_GET['add'])?'block':'none'; ?>">
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
    //Uygulama başlatılır
    var App = {

        //Uygulamanın viewları burada biriktiriliyor.
        views: {},

        //Temel uygulama ayarları
        config: function() {
            _.templateSettings = {
                interpolate : /\{\{(.+?)\}\}/g
            };
            Backbone.emulateJSON = true;
        },

        //Router'ı start eder ve Backbone history'i pushstate ile başlatır.
        start: function() {
            App.config();

            //global atama
            window.Application = new App.Router();

            Backbone.history.start({pushState: true, root: '/~fka/pozitim/backbone-present/v4/'});
        }
    };

    //Router, diğer adıyla Main Controller başlatılıyor.
    App.Router = Backbone.Router.extend({

        //Routelar tanımlanıyor.
        routes: {
            //?add pushstate'i add methodunu çalıştırıyor.
            '?add': 'add'
        },

        //constructor. new App.Router burayı çağırıyor.
        initialize: function() {

            //View'lar create ediliyor. 3 adet view var: uygulama, liste ve form.
            App.views.app      = new App.View();
            App.views.entries  = new App.EntryListView();
            App.views.form     = new App.FormView();
        },

        //add methodu, router tarafından çağırılıyor.
        add: function() {
            //form view'ını slide down et.
            App.views.form.el.slideDown(600);
        }
    });

    //Uygulama view'ı. Tüm uygulamayı body olarak varsayıyoruz.
    App.View = Backbone.View.extend({

        //Element.
        el: $('body'),

        //Eventlar tanımlanıyor.
        events: {
            //Body altındaki #add id'li elementin clickine addNew methodunu bind et.
            'click a#add': 'addNew'
        },

        //addNew methodu, pushState tetikliyor.
        addNew: function(event) {
            App.views.form.el.slideDown(600);
            Application.navigate($(event.target).attr('href'), true);
            event.preventDefault();
            return false;
        }
    });

    //Liste view'i, <ul> tagının view'i.
    App.EntryListView = Backbone.View.extend({

        //Hangi element?
        el: $('#entries'),

        sync: function() {
            console.log(arguments);
        },

        //constructor
        initialize: function() {

            //Model listesi yani Collection oluşturuluyor. Her <li> bir model olacak.
            App.Entries = new App.EntryList();

            //Model listesinin eventları
            //Add eventını addEntry methoduna yönlendiriyor. Collection'a yeni bir model eklendiğinde tetiklenecek.
            App.Entries.bind('add', this.addEntry, this);
            //Collection topluca set edildiğinde tetiklenecek. fetch methodu reset'i tetikler.
            App.Entries.bind('reset', this.addAllEntries, this);

            //Verileri her 10 saniyede bir güncelle.
            window.setInterval(function() {
                App.Entries.fetch();
            }, 10000);
        },

        //Her bir view'i prepend eder.
        renderEntry: function(view) {
            this.el.prepend(view.render().el);
        },

        //Yeni entry ekle.
        addEntry: function(entry) {

            //Model-View oluşturuyoruz.
            var view = new App.EntryView({model: entry});
            //View'i render'a yolluyoruz, o da prepend ediyor.
            this.renderEntry(view);
        },

        //Tüm entryleri topluca ekle.
        addAllEntries: function() {

            this.el.html('');
            //Tüm entryleri ekliyor.
            App.Entries.each(this.addEntry, this);
        }
    });

    //Tek bir entry'nin modeli.
    App.Entry = Backbone.Model.extend({

        //Varsayılan değerler.
        defaults: {
            name: 'İsimsiz',
            message: ''
        }

    });

    //Tek bir entry'nin view'i
    App.EntryView = Backbone.View.extend({

        //Tagın türü li.
        tagName: 'li',

        //Construct edildiğinde template oluşturulup parse ediliyor.
        initialize: function() {
            this.template = _.template($('#entry-view').html());
        },

        //Render ise MV-VM mantığında, değiştiriliyor.
        render: function() {
            //Model'den alınan parametreler ile view birleştiriliyor.
            $(this.el).html(this.template(this.model.toJSON()));
            return this;
        }
    });

    //Model collection'u
    App.EntryList = Backbone.Collection.extend({

        //Bu collection'un her bir modeli App.Entry modelidir.
        model: App.Entry,

        //Kendisini ajax.php üzerinden günceller.
        url: 'ajax.php'
    });

    //Form'un view'ı
    App.FormView = Backbone.View.extend({

        //Form elementini handle ediyor.
        el: $('form'),

        //Form inputları.
        inputs: {},

        //Eventlar tanımlanıyor
        events: {
            //Submit edildiğinde tetiklenecek method.
            'submit': 'addNew'
        },

        //constructor
        initialize: function() {
            //Inputlar belirleniyor.
            this.inputs.name = this.$('[name="name"]');
            this.inputs.message = this.$('[name="message"]');
        },

        addNew: function(event) {

            //Collection'a kendi modelinden yeni bir tane create etmesini söylüyoruz.
            App.Entries.create({
                name: this.inputs.name.val(),
                message: this.inputs.message.val()
            },{success: function(model, response) {
                //Eğer başarılı olursa form kapatılsın.
                App.views.form.el.slideUp(600).find(':input').val('');
                //Geri yönlendir.
                Application.navigate('');
            }});
            return false;
        }

    });

    //Ve her şey hazırsa, uygulama başlasın :)
    App.start();
</script>
<!-- view'ların render edildiği templateler burada -->
<script type="text/x-template" id="entry-view">
<?php echo $template ?>
</script>
</body>
</html>