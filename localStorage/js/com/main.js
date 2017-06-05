$(function(){
    //点击右侧设置
    $('.plateform_btn').on('click','li',function(){
        // alert(1);
        if($(this).hasClass('windowclose_icon')){
            PL.callFun('mainframe', 'close','');
        }else if($(this).hasClass('max_icon')){
            PL.callFun('common', 'max','');

        }else if($(this).hasClass('min_icon ')){
            PL.callFun('common', 'min','');
        }else if($(this).hasClass('vrtools_icon ')){
            PL.callFun('mainframe', 'showhidetool', '');
        }else if($(this).hasClass('vrbtn_icon')){
            PL.callFun('mainframe', 'vrBtn','');
        }
    });
    $('.left_plateform_btn').on('click','',function(){

    });

    $(".search_text").keypress(function() {
        if(event.keyCode==13) {
            search($(this).next())
        }
    });
    $('.back_icon').click(function(){
         window.history.go(-1);
    })
    $('.refresh_icon').click(function(){
       window.location.reload();
    })
    //点击搜索
    $('.header ').on('click','.search_icon',function(){
        search($(this))
    });
    $('.search_con').on('click','span',function(e){
        $('.search_con').find('ul').slideToggle(100);
        e.stopPropagation()
        e.preventDefault()
    });
    $('.search_con').on('click','li',function(){
        $(this).parents('ul').slideUp(100);
        var txt = $(this).html();
        $('.search_con').find('span b').text(txt);
        $(this).addClass('cur').siblings().removeClass('cur');
    })
    $(window).on('click',function(){
        $('.search_con').find('ul').slideUp(100);
        $('.plateform_btn .set_icon').find('ol').hide();        
    });
    //设置
    $('.close_mask').on('click',function(){
        $(this).parents('.mask').hide();
    })
    $('.plateform_btn').on('click','.set_icon',function(e){
        $(this).find('ol').toggle();
        e.stopPropagation();
        e.preventDefault();
    });
    
    $('.plateform_btn').on('click','ol li',function(){
        var i = $(this).index();
        //console.log(i)
        if(i == 0){
            $('.set_mask').show();
        }else if(i ==1){
            $('.update_platform').show();
            $('.suc_update').show();
        }else if(i ==2){
            $('.add_game').show();
        }else if(i == 3){
            PL.callFun('mainframe', 'requestversion', '')
        }
    })
    $('.set_language_con').on('click',function(e){
        $('.language_setlist ').toggle();
        e.stopPropagation()
        e.preventDefault()
    })
    $('.language_setlist').on('click','p',function(e){
        var text = $(this).html();
        $('.set_language_con').find('b').html(text);
        $(this).parents('.language_setlist').hide();
    })
    $('.set_mask .set_head').on('click','span',function(){
        var i = $(this).index();
        $(this).addClass('cur').siblings().removeClass('cur');
        $('.set_mask').find('.set_body ').eq(i).addClass('cur').siblings().removeClass('cur');
        if($('.set_nav').find('.vr_game_list.cur').hasClass('wow')){
            $('.set_mask').find('.set_body .wow_set_con').show();
            $('.set_mask').find('.set_body .ow_set_con').hide()
        }else{
            $('.set_mask').find('.set_body .wow_set_con').hide();
            $('.set_mask').find('.set_body .ow_set_con').show()
        }
    });
    $('.set_mask').on('click','.vr_game_list',function(){
        $(this).addClass('cur').siblings().removeClass('cur');
        $(this).parents('li').addClass('cur').siblings().removeClass('cur');
        if($(this).hasClass('wow')){
             $('.set_mask').find('.set_body .wow_set_con').show();
            $('.set_mask').find('.set_body .ow_set_con').hide()
        }else{
            $('.set_mask').find('.set_body .wow_set_con').hide();
            $('.set_mask').find('.set_body .ow_set_con').show()
        }
    });
    $('.set_nav').on('click','li',function(){
        $(this).addClass('cur').siblings().removeClass('cur');
        var i = $(this).index();
        $(this).parents('.set_mask').find('ol li').eq(i).addClass('cur').siblings().removeClass('cur');    
    })

    $(".control_settting  .show_vrDeploy input").on('blur',function(){
        	checkName(this);
            //console.log(1)
    });
    $(".vr_insetting  .show_vrDeploy input").on('blur',function(){
        	checkVrName(this);
            //console.log(1)
    });
    //设置
    $('body').on('click','.set_mask .browse',function(){
       PL.callFun('mainframe', 'set_modify','');
    })
    $('body').on('click','.set_mask .open',function(){
        PL.callFun('mainframe', 'set_open','');
    })
    $('body').on('click','.set_mask .default_set',function(){
        if($(this).parents('.mask').find('.set_nav li.cur').index() == 0){
            PL.callFun('mainframe', 'set_restore','');
        }else{
            if($('.set_mask .set_head').find('span').eq(0).hasClass('cur')){  
                PL.callFun('mainframe','resetkeyset','common');
            }else{
                PL.callFun('mainframe','resetkeyset','gamewarworld');                
            }
        }
    })
    $('body').on('click','.set_mask .sure_set',function(){
        $(this).parents('.mask').hide();        
        if($(this).parents('.mask').find('.set_nav li.cur').index() == 0){
            var sure_ste ={
              'languageState':'',
              'serviceState':'',
              'autoState':'',
              'openVronline':'',
              'dir':'D:/'
          };
          if( $('.equipmentauto_set').find('.slect_con').eq(0).hasClass('cur')){
               sure_ste.serviceState =1;
          }else{
               sure_ste.serviceState =0;
          }
            if($('.close_btn_set').find('.slect_con').eq(0).hasClass('cur')){
                sure_ste.autoState =1;
            }else{
                sure_ste.autoState =0;
            }
            if($('.openVRonline_set').find('.slect_con').eq(0).hasClass('cur')){
                sure_ste.openVronline =1;
            }else{
                sure_ste.openVronline =0;
            }
             if($('.close_plateform').find('.slect_con').eq(0).hasClass('cur')){
                sure_ste.closeplatselected =1;
            }else{
                sure_ste.closeplatselected =0;
            }
            sure_ste.dir = $('.setway_con input').val();
            PL.callFun('mainframe', 'set_confirm',JSON.stringify(sure_ste));
        }else{
            var json = {
                    openset:'122'
                  };
            json.openset     = $('.control_settting').find('.openset').val()
            json.closerview  = $('.control_settting').find('.closerview').val()
            json.lenspull    = $('.control_settting').find('.lenspull').val()
            json.openhandle  = $('.control_settting').find('.openhandle').val()
            json.ctrlmouse   = $('.control_settting').find('.ctrlmouse').val()
            json.ctrlview    = $('.control_settting').find('.ctrlview').val()
            if($('.set_mask .set_head').find('span').eq(0).hasClass('cur')){
                PL.callFun('mainframe', 'savecommonkeyset',JSON.stringify(json));
            }else{

            }
            if($('.set_mask .wow').hasClass('cur')){
                var json = {
                    gamewarworld:{

                    }
                };
                json.gamewarworld.A = $('.gameSetting ').eq(0).find('input').val()
                json.gamewarworld.B = $('.gameSetting ').eq(1).find('input').val()
                json.gamewarworld.X = $('.gameSetting ').eq(2).find('input').val()
                json.gamewarworld.Y = $('.gameSetting ').eq(3).find('input').val()
                json.gamewarworld.LB = $('.gameSetting ').eq(4).find('input').val()
                json.gamewarworld.RB = $('.gameSetting ').eq(5).find('input').val()
                json.gamewarworld.LT = $('.gameSetting ').eq(6).find('input').val()
                json.gamewarworld.RT = $('.gameSetting ').eq(7).find('input').val()
                PL.callFun('mainframe','savegamekeyset',JSON.stringify(json));
            }
        }
    })
    $('body').on('click','.set_mask .cancel_set',function(){
        $(this).parents('.mask').hide();
    })
    //游戏快捷键设置
        $(".set_vr_con .show_vrDeploy input").keydown(function(event){
		  	var e = event || window.event || arguments.callee.caller.arguments[0];
		  	this.value=$(this).val();
            var keyName;
            //console.dir(e.keyCode)
            switch(e.keyCode){
				case 9:keyName="Tab";$(this).attr('keyCode',9);break;
				case 13:keyName="Enter";$(this).attr('keyCode',13);break;
				case 16:keyName="Shift";$(this).attr('key',16);break;
				case 17:keyName="Ctrl" ;$(this).attr('key',17);break;
				case 18:keyName="Alt";$(this).attr('key',18);break;
				case 19:keyName="PauseBreak";$(this).attr('keyCode',19);break;
				case 20:keyName="Caps Lock";$(this).attr('keyCode',20);break;
				case 27:keyName="Esc";$(this).attr('keyCode',27);break;
				case 32:keyName="空格";$(this).attr('keyCode',32);break;
				case 33:keyName="PageUp";$(this).attr('keyCode',33);break;
				case 34:keyName="PageDown";$(this).attr('keyCode',34);break;
				case 35:keyName="End";$(this).attr('keyCode',35);break;
				case 36:keyName="Home";$(this).attr('keyCode',36);break;
				case 37:keyName="left";$(this).attr('keyCode',37);break;
				case 38:keyName="up";$(this).attr('keyCode',38);break;
				case 39:keyName="right";$(this).attr('keyCode',39);break;
				case 40:keyName="down";$(this).attr('keyCode',40);break;
				case 45:keyName="Insert";$(this).attr('keyCode',45);break;
				case 46:keyName="Delete";$(this).attr('keyCode',46);break;
				case 19:keyName="PauseBreak";break;
				case 20:keyName="Caps Lock";break;
				case 27:keyName="Esc";break;
				case 32:keyName="空格";break;
				case 33:keyName="PageUp";break;
				case 34:keyName="PageDown";break;
				case 35:keyName="End";break;
				case 36:keyName="Home";break;
				case 37:keyName="left";break;
				case 38:keyName="up";break;
				case 39:keyName="right";break;
				case 40:keyName="down";break;
				case 45:keyName="Insert";break;
				case 46:keyName="Delete";break;
				case 19:keyName="PauseBreak";break;
				case 20:keyName="Caps Lock";break;
				case 27:keyName="Esc";break;
				case 33:keyName="PageUp";break;
				case 34:keyName="PageDown";break;
				case 35:keyName="End";break;
				case 36:keyName="Home";break;
				case 37:keyName="left";break;
				case 38:keyName="up";break;
				case 39:keyName="right";break;
				case 40:keyName="down";break;
				case 45:keyName="Insert";break;
				case 46:keyName="Delete";break;
				case 65:keyName='A';$(this).attr('keyCode',65);break;
                case 66:keyName='B';$(this).attr('keyCode',66);break;
                case 67:keyName='C';$(this).attr('keyCode',67);break;
                case 68:keyName='D';$(this).attr('keyCode',68);break;
                case 69:keyName='E';$(this).attr('keyCode',69);break;
                case 70:keyName='F';$(this).attr('keyCode',70);break;
                case 71:keyName='G';$(this).attr('keyCode',71);break;
                case 72:keyName='H';$(this).attr('keyCode',72);break;
                case 73:keyName='I';$(this).attr('keyCode',73);break;
                case 74:keyName='J';$(this).attr('keyCode',74);break;
                case 75:keyName='K';$(this).attr('keyCode',75);break;
                case 76:keyName='L';$(this).attr('keyCode',76);break;
                case 77:keyName='M';$(this).attr('keyCode',77);break;
                case 78:keyName='N';$(this).attr('keyCode',78);break;
                case 79:keyName='O';$(this).attr('keyCode',79);break;
                case 80:keyName='P';$(this).attr('keyCode',80);break;
                case 81:keyName='Q';$(this).attr('keyCode',81);break;
                case 82:keyName='R';$(this).attr('keyCode',82);break;
                case 83:keyName='S';$(this).attr('keyCode',83);break;
                case 84:keyName='T';$(this).attr('keyCode',84);break;
                case 85:keyName='U';$(this).attr('keyCode',85);break;
                case 86:keyName='V';$(this).attr('keyCode',86);break;
                case 87:keyName='W';$(this).attr('keyCode',87);break;
                case 88:keyName='X';$(this).attr('keyCode',88);break;
                case 89:keyName='Y';$(this).attr('keyCode',89);break;
                case 90:keyName='Z';$(this).attr('keyCode',90);break;
				case 91:keyName="左Win";$(this).attr('keyCode',91);break;
				case 92:keyName="右Win";$(this).attr('keyCode',92);break;
				case 93:keyName="快捷菜单键";$(this).attr('keyCode',93);break;
				case 95:keyName="Sleep";$(this).attr('keyCode',95);break;
				case 96:keyName="小键盘区0";$(this).attr('keyCode',96);break;
				case 97:keyName="小键盘区1";$(this).attr('keyCode',97);break;
				case 98:keyName="小键盘区2";$(this).attr('keyCode',98);break;
				case 99:keyName="小键盘区3";$(this).attr('keyCode',99);break;
				case 100:keyName="小键盘区4";$(this).attr('keyCode',100);break;
				case 101:keyName="小键盘区5";$(this).attr('keyCode',101);break;
				case 102:keyName="小键盘区6";$(this).attr('keyCode',102);break;
				case 103:keyName="小键盘区7";$(this).attr('keyCode',103);break;
				case 104:keyName="小键盘区8";$(this).attr('keyCode',104);break;
				case 105:keyName="小键盘区9";$(this).attr('keyCode',105);break;
				case 106:keyName="*";$(this).attr('keyCode',106);break;
				case 107:keyName="+";$(this).attr('keyCode',107);break;
				case 109:keyName="-";$(this).attr('keyCode',109);break;
				case 110:keyName=".";$(this).attr('keyCode',110);break;
				case 111:keyName="/";$(this).attr('keyCode',111);break;
				case 112:keyName="F1";$(this).attr('keyCode',112);break;
				case 113:keyName="F2";$(this).attr('keyCode',113);break;
				case 114:keyName="F3";$(this).attr('keyCode',114);break;
				case 115:keyName="F4";$(this).attr('keyCode',115);break;
				case 116:keyName="F5";$(this).attr('keyCode',116);break;
				case 117:keyName="F6";$(this).attr('keyCode',117);break;
				case 118:keyName="F7";$(this).attr('keyCode',118);break;
				case 119:keyName="F8";$(this).attr('keyCode',119);break;
				case 120:keyName="F9";$(this).attr('keyCode',120);break;
				case 121:keyName="F10";$(this).attr('keyCode',121);break;
				case 122:keyName="F11";$(this).attr('keyCode',122);break;
				case 123:keyName="F12";$(this).attr('keyCode',123);break;
				case 144:keyName="NumLock";$(this).attr('keyCode',144);break;
				case 145:keyName="ScrollLock";$(this).attr('keyCode',145);break;
				case 186:keyName=";";$(this).attr('keyCode',186);break;
				case 187:keyName="=";$(this).attr('keyCode',187);break;
				case 188:keyName=",";$(this).attr('keyCode',188);break;
				case 189:keyName="-";$(this).attr('keyCode',189);break;
				case 190:keyName=".";$(this).attr('keyCode',190);break;
				case 191:keyName="/";$(this).attr('keyCode',191);break;
				case 192:keyName="`";$(this).attr('keyCode',192);break;
				case 219:keyName="[]";$(this).attr('keyCode',219);break;
				case 220:keyName="\\";$(this).attr('keyCode',220);break;
				case 221:keyName="]";$(this).attr('keyCode',221);break;
				case 222:keyName="'";$(this).attr('keyCode',222);break;
				case 255:keyName="Wake";$(this).attr('keyCode',255);break;
				default:keyName='';break;
            }
			if((e.shiftKey)&&(e.keyCode!=16)){
            	keyName="Shift + "+ keyName;
            	$(this).attr("value",keyName);
            	keyName=keyName.substring(0,keyName.length-1);
            }
            if((e.altKey)&&(e.keyCode!=18)){keyName = "Alt + "+ keyName; $(this).attr("value",keyName);}
            if((e.ctrlKey)&&(e.keyCode!=17)){keyName ="Ctrl + "+ keyName; $(this).attr("value",keyName);}
            if(!(e.ctrlKey)&&!(e.altKey)&&!(e.shiftKey)&&(e.keyCode!=37)&&(e.keyCode!=38)&&(e.keyCode!=39)&&(e.keyCode!=40)&&(e.keyCode!=33)&&(e.keyCode!=34)&&(e.keyCode!=35)&&(e.keyCode!=36)&&(e.keyCode!=45)&&(e.keyCode!=46)&&(e.keyCode!=9)&&(e.keyCode!=20)){
            	$(this).attr("value",keyName);
              if(e.keyCode > 64 && e.keyCode < 91){
                keyName=keyName.substring(0,keyName.length-1);

              }
            }
            this.value= keyName;
		});
})

function setDateFn(data){
    $(data).each(function(key,val){
        if(val.languageState == 1033){
            $('.set_language_con b').html('英文')
        }else{
            $('.set_language_con b').html('中文')
        };
        if(val.serviceState == 1){
            $('.equipmentauto_set').find('.slect_con').eq(0).addClass('cur').siblings().removeClass('cur');
        }else{
            $('.equipmentauto_set').find('.slect_con').eq(1).addClass('cur').siblings().removeClass('cur');
        };
        if(val.autoState == 1){
            $('.close_btn_set').find('.slect_con').eq(0).addClass('cur').siblings().removeClass('cur');
        }else{
            $('.close_btn_set').find('.slect_con').eq(1).addClass('cur').siblings().removeClass('cur');
        };
        if(val.openVronline == 1){
            $('.openVRonline_set').find('.slect_con').eq(0).addClass('cur').siblings().removeClass('cur');
        }else{
            $('.openVRonline_set').find('.slect_con').eq(1).addClass('cur').siblings().removeClass('cur');
        };
        if(val.closeplatselected == 1){
            $('.close_plateform').find('.slect_con').eq(0).addClass('cur').siblings().removeClass('cur');
        }else{
            $('.close_plateform').find('.slect_con').eq(1).addClass('cur').siblings().removeClass('cur');
        };
        $('.setway_con input').val(val.dir)
    });   
    $('.set_mask ').show();
}

function showVersion(data){
    $('.aboutUs_con').show().find('.version_con b').html(data.version);
}

//判断对应按键值不能重复
function checkName(data){
    var values="";
    $(".control_settting  input").each(function(i,item){
        var value=$(this).val().toUpperCase();
        values+=value; //获取所有的名称
    });
    var val=$(data).val().toUpperCase(); //获得当前输入框的值
    //判断当前输入框的值
    var newValue=values.replace(val,""); //去除当前输入框的值
    if(newValue==""){
        return false;
    }else{
        if(val == ''){
            $(data).next('span').removeClass('hide').text('按键不能为空');
        }else{
            if(newValue.indexOf(val)>-1){  //当前值和newValue作比较
                $(data).next('span').removeClass('hide').text('按键被占用');
            }else{
                $(data).next('span').addClass('hide');
            }
        }
    }
}
function checkVrName(data){
    var values="";
    $(".vr_insetting  input").each(function(i,item){
        var value=$(this).val().toUpperCase();
        values+=value; //获取所有的名称
    });
    var val=$(data).val().toUpperCase(); //获得当前输入框的值
    console.log(val)
    //判断当前输入框的值
    var newValue=values.replace(val,""); //去除当前输入框的值
    if(newValue==""){
        return false;
    }else{
        if(val == ''){
            $(data).next('span').removeClass('hide').text('按键不能为空');
        }else{
            if(newValue.indexOf(val)>-1){  //当前值和newValue作比较
                $(data).next('span').removeClass('hide').text('按键被占用');
            }else{
                $(data).next('span').addClass('hide');
            }
        }
    }
}
function search(obj) {
    var val = obj.prev('input').val();
    var typeid = obj.parents('.search_con').find('ul li.cur').attr('typeid');
    if(val !=''){
        if(typeid == 1){
            $('#main').attr('src','http://www.vronline.com/vrhelp/searchGame?name='+val+'&page=1')
        }else if(typeid == 2){
            $('#main').attr('src','http://www.vronline.com/vrhelp/searchVideo?name='+val+'&page=1')
        }
    }else{
    }   
}


function vrOpen(){
    $('.plateform_btn').find('.vrbtn_icon').addClass('cur');
}
function vrClose(){
    $('.plateform_btn').find('.vrbtn_icon').removeClass('cur');    
}
//次校准开启
function correctOpen(){
    $('body').find('.vrtools_icon').addClass('cur');
}
//次校准关闭
function correctClose(){
    $('body').find('.vrtools_icon').removeClass('cur');
}




