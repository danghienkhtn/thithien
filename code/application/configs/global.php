<?php

/**
 * @name        :   global.php
 * @version     :   201611
 * @copyright   :   DaHi
 * @todo        :   define global configuration
 */

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
global $globalConfig;

$globalConfig['allow_file'] =  array("jpg", "png", "gif", "bmp", "jpeg","GIF","JPG","PNG","pdf","doc","docx","xls","xlsx");
$globalConfig['allow_image'] =  array("jpg", "png", "gif", "bmp", "jpeg","GIF","JPG","PNG");
$globalConfig['allow_document'] = array("pdf", "doc", "docx", "xls", "xlsx", "ppt", "pptx");

$globalConfig['special_day_type'] = array(

    1 => 'Holiday',
    2 => 'Compensation',
    3 => 'leave paid' // nghỉ tính lương

);

$globalConfig['general_type'] = array(
    
    1 => 'Position',
    4 => 'Contract Type',

);

$globalConfig['news_type'] = array(
    0 => 'None',
    1 => 'Announcement News',
    2 => 'Share News'  
);

$globalConfig['attribute_type'] = array(
    TEXTBOX_ATTRIBUTE_TYPE  => 'TextBox',
    TEXTAREA_ATTRIBUTE_TYPE => 'TextArea'
);

/*$globalConfig['attendance'] = array(
    1  => 'check-in',
    2  => 'check-out',
    3  => 'late',
    4  => '2h-late',
    5  => '2h-early',
);*/


/*
 *   Nguoi Dugn dau 1 nhom
 */
/*$globalConfig['manager_type'] = array(
    1  => 'Team magager',
    2  => 'Department magager',
);

// Platform 
$globalConfig['platform_type']  = array(
    1 => 'Normal',
    2 => 'Mobage',
    4 => 'AppDriver'
);

// Platform 
$globalConfig['device_type']  = array(
    1 => 'IOS',
    2 => 'Android',
    4 => 'Window phone',
    8 => 'Web'
);*/


$globalConfig['month_text'] = array(
    1  => 'Jan',
    2  => 'Feb',
    3  => 'Mar',
    4  => 'Apr',
    5  => 'May',
    6  => 'Jun',
    7  => 'Jul',
    8  => 'Aug',
    9  => 'Sep',
    10  => 'Oct',
    11  => 'Nov',
    12  => 'Dec',
);


$globalConfig['menu']  = array(
    1   => array(
                "name"          => "Home",
                "controller"    => "index",
                "link"          => "/backend",
                "icon"          => "iconHomeNew",
                "isshow"        => 1
                ),
    2   => array(
                "name"          => "Account list",
                "controller"    => "index",
                "link"          => "/backend/index",
                "icon"          => "iconUser",
                "isshow"        => 1
                ),
    4   => array(
                "name"          => "General",
                "controller"    => "general",
                "link"          => "/backend/general",
                "icon"          => "iconProduct",
                "isshow"        => 1
                ),
    8  => array(
                "name"          => "Attribute",
                "controller"    => "attribute",
                "link"          => "/backend/attribute",
                "icon"          => "iconPackage",
                "isshow"        => 1
                ),
    /*16  => array(
                "name"          => "Workflow",
                "controller"    => "workflow",
                "link"          => "/backend/workflow",
                "icon"          => "iconReward",
                "isshow"        => 1
                ),*/
    /*32  => array(
                "name"          => "Orgchart",
                "controller"    => "orgchart",
                "link"          => "/backend/orgchart/team",
                "icon"          => "iconEvent",
                "isshow"        => 1
                ),*/
    64  => array(
                "name"          => "News",
                "controller"    => "news",
                "link"          => "/backend/news",
                "icon"          => "iconPackage",
                "isshow"        => 1
                ),
   /* 128  => array(
                "name"          => "Product",
                "controller"    => "product",
                "link"          => "/backend/product",
                "icon"          => "iconProduct",
                "isshow"        => 1
                ),
    
    */
     /*256  => array(
                "name"          => "Album",
                "controller"    => "album",
                "link"          => "/backend/album",
                "icon"          => "iconUser",
                "isshow"        => 1
                ),
     512  => array(
                "name"          => "Photo",
                "controller"    => "photo",
                "link"          => "/backend/photo",
                "icon"          => "iconUser",
                "isshow"        => 0
                ),
     1024  => array(
                "name"          => "Group",
                "controller"    => "group",
                "link"          => "/backend/group",
                "icon"          => "iconUser",
                "isshow"        => 1
                ),
     2048  => array(
                "name"          => "MemberGroup",
                "controller"    => "groupmember",
                "link"          => "/backend/groupmember",
                "icon"          => "iconUser",
                "isshow"        => 0
                ),
        */
   
       );
   

/* For absence */

//Ly do cho phan nghi phep urgent
/*$globalConfig['absence_type_reason']  = array(
    1 => 'Wedding',
    2 => 'Have new baby',
    3 => 'Other'
);
*/
//absence date
/*$globalConfig['absence_date_type']  = array(
    1 => 'Full',
    2 => 'AM',
    3 => 'PM'
);
*/

//Ly do them ngay phep
/*$globalConfig['absence_type_reason_admin']  = array(
    1 => 'Wedding',
    2 => 'Have new baby',
    3 => 'Parents die',
    4 => 'Overtime' ,
    5 => 'Other' 
);
*/

// type of feed
/*$globalConfig['feed_type'] = array(
		'normal' => 1,
		'link'   => 2,
		'image'  => 3,
		'file'   => 4	
);

*/
// type of feed
/*$globalConfig['group_type'] = array(
		'company' => 1,//all gianty
		'team' => 2,
		'project' => 3,
		'other' => 4,
        'favorite' => 5

);
*/
//album type
/*$globalConfig['album_type'] = array(
    'company' => 1,
    'team'	=> 2,
    'project' => 3,
    'other' => 4
);
*/
//action status
/*$globalConfig['group_public'] = array(
		0 => 'No',
        1 => 'Yes'             
);
*/




// type of action
/*$globalConfig['workflow_action'] = array(
		1 => 'Create request',
                2 => 'View request',
                3 => 'Review request',
                4 => 'Approve request',
                5 => 'Confirm request' ,
                6 => 'Delegate request'
);
*/
//role of workflow
/*$globalConfig['workflow_role'] = array(
		1 => 'All',
                2 => 'Sub/Leader',
                3 => 'Deputy/Manager'
);
*/
//type of workflow
/*$globalConfig['workflow_type'] = array(
		1 => 'Leave of absence',
                2 => 'Expense request'
);
*/

//action status
/*$globalConfig['action_status'] = array(
		1 => 'Pending',
               // 2 => 'Pending (Need more information)',
                3 => 'OK',
                4 => 'Reject'
);
*/


//join group
/*$globalConfig['group_request'] = array(
		'request' => 0,
		'invite'	=> 1,
);
*/
//type acction log
$globalConfig['action_log_type'] = array(
		1 => 'news',
		2 => 'photo',
		3 => 'profile',
		4 => 'comment',
		5 => 'like',
        6 => 'user',
        7 => 'project member',
        8 => 'project',
        9 => 'group',
        10 => 'general',
        11 => 'leave application',
        12 => 'feed',
        13 => 'album',
        14 => 'special day',
		15 => 'admin'
);

// acction log
$globalConfig['action_log_action'] = array(
		1 => 'create',
		2 => 'update',
		3 => 'delete',
        4 => 'add'
);

$globalConfig['country'] = array(
        0 => '',
		1 => 'Vietnam',
		2 => 'Japan',
		3 => 'US'
);

$globalConfig['currencies'] = array(
    0 => '',
    1 => 'VND',
    2 => 'YEN',
    3 => 'USD'
);

//province
$globalConfig['province'] = array(
		
		0=>'',
		1=>'Bà Rịa - Vũng Tàu',
		2=>'Bạc Liêu',
		3=>'Bắc Kạn',
		4=>'Bắc Giang',
		5=>'Bắc Ninh',
		6=>'Bến Tre',
		7=>'Bình Dương',
		8=>'Bình Định',
		9=>'Bình Phước',
		10=>'Bình Thuận',
		11=>'Cà Mau',
		12=>'Cao Bằng',
		13=>'Cần Thơ',
		14=>'Đà Nẵng',
		15=>'Đắk Lắk',
		16=>'Đắk Nông',
		17=>'Đồng Nai',
		18=>'Đồng Tháp',
		19=>'Điện Biên',
		20=>'Gia Lai',
		21=>'Hà Giang',
		22=>'Hà Nam',
		23=>'Hà Nội',
		24=>'Hà Tĩnh',
		25=>'Hải Dương',
		26=>'Hải Phòng',
		27=>'Hòa Bình',
		28=>'Hậu Giang',
		29=>'Hưng Yên',
		30=>'TP. Hồ Chí Minh',
		31=>'Khánh Hòa',
		32=>'Kiên Giang',
		33=>'Kon Tum',
		34=>'Lai Châu',
		35=>'Lào Cai',
		36=>'Lạng Sơn',
		37=>'Lâm Đồng',
		38=>'Long An',
		39=>'Nam Định',
		40=>'Nghệ An',
		41=>'Ninh Bình',
		42=>'Ninh Thuận',
		43=>'Phú Thọ',
		44=>'Phú Yên',
		45=>'Quảng Bình',
		46=>'Quảng Nam',
		47=>'Quảng Ngãi',
		48=>'Quảng Ninh',
		49=>'Quảng Trị',
		50=>'Sóc Trăng',
		51=>'Sơn La',
		52=>'Tây Ninh',
		53=>'Thái Bình',
		54=>'Thái Nguyên',
		55=>'Thanh Hóa',
		56=>'Thừa Thiên - Huế',
		57=>'Tiền Giang',
		58=>'Trà Vinh',
		59=>'Tuyên Quang',
		60=>'Vĩnh Long',
		61=>'Vĩnh Phúc',
		62=>'Yên Bái',
		63=>'An Giang'

);

//bank name
$globalConfig['bank_account_id'] = array(
		
		0=>'',
		1=>'ACB',
		2=>'TP Bank',
		3=>'DAB',
		4=>'Oceanbank',
		5=>'ABBank',
		6=>'BacABank',
		7=>'GPBank',
		8=>'VCCB',
		9=>'MSB',
		10=>'Techcombank',
		11=>'KienLongBank',
		12=>'Nam A Bank',
		13=>'NCB',
		14=>'VPBank',
		15=>'HDBank',
		16=>'OCB',
		17=>'MB',
		18=>'PVcom Bank',
		19=>'VIB',
		20=>'SCB',
		21=>'Saigonbank',
		22=>'SHB',
		23=>'Sacombank',
		24=>'VAB',
		25=>'BVB',
		26=>'VietBank',
		27=>'PG Bank',
		28=>'EIB',
		29=>'LienVietPostBank',
		30=>'VCB',
		31=>'VNCB',
		32=>'Vietinbank',
		33=>'BIDV',
		34=>'Agribank'
);

//contract
/*$globalConfig['contract'] = array(
		0 => '',
		1 => '1 year',
		2 => '2 years',
		3 => '3 years',
		4 => 'unlimit'
);*/

$globalConfig['level'] = array(
		0 => '',
		1 => 'Cộng tác viên',
		2 => 'Quản trị',
);


// $globalConfig['ManagerDocsLevel'] = array(4,5,6,7);

/*$globalConfig['position'] = array(
		0 => '',
		1 => 'Animator',
		2 => 'Software Engineer',
		3 => 'QA',
		4 => 'Tester',
		5 => 'HR',
		6 => 'IT',
		7 => 'Accountant',
		8 => 'Monitor',
		9 => 'Designer'
		
);
*/
$globalConfig['gender'] = array(    
    0 => 'Male',
    1 => 'FeMale'
);

$globalConfig['ownerName'] = array(
    1 => 'His',
    2 => 'Her'
);

$globalConfig['marital'] = array(
		0 => 'Single',
		1 => 'Married'
);

?>
