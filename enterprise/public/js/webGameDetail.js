/*
加载详情页
*/

(function($, template, ClientConfig, Comment) {

    template.config("openTag", "@{");
    template.config("closeTag", "}@");

    var detail = {

        appid: "",

        temp: template("detailTemp"),

        getGameType: function() {

            var _this = this;

            $.ajax({
                    url: ClientConfig.webGameHost + '/webGame/getGameType',
                    type: 'GET',
                    dataType: 'json',
                })
                .done(function(obj) {

                    _this.gameType = [];

                    if (obj.code == 0) {
                        $.each(obj.data, function(i, e) {
                            _this.gameType[e.gtid] = e.typename
                        });
                    }
                })
                .fail(function() {
                    _this.gameType = [];
                })
        },

        getGameInfo: function() {

            var _this = this;

            if (!_this.appid) {
                location.href = "";
                return false;
            }

            $.ajax({
                    url: ClientConfig.webGameHost + '/webGame/getGameInfo',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        appid: _this.appid
                    },
                })
                .done(function(obj) {
                    var data = obj.data;
                    if (data.bg) {
                        $("body").css("background-image", "url(" + data.bg + ")");
                        $("body").css("background-size", "cover")
                    } else {
                        $("body").removeAttr('style');
                    }
                    if (data.img_num) {
                        data.img_list = [];
                        var imgurl;
                        for (var i = 0; i < data.img_num; i++) {
                            imgurl = ClientConfig.webGameImgHost() + _this.appid + "/" + (i + 1) + ".jpg";
                            data.img_list.push(imgurl);
                        }
                    }
                    if (_this.gameType && _this.gameType[data.first_class]) {
                        data.first_class = _this.gameType[data.first_class]
                    }
                    if (data.score) {
                        data.score = parseFloat(data.score).toFixed(1);
                    }

                    var detailHtml = _this.temp(data);

                    $("#gameDetail").html(detailHtml);
                    $("#gameDetail").show();
                    $(".pageGame_index").hide();

                    Comment.init({
                        resource: "pc",
                    });
                    resizeFn(true);
                    banner();

                })
                .fail(function() {
                    location.href = "";
                })
        },

        init: function() {

            var _this = this;
            _this.getGameType();

            $("body").delegate('.getGift', 'click', function(event) {
                if ($(this).hasClass('disable')) {
                    return false;
                }
                window.location.href = ClientConfig.webGameHost + "/packageReceive/" + _this.appid;

            });

            $("#mainShowCon").delegate('.showWebGameDetail', 'click', function(event) {
                var _clickThis = $(this);
                appid = _clickThis.attr("appid");
                location.href = "#webgame_" + appid;
            });

            $("#mainShowCon").delegate('.getGift', 'click', function(event) {
                var _clickThis = $(this);
                if (_clickThis.hasClass("hasget")) {
                    return false;
                }
                appid = _clickThis.attr("appid");
                location.href = ClientConfig.webGameHost + "/packageReceive/" + appid;
            });


            $("#mainShowCon").append("<div id='gameDetail' style='display:none'></div>");

        }

    };

    detail.init();
    analyzeHash();

    function analyzeHash() {
        var hash = location.hash;
        var webgame = hash.split("_");
        if (webgame[0] && webgame[0] == "#webgame" && webgame[1]) {
            detail.appid = webgame[1];
            detail.getGameInfo();
        } else {
            $("body").removeAttr('style');
            $("#gameDetail").hide();
            $(".pageGame_index").show();
            indexBanner();
            resizeFn();
        }
    }

    window.onhashchange = function() {
        analyzeHash();
    };


})(jQuery, template, ClientConfig, Comment);