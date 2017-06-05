/**
 * 平台整体js规划
 */
(function(w) {
    var platform = {
        init: function() {
            if (typeof w.CppCall == "function") {
                this.Platform = "pc";
            } else {
                this.Platform = "web";
            }
        },
        callFun: function(frameType, action, json) {
            if (!frameType || !action) {
                return false;
            }
            if (!json) {
                json = null;
            }
            if (this.Platform == "pc") {
                w.CppCall(frameType, action, json);
            } else {
                if (typeof this[frameType + "_" + action] == "function") {
                    this[frameType + "_" + action](json);
                }
            }
            return true;
        },
        clientCall(a,b,json,callback) {
            if(typeof(w.CppCall)!="function") {
                return
            }
            if(typeof(callback)!="function") {
                var cb = function(e){
                    w.removeEventListener(a+"_"+b,cb)
                    callback(e.detail)
               }
               w.addEventListener(a+"_"+b,cb)
               // setTimeout(function(){
               //       window.removeEventListener(a+b,cb)
               // },3000)
            }
            if(typeof(json)!="string") {
                json = JSON.stringify(json)
            }
            w.CppCall(a,b,json)
        },
        common_close: function() {

        }
    }
    platform.init();
    window.PL = platform;
})(window);

//PL.callFun('common','close');



