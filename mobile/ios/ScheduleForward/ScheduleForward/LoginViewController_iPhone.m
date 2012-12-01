//
//  LoginViewController_iPhone.m
//  ScheduleForward
//
//  Created by Thomas Smallwood on 11/18/12.
//  Copyright (c) 2012 Thomas Smallwood. All rights reserved.
//

#import "LoginViewController_iPhone.h"
#import "SelectHospitalViewController_iPhone.h"


@interface LoginViewController_iPhone ()

@end

@implementation LoginViewController_iPhone

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
    }
    return self;
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
}

- (void)viewWillAppear:(BOOL)animated {
    [super viewWillAppear:animated];
    
    [self.navigationController setNavigationBarHidden:YES];
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (IBAction)loginPressed:(id)sender {
    SelectHospitalViewController_iPhone *selectHospitalVC = [[SelectHospitalViewController_iPhone alloc] initWithNibName:@"SelectHospitalViewController_iPhone" bundle:nil];
    
    [self.navigationController pushViewController:selectHospitalVC animated:YES];
}

@end
