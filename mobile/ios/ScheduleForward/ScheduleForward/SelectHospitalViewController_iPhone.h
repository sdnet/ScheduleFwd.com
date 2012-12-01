//
//  SelectHospitalViewController_iPhone.h
//  ScheduleForward
//
//  Created by Thomas Smallwood on 11/25/12.
//  Copyright (c) 2012 Thomas Smallwood. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface SelectHospitalViewController_iPhone : UITableViewController <UISearchBarDelegate, UISearchDisplayDelegate>

@property (nonatomic, strong) NSMutableArray *hospitalArray;
@property (nonatomic, strong) NSMutableArray *filteredhospitalArray;
@property (strong, nonatomic) IBOutlet UISearchBar *hospitalSearchBar;

- (void)loadGroups:(NSArray *)groups;

@end
