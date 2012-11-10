//
//  APIManager.m
//  MedSched
//
//  Created by Thomas Smallwood on 8/15/12.
//  Copyright (c) 2012 Forward Intel LLC. All rights reserved.
//

#import "APIManager.h"
#import "ASIFormDataRequest.h"
#import "SBJson/SBJson.h"
#import "NSDictionary+ApiManager.h"
#import "NSDataAdditions.h"


@implementation APIManager

static NSString *API_ENDPOINT_LOGIN = @"http://scheduleforward.com/ws/LoginUser";

static NSString *API_ENDPOINT_GROUPCODES = @"http://scheduleforward.com/ws/getGroupCodes";

static NSString *API_ENDPOINT_ALLUSERS = @"http://scheduleforward.com/ws/getUsers";

static NSString *API_ENDPOINT_GETUSER = @"http://scheduleforward.com/ws/getUser";

static APIManager *sharedInstance = nil;


static NSString *F_LOGIN_USER                = @"log_in_user";
static NSString *F_LOGOUT_USER               = @"log_out_user";
static NSString *F_GET_USER_FIELD            = @"get_user_field";
static NSString *F_UPDATE_USER_FIELD         = @"update_user_field";
static NSString *F_FORGOT_PASSWORD           = @"forgot_password";

static NSString *F_IS_VALID_SESSION          = @"valid_session";

static NSString *POST_FUNCTION = @"submit";
static NSString *POST_USERNAME = @"username";
static NSString *POST_PASSWORD = @"password";
static NSString *GRP_CODE = @"grpcode";
static NSString *POST_FIRSTNAME = @"firstname";
static NSString *POST_LASTNAME = @"lastname";
static NSString *POST_SESSION = @"sessionId";
static NSString *POST_ID = @"id";
static NSString *POST_FIELD = @"field";
static NSString *POST_VALUE = @"value";

@synthesize password = _password;
@synthesize username = _username;
@synthesize groupcode = _groupcode;
@synthesize groupDict = _groupDict;
@synthesize keychainUserPass;

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

#pragma mark - Private Methods

- (void)setSessionId:(NSString *)sessionId {
    [[NSUserDefaults standardUserDefaults] setObject:sessionId forKey:@"session_id"];
    [[NSUserDefaults standardUserDefaults] synchronize];
}

- (NSString *)sessionId {
    NSString *session_Id = [[NSUserDefaults standardUserDefaults] objectForKey:@"session_id"];
    
    return session_Id;
}

- (void)clearSessionId {
    [[NSUserDefaults standardUserDefaults] setObject:nil forKey:@"session_id"];
    [[NSUserDefaults standardUserDefaults] synchronize];
}

- (void)setUsername:(NSString *)username password:(NSString *)password groupCode:(NSString *)groupCode {
    NSError *error = nil;
    NSString *user = username;
    NSString *pass = password;
    NSString *groupcode = groupCode;
    
    [[NSUserDefaults standardUserDefaults] setObject:username forKey:@"username"];
    [[NSUserDefaults standardUserDefaults] setObject:groupcode forKey:@"groupcode"];
    [[NSUserDefaults standardUserDefaults] synchronize];
    
    [SFHFKeychainUtils storeUsername:user andPassword:pass forServiceName:@"ScheduleForward" updateExisting:TRUE error:&error];
}

- (NSString *)password {
    NSError *error = nil;
    NSString *userName = [[NSUserDefaults standardUserDefaults] objectForKey:@"username"];
    NSString *pass = [SFHFKeychainUtils getPasswordForUsername:userName andServiceName:@"ScheduleForward" error:&error];
    return pass;
}

- (NSString *)groupcode {
    NSString *groupCode = [[NSUserDefaults standardUserDefaults] objectForKey:@"groupcode"];
    return groupCode;
}

- (NSString *)username {
    NSString *userName = [[NSUserDefaults standardUserDefaults] objectForKey:@"username"];
    return userName;
}

- (void)clearPassword {
    NSError *error = nil;
    NSString *userName = [[NSUserDefaults standardUserDefaults] objectForKey:@"username"];
    [SFHFKeychainUtils storeUsername:userName andPassword:nil forServiceName:@"ScheduleForward" updateExisting:TRUE error:&error];
}

- (void)sendFireAndForgetPostRequest:(NSDictionary *)postValues {
    NSURL *url = [NSURL URLWithString:API_ENDPOINT_LOGIN];
    ASIFormDataRequest *request = [ASIFormDataRequest requestWithURL:url];
    for (NSString *key in [postValues allKeys]) {
        [request setPostValue:[postValues objectForKey:key] forKey:key];
    }
    [request startAsynchronous];
}

- (NSString *)sendPostRequest:(NSDictionary *)postValues withURL:(NSString *)urlString {
    NSURL *url = [NSURL URLWithString:urlString];
    ASIFormDataRequest *request = [ASIFormDataRequest requestWithURL:url];
    for (NSString *key in [postValues allKeys]) {
        [request setPostValue:[postValues objectForKey:key] forKey:key];
    }
    [request startSynchronous];
    NSError *error = [request error];
    NSString *response;
    if (error) {
        NSLog(@"Request error during login: %@", error);
        response = @"{\"status\":\"failure\",\"error\":\"general request error\"}";
    }
    else {
        response = [request responseString];
    }
    if ([response isEqualToString:@"\"invalid - no function\""]) {
        NSLog(@"Make sure you specify the function parameter 'f' in your POST variables.");
        response = @"{\"status\":\"failure\",\"error\":\"missing function param\"}";
    }
    
    return response;
}


- (BOOL)loginWithUsername:(NSString *)username password:(NSString *)password groupCode:(NSString *)groupCode {
    NSLog(@"USername: %@", username);
    NSLog(@"GC: %@", groupCode);
    NSLog(@"pass: %@", password);
    BOOL apiCallSuccess = NO;
    NSDictionary *params = [NSDictionary dictionaryWithObjectsAndKeys:username, POST_USERNAME, password, POST_PASSWORD, groupCode, GRP_CODE, @"true", POST_FUNCTION, nil];
    NSString *response = [self sendPostRequest:params withURL:API_ENDPOINT_LOGIN];
    NSDictionary *responseDict = [response JSONValue];
    apiCallSuccess = [responseDict apiCallSuccess];
    
    if (apiCallSuccess) {
        [self setUsername:username password:password groupCode:groupCode];
        NSString *session_Id = [[responseDict objectForKey:@"data"] objectForKey:@"sessionId"];
        if (session_Id != nil) {
            [self setSessionId:session_Id];
        }
    }
    return apiCallSuccess;
}

- (NSDictionary *)fetchGroupCodes {
    if (self.groupDict == nil) {
        NSString *response = [self sendPostRequest:nil withURL:API_ENDPOINT_GROUPCODES];
        NSDictionary *responseDict = [response JSONValue];
        NSLog(@"%@", responseDict);
        return responseDict;
    }
    return self.groupDict;
}

- (NSArray *)fetchAllUsers {
    NSString *session = [self sessionId];
    NSString *gCode = [self groupcode];
    
    NSDictionary *params = [NSDictionary dictionaryWithObjectsAndKeys:session, POST_SESSION, gCode, GRP_CODE, nil];
    
    NSString *response = [self sendPostRequest:params withURL:API_ENDPOINT_ALLUSERS];
    NSDictionary *responseDict = [response JSONValue];
    
    NSArray *usersArray = [responseDict objectForKey:@"data"];
    
    return usersArray;
}

- (NSDictionary *)userDetailsForUserId:(NSString *)userId {
    NSString *session = [self sessionId];
    
    NSString *groupCode = [self groupcode];
    
    NSLog(@"uid: %@", userId);
    NSLog(@"session_ID: %@", session);
    NSLog(@"GCODE: %@", groupCode);
    
    NSDictionary *params = [NSDictionary dictionaryWithObjectsAndKeys:session, POST_SESSION, groupCode, GRP_CODE, userId, POST_ID, nil];
    
    NSString *response = [self sendPostRequest:params withURL:API_ENDPOINT_GETUSER];
    
    NSLog(@"res: %@", response);
    
    
    NSDictionary *responseDict = [response JSONValue];
    
    return responseDict;
}


@end
