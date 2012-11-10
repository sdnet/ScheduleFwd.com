//
//  ThemeListCell.h
//  mapper
//
//  Created by Tope on 25/01/2012.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface ThemeListCell : UITableViewCell

@property (nonatomic, strong) IBOutlet UILabel* titleLabel;

@property (nonatomic, strong) IBOutlet UILabel* authorLabel;

@property (nonatomic, strong) IBOutlet UILabel* updateLabel;

@property (nonatomic, strong) IBOutlet UIImageView* avatarImageView;

@property (nonatomic, strong) IBOutlet UIImageView* updateTypeImageview;

@property (nonatomic, assign) BOOL roundedEdgesDone;

-(void)makeRoundedEdges;


@end
