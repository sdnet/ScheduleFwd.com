//
//  ThemeListCell.m
//  mapper
//
//  Created by Tope on 25/01/2012.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "ThemeListCell.h"
#import <QuartzCore/QuartzCore.h>

@implementation ThemeListCell

@synthesize titleLabel, authorLabel, avatarImageView, updateLabel, updateTypeImageview, roundedEdgesDone;

- (id)initWithStyle:(UITableViewCellStyle)style reuseIdentifier:(NSString *)reuseIdentifier
{
    self = [super initWithStyle:style reuseIdentifier:reuseIdentifier];
    if (self) {
        // Initialization code
    }
    return self;
}

- (void)setSelected:(BOOL)selected animated:(BOOL)animated
{
    [super setSelected:selected animated:animated];

    // Configure the view for the selected state
}


-(void)makeRoundedEdges
{
    if(!roundedEdgesDone)
    {
        avatarImageView.layer.cornerRadius = 5.0;
        avatarImageView.layer.masksToBounds = YES;
        
        avatarImageView.frame = CGRectMake(avatarImageView.frame.origin.x , avatarImageView.frame.origin.y, 54, 54); 
        
        roundedEdgesDone = YES;
    }
}

@end
