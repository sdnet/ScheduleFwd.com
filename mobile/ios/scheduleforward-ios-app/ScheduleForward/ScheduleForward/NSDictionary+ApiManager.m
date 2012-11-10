//
//  NSDictionary+ApiManager.m
//  QRApp
//
//  Created by Thomas Smallwood on 8/15/12.
//  Copyright (c) 2012 Forward Intel LLC. All rights reserved.
//

#import "NSDictionary+ApiManager.h"

@implementation NSDictionary (ApiManager)

- (BOOL)apiCallSuccess {
    NSString *status = [self objectForKey:@"message"];
    if ([[status lowercaseString] isEqualToString:@"success"]) {
        return YES;
    }
    return NO;
}

@end
