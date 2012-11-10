//
//  ColourSwitcher.m
//  blogplex
//
//  Created by Thomas Smallwood on 8/15/12.
//  Copyright (c) 2012 Forward Intel LLC. All rights reserved.
//

#import "ColorSwitcher.h"

@implementation ColorSwitcher

@synthesize tintColor;

@synthesize hue, saturation, processedImages;

-(id)initWithScheme:(NSString*)scheme
{
    self = [super init];
    
    if(self)
    {
        self.processedImages = [NSMutableDictionary dictionary];
        if([scheme isEqualToString:@"black"])
        {         
            hue = 0;
            saturation = 1;
            self.tintColor = [UIColor colorWithRed:150.0/255 green:198.0/255 blue:255.0/255 alpha:1.0];
        }
        else if([scheme isEqualToString:@"maroon"])
        {
            hue = 1.40322;
            saturation = 1.782143;
            self.tintColor = [UIColor colorWithRed:153.0/255 green:107.0/255 blue:136.0/255 alpha:1.0];;
        }
        else if([scheme isEqualToString:@"brown"])
        {
            hue = 0.713114;
            saturation = 0.760714;
            self.tintColor = [UIColor colorWithRed:106.0/255 green:65.0/255 blue:12.0/255 alpha:1.0];
        }
        else if([scheme isEqualToString:@"green"])
        {   
            hue = 3.14;
            saturation = 0.760714;
            self.tintColor = [UIColor colorWithRed:109.0/255 green:137.0/255 blue:34.0/255 alpha:1.0];
        }
        
    }
    
    return self;
}


-(UIImage*)processImageWithName:(NSString*)imageName
{
    UIImage* existingImage = [processedImages objectForKey:imageName];
    
    if(existingImage)
    {
        return existingImage;
    }
    
    UIImage* originalImage = [UIImage imageNamed:imageName];
    
    CIImage *beginImage = [CIImage imageWithData:UIImagePNGRepresentation(originalImage)];
    
    CIContext* context = [CIContext contextWithOptions:nil];
    
    CIFilter* hueFilter = [CIFilter filterWithName:@"CIHueAdjust" keysAndValues:kCIInputImageKey, beginImage, @"inputAngle", [NSNumber numberWithFloat:hue], nil];
    
    CIImage *outputImage = [hueFilter outputImage];
    
    CIFilter* saturationFilter = [CIFilter filterWithName:@"CIColorControls" keysAndValues:kCIInputImageKey, outputImage, @"inputSaturation", [NSNumber numberWithFloat:saturation], nil];
    
    outputImage = [saturationFilter outputImage];

    
    CGImageRef cgimg = [context createCGImage:outputImage fromRect:[outputImage extent]];
    
    
    UIImage *processed;
    if ( [[[UIDevice currentDevice] systemVersion] intValue] >= 4 && [[UIScreen mainScreen] scale] == 2.0 )
    {
        processed = [UIImage imageWithCGImage:cgimg scale:2.0 orientation:UIImageOrientationUp]; 
    }
    else
    {
        processed = [UIImage imageWithCGImage:cgimg]; 
    }
    
    CGImageRelease(cgimg);
    
    [processedImages setObject:processed forKey:imageName];

    return processed;
}

@end