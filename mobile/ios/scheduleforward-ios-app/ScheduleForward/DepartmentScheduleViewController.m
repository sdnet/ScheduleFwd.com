//
//  DepartmentScheduleViewController.m
//  MedSched
//
//  Created by Thomas Smallwood on 8/15/12.
//  Copyright (c) 2012 Forward Intel LLC. All rights reserved.
//

#import "DepartmentScheduleViewController.h"
#import "DepartmentScheduleListTableViewController.h"

@interface DepartmentScheduleViewController ()

@end

@implementation DepartmentScheduleViewController

@synthesize disableCal;

- (id)init
{
    self = [super init];
    if (self) {
        // Custom initialization
        
        self.disableCal = NO;
        
        [self.navigationController setTitle:@"Department Shifts"];
        [self.tabBarItem setImage:[UIImage imageNamed:@"104-index-cards.png"]];
        [self.tabBarItem setTitle:@"Department Shifts"];
    }
    return self;
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
    
    self.title = @"Department Shifts";
    
    UIBarButtonItem *todayButton = [[UIBarButtonItem alloc] initWithTitle:@"Today" style:UIBarButtonItemStyleBordered target:self action:@selector(todayPressed)];
    [self.navigationItem setLeftBarButtonItem:todayButton];
}

- (void)viewDidAppear:(BOOL)animated {
    [super viewDidAppear:animated];
    self.disableCal = NO;
}

- (void)viewDidUnload
{
    [super viewDidUnload];
    // Release any retained subviews of the main view.
    // e.g. self.myOutlet = nil;
}

- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation
{
    return (interfaceOrientation == UIInterfaceOrientationPortrait);
}

- (void)todayPressed {
    [self.monthView selectDate:[NSDate date]];
}

- (NSArray*)calendarMonthView:(TKCalendarMonthView*)monthView marksFromDate:(NSDate*)startDate toDate:(NSDate*)lastDate{
	return nil;
}

- (void) calendarMonthView:(TKCalendarMonthView*)monthView didSelectDate:(NSDate*)date{
	
    if (!self.disableCal) {
        self.disableCal = YES;
        
        // CHANGE THE DATE TO YOUR TIMEZONE
        TKDateInformation info = [date dateInformationWithTimeZone:[NSTimeZone timeZoneForSecondsFromGMT:0]];
        NSDate *myTimeZoneDay = [NSDate dateFromDateInformation:info timeZone:[NSTimeZone systemTimeZone]];
        
        NSLog(@"Date Selected here: %@",myTimeZoneDay);
        
        DepartmentScheduleListTableViewController *departmentListVC = [[DepartmentScheduleListTableViewController alloc] initWithNibName:@"DepartmentScheduleListTableViewController" bundle:nil];
        [departmentListVC setSlectedDate:myTimeZoneDay];
        [self.navigationController pushViewController:departmentListVC animated:YES];
    }
	
}

- (void) calendarMonthView:(TKCalendarMonthView*)mv monthDidChange:(NSDate*)d animated:(BOOL)animated{
	[super calendarMonthView:mv monthDidChange:d animated:animated];
	[self.tableView reloadData];
}


- (NSInteger) numberOfSectionsInTableView:(UITableView *)tableView {
	return 1;
	
}
- (NSInteger) tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section {
	return 0;
}


- (UITableViewCell *) tableView:(UITableView *)tv cellForRowAtIndexPath:(NSIndexPath *)indexPath {
    
    static NSString *CellIdentifier = @"Cell";
    UITableViewCell *cell = [tv dequeueReusableCellWithIdentifier:CellIdentifier];
    if (cell == nil) cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:CellIdentifier];
    
	
    return cell;
}

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath {
    
}

@end
