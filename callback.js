/*
* @Author: zhengy
* @Date:   2017-06-05 16:25:05
* @Last Modified by:   zhengy
* @Last Modified time: 2017-06-05 16:25:17
*/

'use strict';


function a(callback){
	if(typeof callback === 'function'){
		callback();
	}
}
function c(){
	alert(1)
}

a(c)