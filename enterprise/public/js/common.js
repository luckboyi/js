/* common.js */
(function(window) {
    var myDialog = {
        dialogModel:[],
        alert: function(msg, callback) {
            var _this = this;

            var config = {
                title: '提示',
                content: '<span style="margin:0 50px; text-align: center;">' + msg + '</span>',
                okValue: '确认',
                cancelDisplay:false,
                ok: function() {},
                quickClose: true
            };

            if (typeof callback == "function") {
                config.ok = callback;
                config.cancel=callback;
            }

            _this.dialog(config);
        },
        confirm: function(obj) {

            if (typeof obj != "object") {
                var obj = {};
            }

            var _this = this;

            var config = {
                title: '确认',
                cancelValue: '取消',
                okValue: '确认',
                content: '<span style="margin:0 50px; text-align: center;">确认</span>',
                ok: function() {},
                cancel: function() {}
            }

            if (obj.content) {
                config.content = '<span style="margin:0 50px; text-align: center;">' + obj.content + '</span>';
            }

            if (obj.ok && typeof obj.ok == "function") {
                config.ok = obj.ok;
            }

            if (obj.cancel && typeof obj.cancel == "function") {
                config.cancel = obj.cancel;
            }

            if (obj.id) {
                config.id = obj.id;
            }

            _this.dialog(config);

        },
        loading: function() {

            var _this = this;

            var config = {
                title: '请稍等...',
                content: '<span style="margin:10px 40px;" class="ui-dialog-loading">Loading..</span>',
                ok:false,
                cancelDisplay:false,
                cancel: function(){
                    return false;
                }
            };

            _this.dialog(config);

        },
        dialog:function(obj){

            var _this=this;

            var config={
                id:"myDialog",
                cancelValue: '取消',
                okValue: '确认',
                content: '<span style="margin:0 50px; text-align: center;">确认</span>',
                ok: function() {},
                cancel: function() {}
            };

            if (typeof obj != "object") {
                var obj = {};
            }

            obj=$.extend(config, obj);

            if (_this.dialogModel[obj.id]) {
                _this.dialogModel[obj.id].close().remove();
            }

            _this.dialogModel[obj.id] = dialog(obj);

            _this.dialogModel[obj.id].showModal();
        }
    };

    window.myDialog = myDialog;

})(window);
