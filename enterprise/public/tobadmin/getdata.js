


// window.onmessage = function(e){
//     //var data = e.data;
//     console.dir(e.data);
//     if(typeof(e.data)=="object") {
//         return
//     }
//     console.log(e.data);
//     var jsonE=JSON.parse(e.data);
//     var josnData={};
//     josnData.type = jsonE.type;
//     switch(josnData.type){
//         case 'gamesstatusres':
//             for(var i= 0 ; i< jsonE.data.length ;i++){
//                 josnData[jsonE.data[i].gameid]=jsonE.data[i];
//             }
//         break;
//         case 'gamedowninfores':
//             for(var i= 0 ; i< jsonE.data.length ;i++){
//                 josnData[jsonE.data[i].gameid]=jsonE.data[i];
//             }
//         break;
//         default:
//             josnData[jsonE.data.gameid]=jsonE.data;
//         break;
//     }
//     localStorage.setItem(josnData.type,JSON.stringify(josnData));
//     //vm.$emit('getgamestatus',JSON.stringify(josnData));
//     //localStorage.setItem("gamelist",e.data);
// }

