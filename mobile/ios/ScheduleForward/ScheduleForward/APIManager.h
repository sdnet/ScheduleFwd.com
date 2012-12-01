//
//  APIManager.h
//  ScheduleForward
//
//  Created by Thomas Smallwood on 11/25/12.
//  Copyright (c) 2012 Thomas Smallwood. All rights reserved.
//

#import <Foundation/Foundation.h>
#import "SelectHospitalViewController_iPhone.h"

@interface APIManager : NSObject

+ (APIManager *)sharedApiManager;
- (BOOL)setUserName:(NSString *)userName andPassword:(NSString *)password;
- (void)fetchGroupCodesAndNamesFor:(SelectHospitalViewController_iPhone *)controller;

@end
