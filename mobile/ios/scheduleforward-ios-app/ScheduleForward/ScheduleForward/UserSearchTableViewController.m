//
//  UserSearchTableViewController.m
//  ScheduleForward
//
//  Created by Thomas Smallwood on 8/18/12.
//  Copyright (c) 2012 Forward Intel LLC. All rights reserved.
//

#import "UserSearchTableViewController.h"
#import "APIManager.h"
#import "UserDetailViewController.h"

@interface UserSearchTableViewController ()

@end

@implementation UserSearchTableViewController

@synthesize searchBar;

@synthesize userArray;
@synthesize filteredUserArray;

- (id)initWithStyle:(UITableViewStyle)style
{
    self = [super initWithStyle:style];
    if (self) {
        // Custom initialization
        
        NSLog(@"here");
        
    }
    return self;
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    
    [self.navigationController setNavigationBarHidden:YES];
    
    [searchBar setShowsScopeBar:YES];
    [searchBar sizeToFit];
    
//    // Hide the search bar until user scrolls up
//    CGRect newBounds = [[self tableView] bounds];
//    newBounds.origin.y = newBounds.origin.y + searchBar.bounds.size.height;
//    [[self tableView] setBounds:newBounds];
    
    NSArray *usersArray = [[APIManager sharedApiManager] fetchAllUsers];
    
    
    
    self.userArray = usersArray;
    
    // Initialize the filteredUserArray with a capacity equal to the userArray's capacity
    filteredUserArray = [NSMutableArray arrayWithArray:self.userArray];
    
    // Reload the table
    [[self tableView] reloadData];
    
}

- (void)viewDidUnload
{
    [super viewDidUnload];
    // Release any retained subviews of the main view.
    // e.g. self.myOutlet = nil;
}

- (void)viewWillAppear:(BOOL)animated {
    [super viewWillAppear:animated];
    [self.navigationController setNavigationBarHidden:YES animated:YES];
}

- (void)viewWillDisappear:(BOOL)animated {
    [super viewWillDisappear:animated];
    [self.navigationController setNavigationBarHidden:NO animated:YES];
}

- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation
{
    return (interfaceOrientation == UIInterfaceOrientationPortrait);
}

#pragma mark - Table view data source

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
    // Check to see whether the normal table or search results table is being displayed and return the count from the appropriate array
    if (tableView == self.searchDisplayController.searchResultsTableView)
	{
        return [filteredUserArray count];
    }
	else
	{
        return [userArray count];
    }
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    static NSString *CellIdentifier = @"Cell";
    UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
    if ( cell == nil ) {
        cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:CellIdentifier];
    }
    
    NSDictionary *user = nil;
    
    // Check to see whether the normal table or search results table is being displayed and set the User object from the appropriate array
    if (tableView == self.searchDisplayController.searchResultsTableView)
	{
        user = [filteredUserArray objectAtIndex:[indexPath row]];
    }
	else
	{
        user = [userArray objectAtIndex:[indexPath row]];
    }
    
    // Configure the cell
    [[cell textLabel] setText:[NSString stringWithFormat:@"%@ %@", [user objectForKey:@"first_name"], [user objectForKey:@"last_name"]]];
    [cell setAccessoryType:UITableViewCellAccessoryDisclosureIndicator];
    
    return cell;
}

#pragma mark - TableView Delegate

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath
{
    UserDetailViewController *userDetailVC = [[UserDetailViewController alloc] initWithNibName:@"UserDetailViewController" bundle:nil];
    
    NSLog(@"hereya go: %@", [self.filteredUserArray objectAtIndex:indexPath.row]);
    NSString *user = [[[self.filteredUserArray objectAtIndex:indexPath.row] objectForKey:@"_id"] objectForKey:@"$id"];
    
    NSDictionary *dict = [[APIManager sharedApiManager] userDetailsForUserId:user];

    [userDetailVC setUserDict:dict];
    [self.navigationController pushViewController:userDetailVC animated:YES];
}

#pragma mark Content Filtering

- (void)filterContentForSearchText:(NSString*)searchText scope:(NSString*)scope
{
	// Update the filtered array based on the search text and scope.
	
    // Remove all objects from the filtered search array
	[self.filteredUserArray removeAllObjects];
    
	// Filter the array using NSPredicate
    NSPredicate *predicate = [NSPredicate predicateWithFormat:@"SELF.first_name BEGINSWITH[c] %@",searchText];
    NSArray *tempArray = [userArray filteredArrayUsingPredicate:predicate];
    
    filteredUserArray = [NSMutableArray arrayWithArray:tempArray];
    
    if([scope isEqualToString:@"OTHER"]) {
        NSLog(@"other");
        // Further filter the array with the scope
        
        NSPredicate *scopePredicate = [NSPredicate predicateWithFormat:@"SELF.group != [c] %@", @"ATTENDING"];
        tempArray = [tempArray filteredArrayUsingPredicate:scopePredicate];
        
        self.filteredUserArray = [NSMutableArray arrayWithArray:tempArray];
        
        NSLog(@"filterArray: %@", self.filteredUserArray);
    }
    
    if([scope isEqualToString:@"ATTENDING"]) {
        NSLog(@"Attending");
        // Further filter the array with the scope
        NSPredicate *scopePredicate = [NSPredicate predicateWithFormat:@"SELF.group LIKE[c] %@", scope];
        tempArray = [tempArray filteredArrayUsingPredicate:scopePredicate];
        self.filteredUserArray = [NSMutableArray arrayWithArray:tempArray];
    }
}

#pragma mark - UISearchDisplayController Delegate Methods

- (BOOL)searchDisplayController:(UISearchDisplayController *)controller shouldReloadTableForSearchString:(NSString *)searchString
{
    // Tells the table data source to reload when text changes
    [self filterContentForSearchText:searchString scope:
     [[self.searchDisplayController.searchBar scopeButtonTitles] objectAtIndex:[self.searchDisplayController.searchBar selectedScopeButtonIndex]]];
    
    // Return YES to cause the search result table view to be reloaded.
    return YES;
}


- (BOOL)searchDisplayController:(UISearchDisplayController *)controller shouldReloadTableForSearchScope:(NSInteger)searchOption
{
    // Tells the table data source to reload when scope bar selection changes
    [self filterContentForSearchText:[self.searchDisplayController.searchBar text] scope:
     [[self.searchDisplayController.searchBar scopeButtonTitles] objectAtIndex:searchOption]];
    
    // Return YES to cause the search result table view to be reloaded.
    return YES;
}

#pragma mark - Search Button

- (IBAction)goToSearch:(id)sender
{
    // If you're worried that your users might not catch on to the fact that a search bar is available if they scroll to reveal it, a search icon will help them
    // Note that if you didn't hide your search bar, you should probably not include this, as it would be redundant
    [self.searchBar becomeFirstResponder];
}

@end
