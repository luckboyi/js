<?php
/**
 * 按位保存的状态信息，字段 bits_status
 * 写入时，bits_stats | is_sdk_conflict，得到的结果写入数据库
 * 读取时，bits_stats & is_sdk_conflict，得到的结果就是最终状态
 * 使用下面的对应的配置，通过计算得到对应的状态
 */
return [
	/**
	 * 游戏的接入的SDK是否与平台的冲突
	 */
	"is_sdk_conflict" => 1,
];
