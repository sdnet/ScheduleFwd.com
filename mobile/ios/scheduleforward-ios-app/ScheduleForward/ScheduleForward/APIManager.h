//
//  APIManager.h
//  MedSched
//
//  Created by Thomas Smallwood on 8/15/12.
//  Copyright (c) 2012 Forward Intel LLC. All rights reserved.
//

#import <Foundation/Foundation.h>
#import "SFHFKeychainUtils.h"

static NSString *kSuccessKey = @"success_key";
static NSString *kResultKey = @"result_key";

static NSString *kUsernameKey = @"username_key";
static NSString *kPasswordKey = @"password_key";

static NSString *kUsernameDisplay = @"username";
static NSString *kPasswordDisplay = @"password";

@interface APIManager : NSObject <NSURLConnectionDataDelegate>

@property (nonatomic, strong) NSString *username;
@property (nonatomic, strong) NSString *password;
@property (nonatomic, strong) NSString *groupcode;

@property (nonatomic, strong) SFHFKeychainUtils *keychainUserPass;

@property (nonatomic, strong) NSDictionary *groupDict;

+ (APIManager *)sharedApiManager;

- (void)setUsername:(NSString *)username password:(NSString *)password groupCode:(NSString *)groupCode;
- (BOOL)loginWithUsername:(NSString *)username password:(NSString *)password groupCode:(NSString *)groupCode;

- (NSDictionary *)fetchGroupCodes;
- (NSArray *)fetchAllUsers;

- (NSDictionary *)userDetailsForUserId:(NSString *)userId;


@end
