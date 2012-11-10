//
//  UserSearchTableViewController.h
//  ScheduleForward
//
//  Created by Thomas Smallwood on 8/18/12.
//  Copyright (c) 2012 Forward Intel LLC. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface UserSearchTableViewController : UITableViewController<UISearchBarDelegate, UISearchDisplayDelegate>

@property (strong, nonatomic) IBOutlet UISearchBar *searchBar;

@property (strong,nonatomic) NSArray *userArray;
@property (strong,nonatomic) NSMutableArray *filteredUserArray;

@end
