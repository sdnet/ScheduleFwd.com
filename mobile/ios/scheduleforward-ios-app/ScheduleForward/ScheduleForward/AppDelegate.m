//
//  AppDelegate.m
//  ScheduleForward
//
//  Created by Thomas Smallwood on 8/15/12.
//  Copyright (c) 2012 Forward Intel LLC. All rights reserved.
//

#import "AppDelegate.h"
#import "MyCalendarMonthViewController.h"
#import "DepartmentScheduleViewController.h"
#import "UserSearchTableViewController.h"


@implementation AppDelegate

@synthesize colorSwitcher;
@synthesize tabBarController;

- (BOOL)application:(UIApplication *)application didFinishLaunchingWithOptions:(NSDictionary *)launchOptions
{
    self.window = [[UIWindow alloc] initWithFrame:[[UIScreen mainScreen] bounds]];
    // Override point for customization after application launch.
    
    [[UIApplication sharedApplication] setApplicationIconBadgeNumber:0];
    
    //Init Airship launch options
    NSMutableDictionary *takeOffOptions = [[NSMutableDictionary alloc] init];
    [takeOffOptions setValue:launchOptions forKey:UAirshipTakeOffOptionsLaunchOptionsKey];
    
    // Create Airship singleton that's used to talk to Urban Airship servers.
    // Please populate AirshipConfig.plist with your info from http://go.urbanairship.com
    [UAirship takeOff:takeOffOptions];
    
    // Register for notifications
    [[UAPush shared]
     registerForRemoteNotificationTypes:(UIRemoteNotificationTypeBadge |
                                         UIRemoteNotificationTypeSound |
                                         UIRemoteNotificationTypeAlert)];
    
    
    self.window.backgroundColor = [UIColor whiteColor];
    [self.window makeKeyAndVisible];
    
    self.colorSwitcher = [[ColorSwitcher alloc] initWithScheme:@"black"];
    
    [self customizeGlobalTheme];
    
    MyCalendarMonthViewController *calendarMonthViewController = [[MyCalendarMonthViewController alloc] init];
    
    UINavigationController *calendarNavController = [[UINavigationController alloc] initWithRootViewController:calendarMonthViewController];
    
    DepartmentScheduleViewController *departmentScheduleViewController = [[DepartmentScheduleViewController alloc] init];
    
    UINavigationController *departmentScheduleNavController = [[UINavigationController alloc] initWithRootViewController:departmentScheduleViewController];
    
    UserSearchTableViewController *userSearchTVC = [[UserSearchTableViewController alloc] initWithNibName:@"UserSearchTableViewController" bundle:nil];
    
    UINavigationController *userSearchNavController = [[UINavigationController alloc] initWithRootViewController:userSearchTVC];
    
    self.tabBarController = [[UITabBarController alloc] init];
    
    [userSearchNavController.tabBarItem setTitle:@"Search Users"];
    [userSearchNavController.tabBarItem setImage:[UIImage imageNamed:@"06-magnify.png"]];
    
    self.tabBarController.viewControllers = @[ calendarNavController, departmentScheduleNavController, userSearchNavController ];
    
    self.window.rootViewController = self.tabBarController;
    
    
    return YES;
}

- (void)customizeGlobalTheme
{
    UIImage *navBarImage = [colorSwitcher processImageWithName:@"menu-bar.png"];
    
    [[UINavigationBar appearance] setBackgroundImage:navBarImage
                                       forBarMetrics:UIBarMetricsDefault];
    
}


- (void)application:(UIApplication *)application didRegisterForRemoteNotificationsWithDeviceToken:(NSData *)deviceToken {
    // Updates the device token and registers the token with UA
    [[UAPush shared] registerDeviceToken:deviceToken];
}

- (void)applicationWillResignActive:(UIApplication *)application
{
    // Sent when the application is about to move from active to inactive state. This can occur for certain types of temporary interruptions (such as an incoming phone call or SMS message) or when the user quits the application and it begins the transition to the background state.
    // Use this method to pause ongoing tasks, disable timers, and throttle down OpenGL ES frame rates. Games should use this method to pause the game.
}

- (void)applicationDidEnterBackground:(UIApplication *)application
{
    // Use this method to release shared resources, save user data, invalidate timers, and store enough application state information to restore your application to its current state in case it is terminated later.
    // If your application supports background execution, this method is called instead of applicationWillTerminate: when the user quits.
}

- (void)applicationWillEnterForeground:(UIApplication *)application
{
    // Called as part of the transition from the background to the inactive state; here you can undo many of the changes made on entering the background.
}

- (void)applicationDidBecomeActive:(UIApplication *)application
{
    // Restart any tasks that were paused (or not yet started) while the application was inactive. If the application was previously in the background, optionally refresh the user interface.
}

- (void)applicationWillTerminate:(UIApplication *)application
{
    // Called when the application is about to terminate. Save data if appropriate. See also applicationDidEnterBackground:.
    
    [UAirship land];
}

@end
