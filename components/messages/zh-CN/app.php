<?php
return [
    'web_name' => '全民支付',

    'default_select' => [
        '' => '请选择',
    ],
    'system_config_status' => [
        0 => '禁用',
        1 => '启用',
        2 => '删除',
    ],
    'ip_status' => [
        0 => '禁用',
        1 => '启用',
        2 => '删除',
    ],
    'mch_status' => [
        0 => '禁用',
        1 => '启用',
    ],
    'is_settlement' => [
        0 => '未结算',
        1 => '已结算',
    ],
    'user_type' => [
        '1' => '平台',
        '2' => '商户',
        '3' => '收款员',
    ],
    'user_type_bank' => [
        '1' => '商户',
        '2' => '收款员',
    ],
    'user_type_withdraw' => [
        '1' => '商户',
        '2' => '收款员',
    ],
    'query_team' => [
        '1' => '直接下级',
        '2' => '所有下级',
    ],
    'finance_type' => [
        '1' => '保证金',
        '2' => '收益',
        '3' => '微信可收额度',
        '4' => '支付宝可收额度',
        '5' => '手续费',
        '6' => '提现',
        '7' => '提现返还',
        '8' => '微信接单',
        '9' => '微信接单返还',
        '10' => '提单',
        '11' => '提单返还',
        '12' => '支付宝接单',
        '13' => '支付宝接单返还',
        '14' => '云闪付接单',
        '15' => '云闪付接单返还',
        '16' => '云闪付可收额度',
        '17' => '银行卡接单',
        '18' => '银行卡接单返还',
        '19' => '银行卡可收额度',
    ],
    'deposit_status' => [
        '0' => '创建',
        '1' => '处理中',
        '2' => '成功',
        '3' => '失败',
        '4' => '驳回',
    ],
    'withdraw_status' => [
        '0' => '创建',
        '1' => '处理中',
        '2' => '成功',
        '3' => '失败',
        '4' => '驳回',
    ],
    'cashier_status' => [
        0 => '禁用',
        1 => '启用',
        2 => '删除',
    ],
    'is_shopowner' => [
        1 => '店长',
        2 => '店员',
    ],
    'trade_type' => [
        1 => '支付宝',
        2 => '微信',
        3 => '银商码',
    ],
    'qr_type' => [
        1 => '支付宝',
        2 => '微信',
        3 => '云闪付',
        4 => '银行卡',
    ],
    'channel_status' => [
        0 => '关闭',
        1 => '开启',
    ],
    'bank_card_pay_type' => [
        101 => '网银转卡',
        102 => '支付宝转卡',
        103 => '微信转卡',
        104 => '手机号转卡',
    ],
    'order_type' => [
        1 => '支付宝',
        2 => '微信',
        3 => '云闪付',
        4 => '银行卡',
    ],
    'trade_cate' => [
        1 => '支付宝',
        2 => '微信',
    ],
    'google' => '谷歌验证码',
    'trans_type' => [
        0 => '支出',
        1 => '收入',
    ],
    'qr_status' => [
        0 => '禁用',
        1 => '启用',
        2 => '接单',
        9 => '删除',
    ],
    'order_status' => [
        1 => '未支付',
        2 => '自动成功',
        3 => '超时',
        4 => '手动失败',
        5 => '手动成功',
        999 => '所有成功',
    ],
    'trans_status' => [
        0 => '创建',
        1 => '处理中',
        2 => '成功',
        3 => '异常',
    ],
    'refund_status' => [
        '' => '未返款',
        1 => '待审核',
        2 => '成功',
        3 => '驳回',
    ],
    'refund_type' => [
        '' => '待确认',
        1 => '全额返款',
        2 => '扣除佣金返款',
        3 => '待确认',
    ],
    'notify_status' => [
        1 => '未通知',
        2 => '已通知',
        3 => '通知失败',
    ],

    'order_date' => [
        '当日补单' => '当日补单',
        '跨日补单' => '跨日补单',
    ],
    //login
    'login' => '登录',
    'logout' => '安全退出',
    'signin' => '登录',
    'account_login' => '登录到您的帐户',
    'username' => '用户名',
    'password' => '密码',
    'remember_me' => '记住我',
    'invalid_username' => '用户名无效',
    'invalid_password' => '用户名或密码错误',
    'loading' => '加载中',
    'success' => '成功',
    'fail' => '失败',
    'profile' => '个人资料',
    'not_set' => '未设置',
    'change_password' => '修改密码',
    'none' => '无',

    //error
    'error' => '错误',
    'error_page' => '出错了',
    'error_http403' => '您没有操作权限',
    'error_http404' => '请求页面未找到',
    'error_http500' => '服务器或者网络错误，请稍后重试',
    'error_permission_denied' => '权限不足',

    'bank_code' => array(
        'ICBC' => '中国工商银行',
        'CMB' => '招商银行',
        'CCB' => '中国建设银行',
        'BOC' => '中国银行',
        'ABC' => '中国农业银行',
        'BCM' => '交通银行',
        'SPDB' => '上海浦东发展银行',
        'CGB' => '广东发展银行',
        'CNCB' => '中信银行',
        'CEB' => '中国光大银行',
        'CIB' => '兴业银行',
        'PAB' => '平安银行',
        'CMBC' => '中国民生银行',
        'HXB' => '华夏银行',
        'PSBC' => '中国邮政储蓄银行',
        'NBBANK' => '宁波银行',
        'BJBANK' => '北京银行',
        'CZBANK' => '浙商银行',
        'GDRCC' => '广东省农村信用社联合社',
        'BOD' => '东莞银行',
        'NJCB' => '南京银行',
        'NCB' => '江西银行',
        'HZCB' => '杭州银行',
        'SRCB' => '深圳农村商业银行',


        'GCB' => '广州银行',
        'JSBC' => '江苏银行',
        'BGB' => '北部湾银行',
        'GLB' => '桂林银行',
        'BOHAIB' => '渤海银行',
        'XMBANK' => '厦门银行',
        'SHBANK' => '上海银行',
        'HSBANK' => '徽商银行',
        'TCCB' => '天津银行',
        'LZCCB' => '柳州银行',
        'SDRCU' => '山东省农村信用社联合社',
        'JXRCU' => '江西省农村信用社',
        'WHRCB' => '武汉农村商业银行',
        'GXRCU' => '广西壮族自治区农村信用社联合社',
        'GZRCU' => '贵州省农村信用社联合社',
        'MTBANK' => '浙江民泰商业银行',

        'JLBANK' => '吉林银行',
        'KSRB' => '昆山农村商业银行',
        'SHRCB' => '上海农村商业银行',
        'DLB' => '大连银行',
        'YZBANK' => '银座村镇银行',
        'SJBANK' => '盛京银行	',
        'HURCB' => '湖北省农信社',
        'DRCBCL' => '东莞农村商业银行',
        'ZJNX' => '浙江省农村信用社联合社',
        'HNRCC' => '湖南省农村信用社',
        'BOSZ' => '苏州银行',
        'HBC' => '湖北银行',
        'JXNXS' => '江西农商银行',
        'URCB' => '杭州联合银行',
        'GSBANK' => '甘肃银行',
        'BENXI' => '本溪银行',
        'HRXJB' => '华融湘江银行',
        'CABANK' => '长安银行',
        'CZCB' => '浙江稠州商业银行',
        'JSQDCCB' => '江苏启东农商银行',
        'JJBANK' => '九江银行',
        'LSBANK' => '莱商银行',
        'QDCCB' => '青岛银行',
        'XABANK' => '西安银行',
        'HKB' => '汉口银行',
        'CSCB' => '长沙银行',
        'HRBANK' =>  '哈尔滨银行',
        'NXRCU' => '宁夏黄河农村商业银行',
        'NYNB' => '广东南粤银行',
        'ZZBANK' => '郑州银行',
        'WHCCB' => '威海市商业银行',
        'GHB' => '广东华兴银行',
        'CQBANK' => '重庆银行',
        'ZRCBANK' => '张家港农村商业银行',
        'BOJZ' => '锦州银行',
        'SHZBORU' => '石河子交银村镇银行',
        'CSRCB' => '常熟农商银行',
        'ZJTLCB' => '浙江泰隆商业银行',
        'GRCB' => '广州农商银行',
        'HNRCU' => '河南省农村信用社',
        'ZYB' => '中原银行',
        'EGBANK' => '恒丰银行',
        'ZYCBANK' => '贵州银行',
        'DYCCB' => '东营银行',
        'ZBCB' => '齐商银行',
        'BOCZ' => '沧州银行',
        'QLBANK' => '齐鲁银行',
        'ZJKCCB' => '张家口银行',
        'CDCB' => '成都银行',
        'JRCCB' => '江南村镇银行',
        'YRCCT' => '云南农村信用社',
        'QDRCCB' => '青岛农商银行',
        'BHB' => '河北银行',
        'GDRC' => '广东农信银行',
        'RBOZ' => '华润银行',
        'HHNX' => '黄河农信银行',
        'JSB' => '晋商银行',
        'HDBANK' => '邯郸银行',
        'BJRCB' => '北京农商银行',
        'GYCB' => '贵阳银行',
        'ZRCB' => '珠海农商银行',
        'CRCB' => '贵阳农商银行',
        'SXCCU' => '陕西信合银行',
        'FJHXBC' => '福建省农村信用',
        'BOYK' => '营口银行',
        'LANGFB' => '廊坊银行	',
        'WSGM' => '微信固码',
        'BANKWF' => '潍坊银行',
        'RZB' => '日照银行',
        'TACCB' => '泰安银行',
        'JNBANK' => '济宁银行',
        'DZBANK' => '德州银行',
        'LSBC' => '临商银行',
        'YTBANK' => '烟台银行',
    ),

    //银行类型配置
    'BankTypes' => array(
        '101' => array('BankTypeName' => '中国工商银行', 'BankTypeShortName' => '工商', 'BankTypeCode' => 'ICBC'),
        '102' => array('BankTypeName' => '招商银行', 'BankTypeShortName' => '招商', 'BankTypeCode' => 'CMB'),
        '103' => array('BankTypeName' => '中国建设银行', 'BankTypeShortName' => '建设', 'BankTypeCode' => 'CCB'),
        '104' => array('BankTypeName' => '中国银行', 'BankTypeShortName' => '中国', 'BankTypeCode' => 'BOC'),
        '105' => array('BankTypeName' => '中国农业银行', 'BankTypeShortName' => '农业', 'BankTypeCode' => 'ABC'),
        '106' => array('BankTypeName' => '交通银行', 'BankTypeShortName' => '交通', 'BankTypeCode' => 'BCM'),
        '107' => array('BankTypeName' => '上海浦东发展银行', 'BankTypeShortName' => '浦发', 'BankTypeCode' => 'SPDB'),
        '108' => array('BankTypeName' => '广东发展银行', 'BankTypeShortName' => '广发', 'BankTypeCode' => 'CGB'),
        '109' => array('BankTypeName' => '中信银行', 'BankTypeShortName' => '中信', 'BankTypeCode' => 'CNCB'),
        '110' => array('BankTypeName' => '中国光大银行', 'BankTypeShortName' => '光大', 'BankTypeCode' => 'CEB'),
        '111' => array('BankTypeName' => '兴业银行', 'BankTypeShortName' => '兴业', 'BankTypeCode' => 'CIB'),
        '112' => array('BankTypeName' => '平安银行', 'BankTypeShortName' => '平安', 'BankTypeCode' => 'PAB'),
        '113' => array('BankTypeName' => '中国民生银行', 'BankTypeShortName' => '民生', 'BankTypeCode' => 'CMBC'),
        '114' => array('BankTypeName' => '华夏银行', 'BankTypeShortName' => '华夏', 'BankTypeCode' => 'HXB'),
        '115' => array('BankTypeName' => '中国邮政储蓄银行', 'BankTypeShortName' => '邮政', 'BankTypeCode' => 'PSBC'),
        '116' => array('BankTypeName' => '宁波银行', 'BankTypeShortName' => '宁波', 'BankTypeCode' => 'NBBANK'),
        '117' => array('BankTypeName' => '北京银行', 'BankTypeShortName' => '北京', 'BankTypeCode' => 'BJBANK'),
        '118' => array('BankTypeName' => '浙商银行', 'BankTypeShortName' => '浙商', 'BankTypeCode' => 'CZBANK'),
        '119' => array('BankTypeName' => '广州银行', 'BankTypeShortName' => '广州', 'BankTypeCode' => 'GCB'),
        '120' => array('BankTypeName' => '江苏银行', 'BankTypeShortName' => '江苏', 'BankTypeCode' => 'JSBC'),
        '121' => array('BankTypeName' => '北部湾银行', 'BankTypeShortName' => '北部湾', 'BankTypeCode' => 'BGB'),
        '122' => array('BankTypeName' => '桂林银行', 'BankTypeShortName' => '桂林', 'BankTypeCode' => 'GLB'),
        '123' => array('BankTypeName' => '广东省农村信用社联合社', 'BankTypeShortName' => '广东农信', 'BankTypeCode' => 'GDRCC'),
        '124' => array('BankTypeName' => '东莞银行', 'BankTypeShortName' => '东莞', 'BankTypeCode' => 'BOD'),
        '125' => array('BankTypeName' => '渤海银行', 'BankTypeShortName' => '渤海', 'BankTypeCode' => 'BOHAIB'),
        '126' => array('BankTypeName' => '南京银行', 'BankTypeShortName' => '南京', 'BankTypeCode' => 'NJCB'),
        '127' => array('BankTypeName' => '厦门银行', 'BankTypeShortName' => '厦门', 'BankTypeCode' => 'XMBANK'),
        '128' => array('BankTypeName' => '上海银行', 'BankTypeShortName' => '上海', 'BankTypeCode' => 'SHBANK'),
        '129' => array('BankTypeName' => '徽商银行', 'BankTypeShortName' => '徽商', 'BankTypeCode' => 'HSBANK'),
        '130' => array('BankTypeName' => '天津银行', 'BankTypeShortName' => '天津', 'BankTypeCode' => 'TCCB'),
        '131' => array('BankTypeName' => '柳州银行', 'BankTypeShortName' => '柳州', 'BankTypeCode' => 'LZCCB'),
        '132' => array('BankTypeName' => '山东省农村信用社联合社', 'BankTypeShortName' => '山东农信', 'BankTypeCode' => 'SDRCU'),
        '133' => array('BankTypeName' => '江西省农村信用社', 'BankTypeShortName' => '江西农信', 'BankTypeCode' => 'JXRCU'),
        '134' => array('BankTypeName' => '江西银行', 'BankTypeShortName' => '江西', 'BankTypeCode' => 'NCB'),
        '135' => array('BankTypeName' => '杭州银行', 'BankTypeShortName' => '杭州', 'BankTypeCode' => 'HZCB'),
        '136' => array('BankTypeName' => '武汉农村商业银行', 'BankTypeShortName' => '武汉农商', 'BankTypeCode' => 'WHRCB'),
        '137' => array('BankTypeName' => '广西壮族自治区农村信用社联合社', 'BankTypeShortName' => '广西农信', 'BankTypeCode' => 'GXRCU'),
        '138' => array('BankTypeName' => '贵州省农村信用社联合社', 'BankTypeShortName' => '贵州农信', 'BankTypeCode' => 'GZRCU'),
        '139' => array('BankTypeName' => '浙江民泰商业银行', 'BankTypeShortName' => '民泰商业', 'BankTypeCode' => 'MTBANK'),
        '140' => array('BankTypeName' => '深圳农村商业银行', 'BankTypeShortName' => '深圳农商', 'BankTypeCode' => 'SRCB'),
        '141' => array('BankTypeName' => '吉林银行', 'BankTypeShortName' => '吉林银行', 'BankTypeCode' => 'JLBANK'),
        '142' => array('BankTypeName' => '昆山农村商业银行', 'BankTypeShortName' => '昆山农商', 'BankTypeCode' => 'KSRB'),
        '143' => array('BankTypeName' => '上海农村商业银行', 'BankTypeShortName' => '上海农商', 'BankTypeCode' => 'SHRCB'),
        '144' => array('BankTypeName' => '大连银行', 'BankTypeShortName' => '大连银行', 'BankTypeCode' => 'DLB'),
        '145' => array('BankTypeName' => '银座村镇银行', 'BankTypeShortName' => '银座银行', 'BankTypeCode' => 'YZBANK'),
        '146' => array('BankTypeName' => '盛京银行	', 'BankTypeShortName' => '盛京', 'BankTypeCode' => 'SJBANK'),
        '147' => array('BankTypeName' => '湖北省农信社', 'BankTypeShortName' => '湖北农信', 'BankTypeCode' => 'HURCB'),
        '148' => array('BankTypeName' => '东莞农村商业银行', 'BankTypeShortName' => '东莞农商', 'BankTypeCode' => 'DRCBCL'),
        '149' => array('BankTypeName' => '浙江省农村信用社联合社', 'BankTypeShortName' => '浙江农信', 'BankTypeCode' => 'ZJNX'),
        '150' => array('BankTypeName' => '湖南省农村信用社', 'BankTypeShortName' => '湖南农信', 'BankTypeCode' => 'HNRCC'),
        '151' => array('BankTypeName' => '苏州银行', 'BankTypeShortName' => '苏州', 'BankTypeCode' => 'BOSZ'),
        '152' => array('BankTypeName' => '湖北银行', 'BankTypeShortName' => '湖北', 'BankTypeCode' => 'HBC'),
        '153' => array('BankTypeName' => '江西农商银行', 'BankTypeShortName' => '江西农商', 'BankTypeCode' => 'JXNXS'),
        '154' => array('BankTypeName' => '杭州联合银行', 'BankTypeShortName' => '杭州联合', 'BankTypeCode' => 'URCB'),
        '155' => array('BankTypeName' => '甘肃银行', 'BankTypeShortName' => '甘肃', 'BankTypeCode' => 'GSBANK'),
        '156' => array('BankTypeName' => '本溪银行', 'BankTypeShortName' => '本溪', 'BankTypeCode' => 'BENXI'),
        '157' => array('BankTypeName' => '华融湘江银行', 'BankTypeShortName' => '华融', 'BankTypeCode' => 'HRXJB'),
        '158' => array('BankTypeName' => '长安银行', 'BankTypeShortName' => '长安', 'BankTypeCode' => 'CABANK'),
        '159' => array('BankTypeName' => '浙江稠州商业银行', 'BankTypeShortName' => '稠州', 'BankTypeCode' => 'CZCB'),
        '160' => array('BankTypeName' => '江苏启东农商银行', 'BankTypeShortName' => '江苏启东', 'BankTypeCode' => 'JSQDCCB'),
        '161' => array('BankTypeName' => '九江银行', 'BankTypeShortName' => '九江', 'BankTypeCode' => 'JJBANK'),
        '162' => array('BankTypeName' => '莱商银行', 'BankTypeShortName' => '莱商', 'BankTypeCode' => 'LSBANK'),
        '163' => array('BankTypeName' => '青岛银行', 'BankTypeShortName' => '青岛', 'BankTypeCode' => 'QDCCB'),
        '164' => array('BankTypeName' => '西安银行', 'BankTypeShortName' => '西安', 'BankTypeCode' => 'XABANK'),
        '165' => array('BankTypeName' => '汉口银行', 'BankTypeShortName' => '汉口', 'BankTypeCode' => 'HKB'),
        '166' => array('BankTypeName' => '长沙银行	', 'BankTypeShortName' => '长沙', 'BankTypeCode' => 'CSCB'),
        '167' => array('BankTypeName' => '哈尔滨银行', 'BankTypeShortName' => '哈尔滨', 'BankTypeCode' => 'HRBANK'),
        '168' => array('BankTypeName' => '宁夏黄河农村商业银行', 'BankTypeShortName' => '宁夏黄河', 'BankTypeCode' => 'NXRCU'),
        '169' => array('BankTypeName' => '广东南粤银行', 'BankTypeShortName' => '广东南粤', 'BankTypeCode' => 'NYNB'),
        '170' => array('BankTypeName' => '郑州银行', 'BankTypeShortName' => '郑州', 'BankTypeCode' => 'ZZBANK'),
        '171' => array('BankTypeName' => '威海市商业银行', 'BankTypeShortName' => '威海', 'BankTypeCode' => 'WHCCB'),
        '172' => array('BankTypeName' => '广东华兴银行', 'BankTypeShortName' => '华兴', 'BankTypeCode' => 'GHB'),
        '173' => array('BankTypeName' => '重庆银行', 'BankTypeShortName' => '重庆', 'BankTypeCode' => 'CQBANK'),
        '174' => array('BankTypeName' => '张家港农村商业银行', 'BankTypeShortName' => '张家港农商', 'BankTypeCode' => 'ZRCBANK'),
        '175' => array('BankTypeName' => '锦州银行', 'BankTypeShortName' => '锦州', 'BankTypeCode' => 'BOJZ'),
        '176' => array('BankTypeName' => '石河子交银村镇银行', 'BankTypeShortName' => '交银村镇银行', 'BankTypeCode' => 'SHZBORU'),
        '177' => array('BankTypeName' => '常熟农商银行', 'BankTypeShortName' => '常熟农商', 'BankTypeCode' => 'CSRCB'),
        '179' => array('BankTypeName' => '浙江泰隆商业银行', 'BankTypeShortName' => '泰隆', 'BankTypeCode' => 'ZJTLCB'),
        '180' => array('BankTypeName' => '广州农商银行', 'BankTypeShortName' => '广州农商', 'BankTypeCode' => 'GRCB'),
        '181' => array('BankTypeName' => '河南省农村信用社', 'BankTypeShortName' => '河南农信', 'BankTypeCode' => 'HNRCU'),
        '182' => array('BankTypeName' => '中原银行', 'BankTypeShortName' => '中原', 'BankTypeCode' => 'ZYB'),
        '183' => array('BankTypeName' => '恒丰银行', 'BankTypeShortName' => '恒丰', 'BankTypeCode' => 'EGBANK'),
        '184' => array('BankTypeName' => '贵州银行', 'BankTypeShortName' => '贵州', 'BankTypeCode' => 'ZYCBANK'),
        '185' => array('BankTypeName' => '东营银行', 'BankTypeShortName' => '东营', 'BankTypeCode' => 'DYCCB'),
        '186' => array('BankTypeName' => '齐商银行', 'BankTypeShortName' => '齐商', 'BankTypeCode' => 'ZBCB'),
        '187' => array('BankTypeName' => '沧州银行	', 'BankTypeShortName' => '沧州', 'BankTypeCode' => 'BOCZ'),
        '188' => array('BankTypeName' => '齐鲁银行', 'BankTypeShortName' => '齐鲁', 'BankTypeCode' => 'QLBANK'),
        '189' => array('BankTypeName' => '张家口银行', 'BankTypeShortName' => '张家口', 'BankTypeCode' => 'ZJKCCB'),
        '190' => array('BankTypeName' => '成都银行', 'BankTypeShortName' => '成都', 'BankTypeCode' => 'CDCB'),
        '191' => array('BankTypeName' => '江南村镇银行', 'BankTypeShortName' => '江南村镇', 'BankTypeCode' => 'JRCCB'),
        '192' => array('BankTypeName' => '云南农村信用社', 'BankTypeShortName' => '云南农信', 'BankTypeCode' => 'YRCCT'),
        '193' => array('BankTypeName' => '青岛农商银行', 'BankTypeShortName' => '青岛农商', 'BankTypeCode' => 'QDRCCB'),
        '194' => array('BankTypeName' => '河北银行', 'BankTypeShortName' => '河北', 'BankTypeCode' => 'BHB'),
        '197' => array('BankTypeName' => '广东农信银行', 'BankTypeShortName' => '广东农信', 'BankTypeCode' => 'GDRC'),
        '199' => array('BankTypeName' => '华润银行', 'BankTypeShortName' => '华润', 'BankTypeCode' => 'RBOZ'),
        '201' => array('BankTypeName' => '黄河农信银行', 'BankTypeShortName' => '黄河农信', 'BankTypeCode' => 'hhnx'),
        '203' => array('BankTypeName' => '晋商银行', 'BankTypeShortName' => '晋商', 'BankTypeCode' => 'JSB'),
        '204' => array('BankTypeName' => '邯郸银行', 'BankTypeShortName' => '邯郸', 'BankTypeCode' => 'HDBANK'),
        '205' => array('BankTypeName' => '北京农商银行', 'BankTypeShortName' => '北京农商', 'BankTypeCode' => 'BJRCB'),
        '206' => array('BankTypeName' => '贵阳银行', 'BankTypeShortName' => '贵阳', 'BankTypeCode' => 'GYCB'),
        '207' => array('BankTypeName' => '珠海农商银行', 'BankTypeShortName' => '珠海农商', 'BankTypeCode' => 'ZRCB'),
        '208' => array('BankTypeName' => '贵阳农商银行', 'BankTypeShortName' => '贵阳农商', 'BankTypeCode' => 'CRCB'),
        '209' => array('BankTypeName' => '陕西信合银行', 'BankTypeShortName' => '陕西信合', 'BankTypeCode' => 'SXCCU'),
        '210' => array('BankTypeName' => '福建省农村信用', 'BankTypeShortName' => '福建农信', 'BankTypeCode' => 'FJHXBC'),
        '211' => array('BankTypeName' => '营口银行', 'BankTypeShortName' => '营口', 'BankTypeCode' => 'BOYK'),
        '212' => array('BankTypeName' => '廊坊银行	', 'BankTypeShortName' => '廊坊', 'BankTypeCode' => 'LANGFB'),
        '213' => array('BankTypeName' => '微信固码', 'BankTypeShortName' => 'WSGM', 'BankTypeCode' => 'WSGM'),
        '214' => array('BankTypeName' => '潍坊银行', 'BankTypeShortName' => '潍坊', 'BankTypeCode' => 'BANKWF'),
        '215' => array('BankTypeName' => '日照银行', 'BankTypeShortName' => '日照', 'BankTypeCode' => 'RZB'),
        '216' => array('BankTypeName' => '泰安银行', 'BankTypeShortName' => '泰安', 'BankTypeCode' => 'TACCB'),
        '217' => array('BankTypeName' => '济宁银行', 'BankTypeShortName' => '济宁', 'BankTypeCode' => 'JNBANK'),
        '218' => array('BankTypeName' => '德州银行', 'BankTypeShortName' => '德州', 'BankTypeCode' => 'DZBANK'),
        '219' => array('BankTypeName' => '临商银行', 'BankTypeShortName' => '临商', 'BankTypeCode' => 'LSBC'),
        '220' => array('BankTypeName' => '烟台银行', 'BankTypeShortName' => '烟台', 'BankTypeCode' => 'YTBANK'),
    ),


    //二维码允许接单类型
    'qr_allow_order_types' => [
        '1' => '支付宝扫码',
        '2' => '微信扫码',
        '3' => '云闪付扫码',
        '100' => '支付宝红包',
        '101' => '网银转卡',
        '102' => '支付宝转卡',
        '103' => '微信转卡',
        '104' => '手机号转卡',
        '110' => '支付宝网关',
    ],


    //系统银行卡所属人
    'sys_bankcard_owner' => array(
        1 => '全民自有',
        2 => 'YB菲',
        3 => 'YB柬',
        4 => '包网',
    ),
];