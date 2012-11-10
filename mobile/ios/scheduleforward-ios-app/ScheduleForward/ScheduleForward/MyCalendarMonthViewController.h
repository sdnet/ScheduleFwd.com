//
//  CalendarMonthViewController.h
//  MedSched
//
//  Created by Thomas Smallwood on 8/15/12.
//  Copyright (c) 2012 Forward Intel LLC. All rights reserved.
//

#import <UIKit/UIKit.h>
#import <TapkuLibrary/TapkuLibrary.h>
#import "LoginViewController.h"
#import "APIManager.h"


@interface MyCalendarMonthViewController : TKCalendarMonthTableViewController

@property (nonatomic) BOOL loggedIn;

@property (nonatomic, strong) NSMutableArray *dataArray;
@property (nonatomic, strong) NSMutableDictionary *dataDictionary;
@property (nonatomic) BOOL pinValidated;

- (void) generateRandomDataForStartDate:(NSDate*)start endDate:(NSDate*)end;


@end
