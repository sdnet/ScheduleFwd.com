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

static NSString *API_ENDPOINT_LOGIN = @"http://schedulefwd.com/ws/LoginUser";

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
    NSURLRequest *request = [NSURLRequest requestWithURL:[NSURL URLWithString:API_ENDPOINT_LOGIN]];
    AFJSONRequestOperation *operation = [AFJSONRequestOperation JSONRequestOperationWithRequest:request success:^(NSURLRequest *request, NSHTTPURLResponse *response, id JSON) {
        NSLog(@"Name: %@ %@", [JSON valueForKeyPath:@"first_name"], [JSON valueForKeyPath:@"last_name"]);
    } failure:nil];
    
    [operation start];
    
    return NO;
}

- (NSDictionary *)fetchGroupCodesAndNames {
    return nil;
}

@end
