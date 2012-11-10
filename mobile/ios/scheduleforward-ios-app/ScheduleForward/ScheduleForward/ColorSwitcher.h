//
//  ColourSwitcher.h
//  blogplex
//
//  Created by Thomas Smallwood on 8/15/12.
//  Copyright (c) 2012 Forward Intel LLC. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface ColorSwitcher : NSObject

-(id)initWithScheme:(NSString*)scheme;

@property (nonatomic, retain) UIColor* tintColor;

@property (nonatomic, assign) float hue;

@property (nonatomic, assign) float saturation;

@property (nonatomic, retain) NSMutableDictionary* processedImages;

-(UIImage*)processImageWithName:(NSString*)imageName;

//-(UIImage*)mask:(UIImage*)image;

@end
