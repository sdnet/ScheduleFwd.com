//
//  APIManager.m
//  ScheduleForward
//
//  Created by Thomas Smallwood on 11/25/12.
//  Copyright (c) 2012 Thomas Smallwood. All rights reserved.
//

#import "APIManager.h"
#import "AFNetworking.h"

@implementation APIManager

static APIManager *sharedInstance = nil;

static NSString *API_ENDPOINT = @"http://schedulefwd.com/dev/ws";

+ (APIManager *)sharedApiManager {
    static dispatch_once_t pred;        // Lock
    dispatch_once(&pred, ^{             // This code is called at most once per app
        sharedInstance = [[super allocWithZone:NULL] init];
    });
    return sharedInstance;
}

// first time the Singleton is used.
- (id)init {
    self = [super init];
    
    if (self) {
        
    }
    
    return self;
}

// We don't want to allocate a new instance, so return the current one.
+ (id)allocWithZone:(NSZone*)zone {
    return [self sharedApiManager];
}

// Equally, we don't want to generate multiple copies of the singleton.
- (id)copyWithZone:(NSZone *)zone {
    return self;
}

#pragma mark - Login Methods

- (BOOL)setUserName:(NSString *)userName andPassword:(NSString *)password {
    
    
    return NO;
}

- (void)fetchGroupCodesAndNamesFor:(SelectHospitalViewController_iPhone *)controller {
    NSString *path = [API_ENDPOINT stringByAppendingPathComponent:@"/getGroupCodes"];
    NSURL *url = [NSURL URLWithString:path];
    NSURLRequest *request = [NSURLRequest requestWithURL:url];
    __block NSArray *groupArray = nil;
    
    
    
    AFJSONRequestOperation *operation = [AFJSONRequestOperation JSONRequestOperationWithRequest:request success:^(NSURLRequest *request, NSHTTPURLResponse *response, id JSON) {
        
        if ([[JSON objectForKey:@"message"] isEqualToString:@"success"]) {
            groupArray = [JSON objectForKey:@"data"];
            
            [controller loadGroups:groupArray];
        }
    } failure:^(NSURLRequest *request, NSHTTPURLResponse *response, NSError *error, id JSON) {
        groupArray = nil;
    }];
    [operation start];
}

@end
