$(function(){
  
	var dev_users =  db.get('dev_users')
	 if( typeof(dev_users)=="undefined") {
        $.get("/data/devusers",function(res){
            if(res.code==0) {
                db.set('dev_users',res.data,300);
                dev_users = res.data;
                uploadDevUsers();
            }
        },"json")
    } else {
        uploadDevUsers();
    }

    function uploadDevUsers() {
		$(".dev_user").each(function(){
			var id = $(this).attr('data-val');
            if(typeof(dev_users[id])!="undefined") {
                $(this).html(dev_users[id].name)
            }
		})
    }	
})



var db = {
     _pre:"forire",
     set: function(a, b,expire) {
        if(typeof(expire)=="undefined") {
            expire = 31536e6
        } else {
            expire = parseInt(expire)
        }
        var c = new Date;
        if (window.localStorage && this.isLocalStorageNameSupported()==true) {
            var _set = JSON.stringify({data:b,expire:c.getTime()+expire*1000})
            window.localStorage.setItem(this._pre + a, _set);
        } else {
            var c = new Date;
            c.setTime(c.getTime() + expire), document.cookie = this._pre + a + "=" + escape(b) + ";expires=" + c.toGMTString()
        }
    },
    get: function(a) {
        if (window.localStorage && this.isLocalStorageNameSupported()==true)  {
            var val = window.localStorage.getItem(this._pre + a);
            if(val) {
                var res = JSON.parse(val)
                var c = new Date;
                if(res.expire>c.getTime()) {
                    return res.data
                } else {
                    this.remove(a)
                }
            }
            return 
        } else {
            var b = document.cookie.match(new RegExp("(^| )" + this._pre + a + "=([^;]*)(;|$)"));
            return null != b ? unescape(b[2]) : null
        }
    },
    remove: function(a) {
        if (window.localStorage) {
            window.localStorage.removeItem(this._pre + a)
        } else {
            var b, c;
            b = new Date
            b.setTime(b.getTime() - 1)
            c = this.get(a)
            if (null != c) {
                document.cookie = this._pre + a + "=" + c + ";expires=" + b.toGMTString()
            }
        }
    },
    isLocalStorageNameSupported:function() {
        var testKey = 'localtest', storage = window.localStorage;
        try {
            storage.setItem(testKey, '1');
            storage.removeItem(testKey);
            return true;
        } catch (error) {
            return false;
        }
    }
}