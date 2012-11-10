//
//  DepartmentScheduleListTableViewController.h
//  ScheduleForward
//
//  Created by Thomas Smallwood on 8/15/12.
//  Copyright (c) 2012 Forward Intel LLC. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface DepartmentScheduleListTableViewController : UITableViewController

@property (nonatomic,strong) NSDate *slectedDate;
@property (strong, nonatomic) IBOutlet UITableViewCell *topCustomCell;
@end
