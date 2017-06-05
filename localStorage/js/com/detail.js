(function($){
    var detailModule = {};
    detailModule.config = {
        htmlContainer:'body',
        type:'get',//ajaxtype => post/get
        url:'',//ajax请求的地址
        ajaxDataType:'json',//dataType为json/jsonp时，
        paramData:'',//ajax请求的
    }
    detailModule.init = function(config){
        var that = this;
        that.config = $.extend({},that.config,config);
        that.getData(that.config.type,that.config.url,that.config.dataType,that.config.paramData)
    };
    detailModule.getData = function(type,url,ajaxDataType,paramData){
        $.ajax({
            url: url,
            type: type,
            dataType: ajaxDataType,
            data: paramData,
        })
        .done(function(data) {
            console.log(data);
        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            console.log("complete");
        });

    };
    detailModule.mainContainer = function(){
        
    };
    
    window.detailModule = detailModule;
})(jQuery);