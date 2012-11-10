//
//  UserDetailViewController.h
//  ScheduleForward
//
//  Created by Thomas Smallwood on 8/24/12.
//  Copyright (c) 2012 Forward Intel LLC. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface UserDetailViewController : UIViewController

@property (strong, nonatomic) IBOutlet UITextView *detailsTextView;
@property (nonatomic, strong) NSDictionary *userDict;
@end
