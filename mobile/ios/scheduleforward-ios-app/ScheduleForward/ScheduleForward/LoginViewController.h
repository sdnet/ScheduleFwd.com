//
//  LoginViewController.h
//  MedSched
//
//  Created by Thomas Smallwood on 8/15/12.
//  Copyright (c) 2012 Forward Intel LLC. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "MBProgressHUD/MBProgressHUD.h"
#import "PickerPopup.h"


@interface LoginViewController : UIViewController<PickerPopupDelegate, UIPickerViewDelegate, UIPickerViewDataSource, UITextFieldDelegate>

@property (nonatomic, strong) MBProgressHUD *hud;
@property (nonatomic) CGPoint scrollViewOffSet;
@property (strong, nonatomic) IBOutlet UIScrollView *scrollView;
@property (strong, nonatomic) IBOutlet UITextField *userNameTF;
@property (strong, nonatomic) IBOutlet UITextField *passwordTF;
@property (strong, nonatomic) IBOutlet UIButton *selectHospitalButton;
@property (strong, nonatomic) IBOutlet UIButton *loginButton;

@property (nonatomic, strong) NSArray *groupCodeArray;
@property (nonatomic, strong) NSDictionary *groupCodeDict;
@property (nonatomic, strong) NSString *groupCode;
@property (nonatomic, strong) PickerPopup *hospitalPicker;


- (IBAction)selectHospitalPressed:(id)sender;
- (IBAction)loginPressed:(id)sender;
- (void)toggleItems;

@end
