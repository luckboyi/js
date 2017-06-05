/*互调函数*/
function MainFn(){
    //函数调用
    function interface(module,fn,data){
        //data = data == undefined?JSON.parse(data): data;
        data =data != "" ? JSON.parse(data):data ;
        switch(module){
            case 'login':
                switch(fn){
                    case 'login_in':
                        loginReminderData(data);
                        break;
                    case 'login_state':
                        successLoginData(data);
                        userMsgData(data);
                        break;
                    case 'register':
                        registerSucData(data);
                        break;
                    case 'erroThree':
                        erroThree(data);
                        break;
                    case 'verification':
                        checkVerification(data);
                        break;
                    case 'select_phoneNum':
                        selectUserDate(data);
                        break;
                    case 'verificationCheck':
                        verificationData(data);
                        break;
                    case 'checkaccountres':
                        checkaccountres(data);
                        break;
                    case 'show_login':
                        showLogin();
                        break;
                    case 'user_logout':
                        user_logout();
                        break;
                    case 'update_usermsg':
                        update_usermsg(data);
                        break;
                    case 'offline_login':
                        offline_login();
                        break;
                    case 'network_error':
                        network_error();
                        break;
                    case 'show_code':
                        show_code(data);
                        break;
                    case 'checkcode':
                        checkcode(data);
                        break;
                    case 'refresh_page':
                        refresh_page();
                        break;
                }
                break;
            case 'main_panel':
                switch (fn) {
                    case 'add_game':
                        getLeftRecords(data);
                        break;
                    case 'max':
                        maxFn(data);
                        break;
                    case 'menu':
                        setDateFn(data);
                        break;
                    case 'showCourse':
                        showCourse();
                        break;
                    case 'showPlatformInfor':
                        showPlatformInfor()
                        break;
                    case 'aboutVr':
                        showVrStep();
                        break;
                    case 'vrOpen':
                        vrOpen();
                        break;
                    case 'vrClose':
                        vrClose();
                        break;
                    case 'correctOpen':
                        correctOpen();
                        break;
                    case 'correctClose':
                        correctClose();
                        break;
                    case 'title_table':
                        titleTable(data);
                        break;
                    case 'system_tips':
                        system_tips();
                        break;
                    case 'close_platform':
                        close_tips();
                        break;
                    case 'close_data':
                        close_data(data);
                        break;
                    case 'exit_vr':
                        exit_vr(data);
                        break;
                    case 'un_equipment_tips':
                        un_equipment_tips(data);
                        break;
                    case "chargereload":
                        chargereload();
                        break;
                    case "cptest":
                        cptest(data);
                        break;
                    case "public_bounced":
                        public_bounced(data);
                        break;
                    case 'setgamekey':
                        setgamekey(data);
                        break;
                    case 'exchange_code_result':
                        exchange_code_result(data);
                        break;
                    case 'setiframe':
                        setiframe();
                        break;
                    case 'showVersion':
                        showVersion(data);
                        break;
                    case 'goGameDetail':
                        goGameDetail(data);
                        break;
                    case 'getDrive':
                        getDrive(data);
                        break;
                    case 'driveDown':
                        driveDown(data);
                        break;
                    case 'showDownloadTip':
                        showDownloadTip()
                        break;
                    case 'showInstallTips':
                        showInstallTips();
                        break;
                }
                break;
            case 'list':
                switch(fn){
                    case 'add_local'://添加本地游戏
                        addLocalRoutine(data);
                        break;
                    case 'left_item':
                        getLeftRecords(data);
                        break;
                    case 'list_download_pro':
                        getLeftDownRecords(data);
                        break;
                    case 'delete_game':
                        deleteRecords(data);
                        break;
                    case 'select_list':
                        selectListFn(data);
                        break;
                    case 'changeGameListImg':
                        changeGameListImg(data);
                        break;
                    case  'gamelist_empty':
                        gamelist_empty();
                        break;
                }
                break;
            case 'detail':
                switch (fn){
                    case  'downloadBtn':
                        downloadStatue(data);
                        break;
                    case 'downloadProgress':
                        downloadProgress(data);
                        break;
                    case 'local_detail':
                        localDetaiDate(data);
                        break;
                    case 'usable':
                        usable_vr(data);
                        break;
                    case 'delete_game':
                        deleteRecords(data);
                        break;
                    case "download_tips_show":
                        download_tips_show();
                        break;
                    case "download_tips_hide":
                        download_tips_hide();
                        break;
                    case "download_tips_data":
                        download_tips_data(data);
                        break;
                    case "local_cofig_msg":
                        local_cofig_msg(data);
                        break;
                    case "unClickVr3d":
                        unClickVr3d();
                        break;
                    case "vr_sup_tips":
                        vr_sup_tips(data);
                        break;
                    case 'un_service_tips':
                        un_service_tips(data);
                        break;
                    case 'buy_game':
                        buy_game(data);
                        break;
                }
                break;
            case 'drive':
                switch (fn){
                    case "show_drive_title":
                        driveShow(data);
                        break;
                    case "hide_drive_title":
                        driveHide();
                        break;
                    case "drive_name":
                        drive_name(data);
                        break;
                    case "drive_information":
                        drive_information(data);
                        break;
                    case "drive_pro":
                        drive_pro(data);
                        break;
                    case "drive_pro_show":
                        drive_pro_show();
                        break;
                    case "drive_pro_hide":
                        drive_pro_hide();
                        break;
                    case "continue_down":
                        continue_down();
                        break;
                    case "pause_down":
                        pause_down();
                        break;
                    case "drive_erro_info":
                        drive_erro_info(data);
                        break;
                    case 'show_downBtn':
                        show_downBtn(data);
                        break;
                    case 'showInstallSuc':
                        showInstallSuc(data);
                        break;
                    case 'hideInstallSuc':
                        hideInstallSuc();
                        break;
                    case 'showInstallFail':
                        showInstallFail();
                        break;
                    case 'hideInstallFail':
                        hideInstallFail();
                        break;
                    case 'showInstallLoading':
                        showInstallLoading();
                        break;
                    case 'hideInstallLoading':
                        hideInstallLoading();
                        break;
                    case 'showDrive_pro':
                        showDrive_pro();
                        break;
                    case 'hideDrive_pro':
                        hideDrive_pro();
                        break;
                }
                break;
            case "other":
                 var event = new CustomEvent(module+"_"+fn,{detail:data});
                 window.dispatchEvent(event);
                 break;
        }
    }
    return {interface:interface} ;
}
var mainFn = new MainFn();
