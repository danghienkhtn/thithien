<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
global $adminConfig;

//$localObj = Core_Global::getLocalesIni();

/*
 * menu parent
 */
$adminConfig['menu'] = array(
		
    	array('name' => 'Page', 'menu' => array(
						    					  array('name' => 'Dashboard','controller' => array('index'))
										    	, array('name' => 'User','controller' => array('user'))
												, array('name' => 'Groups','controller' => array('group'))
												, array('name' => 'Projects','controller' => array('project'))
												, array('name' => 'Action Log','controller' => array('actionlogs'))
												, array('name' => 'News','controller' => array('news'))
												, array('name' => 'Gallery','controller' => array('album'))
//												, array('name' => 'Workflows','controller' => array('leaveapplication'), 'subMenu' => TRUE)
												, array('name' => 'Workflows','controller' => array('workflow'))
												, array('name' => 'Calendar','controller' => array('calendar'))
												, array('name' => 'Employee Designations','controller' => array('general'))

//												, array('name' => 'Org Chart','controller' => array('orgchart'))
    			)),
    	array('name' => 'Tool', 'menu' => array(
                          array('name' => 'Admin','controller' => array('admin'))
                        , array('name' => 'Absence','controller' => array('absence'))
                        , array('name' => 'Mail','controller' => array('mail'))
                        , array('name' => 'Special day', 'controller' => array('specialday'))
                        , array('name' => 'Attendance','controller' => array('attendance'))
		))
		
    );

/*
 * permission parent
 */
$adminConfig['permission'] = array(
        'index' => array(
            'name' => 'Dashboard',
            'visible' => 1,
            'log' => 1,
            'icon' => 'fa fa-home',
            'type' => PAGE,
            'action' => array(
                'index' => array(
                    'name' => 'Dashboard',
                    'visible' => 1,
                    'value' => 1
                ),

            )
        ),

//    'orgchart' => array(
//        'name' => 'Org Chart',
//        'visible' => 1,
//        'log' => 1,
//        'icon' => 'fa fa-home',
//        'type' => PAGE,
//        'action' => array(
//            'index' => array(
//                'name' => 'Org Chart',
//                'visible' => 1,
//                'value' => 1
//            ),
//
//        )
//    ),

	    'user' => array(
	        'name' => 'User',
	        'visible' => 1,
	        'log' => 1,
            'icon' => 'fa fa-user',
            'type' => PAGE,
	        'action' => array(
	            'index' => array(
	                'name' => 'List User',
	                'visible' => 1,
	                'value' => 1
	            ),
	            'lstuser' => array(
	                'name' => '',
	                'visible' => 0,
	                'value' => 1
	            ),
	            'exporttoexcel' => array(
	                'name' => 'Export',
	                'visible' => 0,
	                'value' => 2
	            ),
	            'searchuser' => array(
	                'name' => 'Search',
	                'visible' => 0,
	                'value' => 4
	            ),
	            'summary' => array(
	                'name' => 'Summary',
	                'visible' => 0,
	                'value' => 8
	            ),
	            'general' => array(
	                'name' => 'General',
	                'visible' => 0,
	                'value' => 16
	            ),
	            'personal' => array(
	                'name' => 'Personal',
	                'visible' => 0,
	                'value' => 32
	            ),
	            'jobuser' => array(
	                'name' => 'Job',
	                'visible' => 0,
	                'value' => 64
	            ),
	            'achievement' => array(
	                'name' => 'Achievement',
	                'visible' => 0,
	                'value' => 128
	            ),
	            'addachievement' => array(
	                'name' => 'Add Achievement',
	                'visible' => 0,
	                'value' => 256
	            ),
	            'deleteachievement' => array(
	                'name' => 'Delete Achievement',
	                'visible' => 0,
	                'value' => 512
	            )
	        )
	    ),

		'group' => array(
				'name' => 'Groups',
				'visible' => 1,
				'log' => 1,
				'icon' => 'fa fa-users',
                'type' => PAGE,
				'action' => array(
						'index' => array(
								'name' => 'List Group',
								'visible' => 1,
								'value' => 1
						),
						'lstgroup' => array(
								'name' => 'Search',
								'visible' => 0,
								'value' => 1
						),
						'new' => array(
								'name' => 'New',
								'visible' => 0,
								'value' => 2
						),
						'upd' => array(
								'name' => 'Update',
								'visible' => 0,
								'value' => 4
						),
						'delete' => array(
								'name' => 'Delete',
								'visible' => 0,
								'value' => 8
						),
						'edit' => array(
								'name' => 'Edit',
								'visible' => 0,
								'value' => 32
						)
				)
		),
		'project' => array(
				'name' => 'Projects',
				'visible' => 1,
				'log' => 1,
				'icon' => 'fa fa-thumb-tack',
                'type' => PAGE,
				'action' => array(
						'index' => array(
								'name' => 'List Project',
								'visible' => 1,
								'value' => 1
						),
						'lstproject' => array(
								'name' => '',
								'visible' => 0,
								'value' => 1
						),
						'add' => array(
								'name' => 'Add',
								'visible' => 0,
								'value' => 2
						),
						'delete' => array(
								'name' => 'Delete',
								'visible' => 0,
								'value' => 4
						)
				)
		),
		'actionlogs' => array(
				'name' => 'Action Log',
				'visible' => 1,
				'log' => 1,
				'icon' => 'fa fa-sign-out',
                'type' => PAGE,
				'action' => array(
						'index' => array(
								'name' => 'Action Log',
								'visible' => 1,
								'value' => 1
						),
						'lstproject' => array(
								'name' => '',
								'visible' => 0,
								'value' => 1
						)
				)
		),

        'calendar' => array(
            'name' => 'Calendar',
            'visible' => 1,
            'log' => 1,
            'icon' => 'fa fa-calendar',
            'type' => PAGE,
            'action' => array(
                'index' => array(
                    'name' => 'Calender',
                    'visible' => 1,
                    'value' => 1
                )
            )
        ),

		'general' => array(
				'name' => 'Employee Designations',
				'visible' => 1,
				'log' => 1,
				'icon' => 'fa fa-cloud',
                'type' => PAGE,
				'action' => array(
						'index' => array(
								'name' => 'List',
								'visible' => 1,
								'value' => 1
						),
						'lstgeneral' => array(
								'name' => '',
								'visible' => 0,
								'value' => 1
						),
						'edit' => array(
								'name' => 'Edit',
								'visible' => 0,
								'value' => 2
						),
						'add' => array(
								'name' => 'Add',
								'visible' => 0,
								'value' => 4
						),
						'delete' => array(
								'name' => 'Delete',
								'visible' => 0,
								'value' => 8
						)
				)
		),
		'news' => array(
				'name' => 'News',
				'visible' => 1,
				'log' => 1,
				'icon' => 'fa fa-newspaper-o',
                'type' => PAGE,
				'action' => array(
						'index' => array(
								'name' => 'List',
								'visible' => 1,
								'value' => 1
						),
						'lstnews' => array(
								'name' => '',
								'visible' => 0,
								'value' => 1
						),
						'add' => array(
								'name' => 'Add',
								'visible' => 0,
								'value' => 2
						),
						'delete' => array(
								'name' => 'Delete',
								'visible' => 0,
								'value' => 4
						),
						'edit' => array(
								'name' => 'Edit',
								'visible' => 0,
								'value' => 8
						)
				)
		),
        'workflow' => array(
            'name' => 'Workflows',
            'visible' => 1,
            'log' => 1,
            'icon' => 'fa fa-dot-circle-o',
            'type' => PAGE,
            'action' => array(
                'index' => array(
                    'name' => 'Workflows',
                    'visible' => 1,
                    'value' => 1
                )
            )
        ),
		'album' => array(
				'name' => 'Gallery',
				'visible' => 1,
				'log' => 1,
				'icon' => 'fa fa-picture-o',
                'type' => PAGE,
				'action' => array(
						'index' => array(
								'name' => 'List',
								'visible' => 1,
								'value' => 1
						),
						'add' => array(
								'name' => 'Add',
								'visible' => 0,
								'value' => 2
						),
						'delete' => array(
								'name' => 'Delete',
								'visible' => 0,
								'value' => 4
						),
						'updatedetail' => array(
								'name' => 'Edit',
								'visible' => 0,
								'value' => 8
						),
						'detail' => array(
								'name' => 'Detail',
								'visible' => 0,
								'value' => 16
						)
				)
		),
		'leaveapplication' => array(
				'name' => 'Leave Application',
				'visible' => 1,
				'log' => 1,
				'icon' => 'fa fa-flag',
                'type' => PAGE,
				'action' => array(
						'index' => array(
								'name' => 'List',
								'visible' => 1,
								'value' => 1
						),
						'lstleaveapplication' => array(
								'name' => '',
								'visible' => 0,
								'value' => 1
						),
						'add' => array(
								'name' => 'Add',
								'visible' => 0,
								'value' => 2
						),
						'delete' => array(
								'name' => 'Delete',
								'visible' => 0,
								'value' => 4
						),
						'edit' => array(
								'name' => 'Edit',
								'visible' => 0,
								'value' => 8
						)
				)
		),
        'specialday' => array(
            'name' => 'Special Days',
            'visible' => 1,
            'log' => 1,
            'icon' => 'fa fa-certificate',
            'type' => PAGE,
            'action' => array(
                'index' => array(
                    'name' => 'List Special Day',
                    'visible' => 1,
                    'value' => 1,
                    'param'=>'type=1'
                ),
                'lstspecialday' => array(
                    'name' => 'Search',
                    'visible' => 0,
                    'value' => 1
                ),
                'add' => array(
                    'name' => 'New',
                    'visible' => 0,
                    'value' => 2
                ),
                'edit' => array(
                    'name' => 'Edit',
                    'visible' => 0,
                    'value' => 4
                ),
                'delete' => array(
                    'name' => 'Delete',
                    'visible' => 0,
                    'value' => 8
                )
            )
        ),
		'test' => array(
				'name' => 'test',
				'visible' => 0,
				'log' => 1,
				'icon' => '',
                'type' => PAGE,
				'action' => array(
						'index' => array(
								'name' => 'index',
								'visible' => 0,
								'value' => 1
						)
				)
		),

	    'ajax' => array(
	        'name' => '',
	        'visible' => 0,
	        'log' => 0,
	    ),
	    'login' => array(
	        'name' => '',
	        'visible' => 0,
	        'log' => 1,
	    ),
	    'logout' => array(
	        'name' => '',
	        'visible' => 0,
	        'log' => 1,
	    ),

        'admin' => array(
            'name' => 'Admin',
            'visible' => 1,
            'log' => 1,
            'icon' => 'fa fa-lock',
            'type' => TOOL,
            'action' => array(
                'index' => array(
                    'name' => 'List',
                    'visible' => 1,
                    'value' => 1
                ),
                'list' => array(
                    'name' => '',
                    'visible' => 0,
                    'value' => 1
                ),
                'add-admin' => array(
                    'name' => 'Add',
                    'visible' => 0,
                    'value' => 2
                ),
                'get-team-name-by-team-id' => array(
                    'name' => '',
                    'visible' => 0,
                    'value' => 2
                ),
                'manage-role' => array(
                    'name' => 'Manage Role',
                    'visible' => 0,
                    'value' => 4
                ),
                'create-role' => array(
                    'name' => 'Create Role',
                    'visible' => 0,
                    'value' => 8
                ),
                'delete' => array(
                    'name' => 'Delete Admin',
                    'visible' => 0,
                    'value' => 16
                ),
                'edit' => array(
                    'name' => 'Update Admin',
                    'visible' => 0,
                    'value' => 32
                )
            )
        ),
        'absence' => array(
            'name' => 'Absence',
            'visible' => 1,
            'log' => 1,
            'icon' => 'fa fa-bullhorn',
            'type' => TOOL,
            'action' => array(
                'index' => array(
                    'name' => 'List Absence',
                    'visible' => 1,
                    'value' => 1
                ),
                'lstabsence' => array(
                    'name' => 'Search',
                    'visible' => 0,
                    'value' => 1
                ),
                'detail' => array(
                    'name' => 'Detail',
                    'visible' => 0,
                    'value' => 2
                ),
                'lstabsencedetail' => array(
                    'name' => 'Search Detail',
                    'visible' => 0,
                    'value' => 2
                )
            )
        ),

    'mail' => array(
        'name' => 'Mail',
        'visible' => 1,
        'log' => 1,
        'icon' => 'fa fa-envelope-o',
        'type' => TOOL,
        'action' => array(
            'index' => array(
                'name' => 'index',
                'visible' => 1,
                'value' => 32
            ),
            'inbox' => array(
                'name' => 'inbox',
                'visible' => 0,
                'value' => 1
            ),
            'lstMail' => array(
                'name' => 'lstMail',
                'visible' => 0,
                'value' => 1
            ),
            'sent' => array(
                'name' => 'Sent',
                'visible' => 0,
                'value' => 2
            ),
            'lstSentMail' => array(
                'name' => 'lstSentMail',
                'visible' => 0,
                'value' => 2
            ),
            'draft' => array(
                'name' => 'Draft',
                'visible' => 0,
                'value' => 4
            ),
            'lstDraftMail' => array(
                'name' => 'lstDraftMail',
                'visible' => 0,
                'value' => 4
            ),
            'detail' => array(
                'name' => 'Detail',
                'visible' => 0,
                'value' => 8
            ),
            'delete' => array(
                'name' => 'Delete',
                'visible' => 0,
                'value' => 16
            )



        )
    )
);

