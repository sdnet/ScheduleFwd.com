//
//  CalendarMonthViewController.m
//  MedSched
//
//  Created by Thomas Smallwood on 8/15/12.
//  Copyright (c) 2012 Forward Intel LLC. All rights reserved.
//

#import "MyCalendarMonthViewController.h"
#import "MyShiftDetailsViewController.h"


@interface MyCalendarMonthViewController ()

@end

@implementation MyCalendarMonthViewController

@synthesize dataArray;
@synthesize dataDictionary;
@synthesize pinValidated;

@synthesize loggedIn;

- (id) init {
    self = [super init];
    if (self) {
        loggedIn = NO;
        
        [self.navigationController setTitle:@"My Shifts"];
        [self.tabBarItem setTitle:@"My Shifts"];
        [self.tabBarItem setImage:[UIImage imageNamed:@"83-calendar.png"]];
    }
    return self;
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
    
    
    
    self.title = @"My Shifts";
    
    UIBarButtonItem *todayButton = [[UIBarButtonItem alloc] initWithTitle:@"Today" style:UIBarButtonItemStyleBordered target:self action:@selector(todayPressed)];
    [self.navigationItem setLeftBarButtonItem:todayButton];
}

- (void)viewWillAppear:(BOOL)animated {
    [super viewWillAppear:animated];
     if (!loggedIn) {
            
         self.loggedIn = YES;
            
         LoginViewController *loginVC = [[LoginViewController alloc] initWithNibName:@"LoginViewController" bundle:nil];
            
            
         [self presentViewController:loginVC animated:NO completion:nil];
    }
}

- (void)dismissHUDWithLogInSuccess:(id)arg {
    
    //[MBProgressHUD hideHUDForView:self.view animated:YES];
    //self.hud = nil;
    
    NSLog(@"success!!!!");

}

- (void)dismissHUDWithLogInFailure:(id)arg {
    //[MBProgressHUD hideHUDForView:self.view animated:YES];
    
    //failed
}

- (void) viewDidAppear:(BOOL)animated{
	[super viewDidAppear:animated];
    
}

- (void)viewDidUnload
{
    [super viewDidUnload];
    
    [self setDataArray:nil];
    [self setDataDictionary:nil];
}

- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation
{
    return (interfaceOrientation == UIInterfaceOrientationPortrait);
}

- (void)todayPressed {
    [self.monthView selectDate:[NSDate date]];
}

- (NSArray*)calendarMonthView:(TKCalendarMonthView*)monthView marksFromDate:(NSDate*)startDate toDate:(NSDate*)lastDate{
	[self generateRandomDataForStartDate:startDate endDate:lastDate];
	return dataArray;
}

- (void) calendarMonthView:(TKCalendarMonthView*)monthView didSelectDate:(NSDate*)date{
	
	// CHANGE THE DATE TO YOUR TIMEZONE
	TKDateInformation info = [date dateInformationWithTimeZone:[NSTimeZone timeZoneForSecondsFromGMT:0]];
	NSDate *myTimeZoneDay = [NSDate dateFromDateInformation:info timeZone:[NSTimeZone systemTimeZone]];
	
	NSLog(@"Date Selected: %@",myTimeZoneDay);
	
	[self.tableView reloadData];
}

- (void) calendarMonthView:(TKCalendarMonthView*)mv monthDidChange:(NSDate*)d animated:(BOOL)animated{
	[super calendarMonthView:mv monthDidChange:d animated:animated];
	[self.tableView reloadData];
}

- (CGFloat)tableView:(UITableView *)tableView heightForHeaderInSection:(NSInteger)section {
    NSDictionary *ar = [dataDictionary objectForKey:[self.monthView dateSelected]];
	if(ar == nil) return 0;
    
    return 22;
}


- (NSInteger) numberOfSectionsInTableView:(UITableView *)tableView {
	return 1;
	
}
- (NSInteger) tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section {
	NSDictionary *ar = [dataDictionary objectForKey:[self.monthView dateSelected]];
	if(ar == nil) return 0;
	return 1;
}

-(NSString *)tableView:(UITableView *)tableView titleForHeaderInSection:(NSInteger)section {
    NSDictionary *ar = [dataDictionary objectForKey:[self.monthView dateSelected]];
	if(ar == nil) return nil;
    
    return [ar objectForKey:@"Department"];
}

- (UITableViewCell *) tableView:(UITableView *)tv cellForRowAtIndexPath:(NSIndexPath *)indexPath {
    
    static NSString *CellIdentifier = @"Cell";
    UITableViewCell *cell = [tv dequeueReusableCellWithIdentifier:CellIdentifier];
    if (cell == nil) cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:CellIdentifier];
    
    [cell setAccessoryType:UITableViewCellAccessoryDetailDisclosureButton];
    
	NSDictionary *ar = [dataDictionary objectForKey:[self.monthView dateSelected]];
	cell.textLabel.text = [ar objectForKey:@"Time"];
	
    return cell;
}


- (void) generateRandomDataForStartDate:(NSDate*)start endDate:(NSDate*)end {
    self.dataArray = [NSMutableArray array];
	self.dataDictionary = [NSMutableDictionary dictionary];
	
	NSDate *d = start;
	while(YES){
		
		int r = arc4random();
		if(r % 3==1){
			[self.dataDictionary setObject:[NSDictionary dictionaryWithObjectsAndKeys:@"Peds Emergency Department", @"Department", @"6am - 6pm", @"Time", nil] forKey:d];
			[self.dataArray addObject:[NSNumber numberWithBool:YES]];
			
		}else if(r%4==1){
			[self.dataDictionary setObject:[NSDictionary dictionaryWithObjectsAndKeys:@"Adult  Emergency Department", @"Department", @"7pm - 7am", @"Time", nil] forKey:d];
			[self.dataArray addObject:[NSNumber numberWithBool:YES]];
			
		}else
			[self.dataArray addObject:[NSNumber numberWithBool:NO]];
		
		
		TKDateInformation info = [d dateInformationWithTimeZone:[NSTimeZone timeZoneForSecondsFromGMT:0]];
		info.day++;
		d = [NSDate dateFromDateInformation:info timeZone:[NSTimeZone timeZoneForSecondsFromGMT:0]];
		if([d compare:end]==NSOrderedDescending) break;
    }
}

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath {
    MyShiftDetailsViewController *myShiftDetailsVC = [[MyShiftDetailsViewController alloc] initWithNibName:@"MyShiftDetailsViewController" bundle:nil];
    [self.navigationController pushViewController:myShiftDetailsVC animated:YES];
}

@end
