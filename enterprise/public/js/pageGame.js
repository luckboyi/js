$(function() {
    if (window.CppCall == undefined) {
        window.CppCall = function() {};
    }

    //切换开服切换
    $('.pageGame_container .tab li').on('click', function() {
        $(this).addClass('cur').siblings().removeClass('cur');
        $('.tab ol li').eq($(this).index()).addClass('cur').siblings().removeClass('cur');
    });
    //点击开始游戏
    $("#pageGame_container").delegate('.pageGame_detail .start_btn,.in_game', 'click', function(event) {
        var json = {};
        json.gameid = $(this).attr('game-id');
        json.gamename = $(this).attr('game-name');
        json.gameSrc = ClientConfig.webGameHost + "/servers/" + json.gameid;
        console.log(json);
        window.CppCall('webpagegameframe', 'openarea', JSON.stringify(json));
    });
    //点击新服列表进入游戏
    $('.startGame').on('click', function() {
        $(this).addClass('cur').siblings().removeClass('cur');
        var json = {};
        json.gameid = $(this).attr('game-id');
        json.areaid = $(this).attr('area-id');
        json.areaname = $(this).attr("area-name");
        json.gameSrc = ClientConfig.webGameHost + "/start/" + json.gameid + "/" + json.areaid;
        console.log(json);
        window.CppCall('webpagegameframe', 'startgame', JSON.stringify(json));
    })


    $(window).resize(function() {
        resizeFn();
    });

    //banner();
    //
    resizeFn();
})

function indexBanner() {
    $('#videoBanner').movingBoxes({
        startPanel: 1,
        hashTags: false,
        reducedSize: .5,
        wrap: true,
        buildNav: true
    });
}


//轮播
function banner() {
    //轮播
    function G(s) {
        return document.getElementById(s);
    }

    function getStyle(obj, attr) {
        if (obj.currentStyle) {
            return obj.currentStyle[attr];
        } else {
            return getComputedStyle(obj, false)[attr];
        }
    }

    function Animate(obj, json) {
        if (obj.timer) {
            clearInterval(obj.timer);
        }
        obj.timer = setInterval(function() {
            for (var attr in json) {
                var iCur = parseInt(getStyle(obj, attr));
                iCur = iCur ? iCur : 0;
                var iSpeed = (json[attr] - iCur) / 5;
                iSpeed = iSpeed > 0 ? Math.ceil(iSpeed) : Math.floor(iSpeed);
                obj.style[attr] = iCur + iSpeed + 'px';
                if (iCur == json[attr]) {
                    clearInterval(obj.timer);
                }
            }
        }, 50);
    }

    function clearAnimate() {
        clearInterval(oPicUl.timer);
        clearInterval(oListUl.timer);
    }

    var oPic = G("picBox");
    var oList = G("listBox");
    var oPrev = G("prev");
    var oNext = G("next");
    var oCenter = G('center');
    var oCenEm = oCenter.getElementsByTagName('em')[0];
    var oWidth = oCenter.offsetWidth - oCenEm.offsetWidth;
    var oPicLi = oPic.getElementsByTagName("li");
    var oListLi = oList.getElementsByTagName("li");
    var len1 = oPicLi.length;
    var len2 = oListLi.length;
    var oPicUl = oPic.getElementsByTagName("ul")[0];
    var oListUl = oList.getElementsByTagName("ul")[0];
    var w1 = oPicLi[0].offsetWidth;
    var w2 = oListLi[0].offsetWidth + 5;
    var w3 = $('#listBox').find('li.on').width();
    var oLeft = 470 / (len2 - 1);
    oPicUl.style.width = w1 * len1 + "px";
    oListUl.style.width = (w3 + 4) * len2 + "px";
    /*var oUlWidth = (w2+4) * len2;
     var oCenEmWidth = parseInt(310800/oUlWidth)+4
     oCenEm.style.width = oCenEmWidth+'px';*/
    var index = 0;

    var num = 5;
    var num2 = Math.ceil(num / 2);
    var picWidth = oListUl.offsetWidth - oPic.offsetWidth;

    function Change() {

        Animate(oPicUl, {
            left: -index * w1
        });

        if (index < num2) {
            Animate(oListUl, {
                left: 0
            });
        } else if (index + num2 <= len2) {
            Animate(oListUl, {
                left: -(index - num2 + 1) * w3
            });
        } else {
            Animate(oListUl, {
                left: -(len2 - num + 1) * w3
            });
        }

        for (var i = 0; i < len2; i++) {
            oListLi[i].className = "";
            if (i == index) {
                oListLi[i].className = "on";
                if (len2 > 4) {
                    oCenEm.style.left = oLeft * i + 'px';
                }
            }
        }
    }
    oNext.onclick = function() {
        index++;
        index = index == len2 ? 0 : index;
        Change();

    }
    oPrev.onclick = function() {
        index--;
        index = index == -1 ? len2 - 1 : index;
        Change();

    }
    for (var i = 0; i < len2; i++) {
        oListLi[i].index = i;
        oListLi[i].onclick = function() {
            index = this.index;
            Change();
        }
    }
    var disX = disY = 0;

    if (len2 < 5) {
        var oUlWidth = (w2 + 4) * len2;
        var oCenEmWidth = parseInt(310800 / oUlWidth) + 4;
        oCenEm.style.width = 518 + 'px';
    } else {
        oCenter.onmousedown = function(e) {
            var event = e || event;
            var x = event.clientX - oCenter.offsetLeft - oCenEm.offsetWidth / 2 - 270;
            x = (x < 0) ? 0 : x;
            x = (x > 470) ? 470 : x;
            oCenEm.style.left = x + 'px';
            var left = picWidth * x / 470;
            clearAnimate();
            oListUl.style.left = -left + 'px';
        }
        oCenEm.onmousedown = function(e) {

            var event = e || event;
            disX = event.clientX - oCenEm.offsetLeft;
            document.onmousemove = function(e) {
                var event = e || event;
                var x = event.clientX - disX;
                x = (x < 0) ? 0 : x;
                x = (x > 470) ? 470 : x;
                oCenEm.style.left = x + 'px';
                var left = picWidth * x / 470;
                clearAnimate();
                oListUl.style.left = -left + 'px';
            }
            document.onmouseup = function() {
                document.onmousemove = null;
                document.onmouseup = null;
            }
            e.preventDefault();
            e.stopPropagation();
        }
    }
}
//网页列表页
function resize_pageGame_list() {
    var w = $('.hot_list').width() - 40;
    //alert(w);
    if (w <= 960) {
        var a = Math.ceil((w - 80) / 5);
        var b = a / 16 * 9;
        $('.hot_list li').each(function(key) {
            $(this).width(a);
            $(this).width(a).height(b);
        })

    } else {
        var a = Math.ceil((w - 120) / 7);
        var b = a / 16 * 9;
        $('.hot_list li').each(function() {
            $(this).width(a);
            $(this).width(a).height(b);
        })
    }

};

function resizeFn() {
    var winHeight;
    if (window.innerHeight) {
        winHeight = window.innerHeight;
    } else if ((document.body) && (document.body.clientHeight)) {
        winHeight = document.body.clientHeight;
    }

    $('.pageGame_container').height(winHeight);
    $('.pageGame_con_hei').height(winHeight);
    $('.pageGame_detail .pageGame_con_hei').height(winHeight - 90);
    resize_pageGame_list();

    if (!tinyscrollbox) {
        var tinyscrollbox = [];
        var pageGame_list_scrollbar = $('#pageGame_list_scrollbar').tinyscrollbar();
        var pageGame_con_scrollbar = $('#pageGame_con_scrollbar').tinyscrollbar()
        var pageGame_left_list_scrollbar = $('#pageGame_left_list_scrollbar').tinyscrollbar()

        tinyscrollbox.push(pageGame_list_scrollbar);
        tinyscrollbox.push(pageGame_con_scrollbar);
        tinyscrollbox.push(pageGame_left_list_scrollbar);
    } else {
        $(tinyscrollbox).each(function(i, e) {
            e.update();
        });
    }

};
/*
 **领取礼包券的弹窗提示
 */
//        createHtml(0,'失败','领取失败','erro'); //失败调用数据
//        createHtml(1,'领取成功','1246633','', url);  //领取成功
function createHtml(state, title, msg, className, url) {
    var createHtml = '<div class="mask_layer success_get gift_get show">\
                                <div class="popup_window">\
                                    <div class="in_popup">\
                                        <p class="title pr"><i  class="' + className + ' pa" ></i><span class="f20">' + title + '</span></p>';
    if (state == 0) {
        createHtml += '<p>' + msg + '</p>'
    } else {
        createHtml += '<p class="">\
                                        <span  class="clearfix">\
                                        <b class="fl">礼包CDKEY：</b><i class="fl">' + msg + '</i>\
                                        </span>\
                                        </p>\
                                        <p class="clearfix"><span class="fl">礼包记录，可至“</span><a href="' + url + '" class="fl">礼包中心</a><span class="fl">"-"</span><a href="' + url + '" class="fl">我的卡包</a><span class="fl">"查看</span></p>';

    }
    createHtml += '</div>\
                                    <div class="popup_btn">\
                                        <ul class="clearfix">\
                                            <li class="fl sure" id="sureBtn" onclick="sureBtn();false;">确定</li>\
                                        </ul>\
                                    </div>\
                                </div>\
                            </div>';
    $('body').append(createHtml);
}

/*
 **关闭弹窗
 */
function sureBtn() {
    $('.gift_get').removeClass('show');
}

/*
 **操作cookies方法
 */
var cookie = {
    "setCookie": function(name, value) {
        var Days = 30;
        var exp = new Date();
        exp.setTime(exp.getTime() + Days * 24 * 60 * 60 * 1000);
        document.cookie = name + "=" + escape(value) + ";expires=" + exp.toGMTString();
    },

    "getCookie": function(name) {
        var arr, reg = new RegExp("(^| )" + name + "=([^;]*)(;|$)");
        if (arr = document.cookie.match(reg)) {
            return unescape(arr[2]);
        } else {
            return null;
        }
    },

    "delCookie": function(name) {
        var exp = new Date();
        exp.setTime(exp.getTime() - 1);
        var cval = getCookie(name);
        if (cval != null) {
            document.cookie = name + "=" + cval + ";expires=" + exp.toGMTString();
        }
    }
}