//
//  LoginViewController.m
//  MedSched
//
//  Created by Thomas Smallwood on 8/15/12.
//  Copyright (c) 2012 Forward Intel LLC. All rights reserved.
//

#import "LoginViewController.h"
#import "APIManager.h"
#import "Keychain Wrapper/SFHFKeychainUtils.h"

@interface LoginViewController ()

@end

@implementation LoginViewController
@synthesize scrollView;
@synthesize userNameTF;
@synthesize passwordTF;
@synthesize selectHospitalButton;
@synthesize scrollViewOffSet;
@synthesize hud = _hud;
@synthesize loginButton;
@synthesize groupCodeArray;
@synthesize groupCodeDict;
@synthesize groupCode = _groupCode;
@synthesize hospitalPicker;

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
        self.groupCodeDict = [[APIManager sharedApiManager] fetchGroupCodes];
        if (self.groupCodeDict != nil) {
            self.groupCodeArray = [self.groupCodeDict objectForKey:@"data"];
        }
    }
    return self;
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
    self.hud = [[MBProgressHUD alloc] initWithView:self.view];
    
    NSString *username = [[NSUserDefaults standardUserDefaults] objectForKey:@"username"];
    
    NSString *groupCode = [[NSUserDefaults standardUserDefaults] objectForKey:@"groupcode"];
    
    if (username != nil) {
        [self toggleItems];
        
        NSError *error = nil;
        NSString *password = [SFHFKeychainUtils getPasswordForUsername:username andServiceName:@"ScheduleForward" error:&error];
        
        NSLog(@"username: %@", username);
        NSLog(@"pass: %@", password);
        NSLog(@"group code: %@", self.groupCode);
        if (password != nil) {
            
            
            
            self.hud = [MBProgressHUD showHUDAddedTo:self.view animated:YES];
            _hud.labelText = @"Logging in...";
            
            [self dismissKeyboards];
            
            scrollViewOffSet = scrollView.contentOffset;
            CGPoint point;
            CGRect svRect = [self.scrollView bounds];
            svRect = [self.scrollView convertRect:svRect toView:scrollView];
            point = svRect.origin;
            point.x = 0;
            point.y = 0;
            
            [scrollView setContentOffset:point animated:YES];
            
            
            BOOL success = [[APIManager sharedApiManager] loginWithUsername:username password:password groupCode:groupCode];
            
            if (success) {
                
                [self performSelector:@selector(dismissHUDWithLogInSuccess:) withObject:nil afterDelay:2.0];
            }
            else {
                NSLog(@"failed");
                [self performSelector:@selector(dismissHUDWithLogInFailure:) withObject:nil afterDelay:2.0];
            }
        }
    }
    else {
        [self.selectHospitalButton setHidden:NO];
    }
}

- (void)toggleItems {
    if ([self.userNameTF isHidden]) {
        [self.loginButton setHidden:NO];
        [self.selectHospitalButton setHidden:NO];
        [self.userNameTF setHidden:NO];
        [self.passwordTF setHidden:NO];
    }
    else {
        [self.loginButton setHidden:YES];
        [self.selectHospitalButton setHidden:YES];
        [self.userNameTF setHidden:YES];
        [self.passwordTF setHidden:YES];
    }
}

- (void)viewDidUnload
{
    [super viewDidUnload];
    [self setUserNameTF:nil];
    [self setScrollView:nil];
    [self setPasswordTF:nil];
    [self setSelectHospitalButton:nil];
    [self setLoginButton:nil];
    [self setGroupCodeArray:nil];
    [self setGroupCodeDict:nil];
    [self setGroupCode:nil];
    [self setHospitalPicker:nil];
}

- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation
{
    return (interfaceOrientation == UIInterfaceOrientationPortrait);
}

- (IBAction)selectHospitalPressed:(id)sender {
    [self dismissKeyboards];
    scrollViewOffSet = scrollView.contentOffset;
    CGPoint point;
    CGRect svRect = [self.scrollView bounds];
    svRect = [self.scrollView convertRect:svRect toView:scrollView];
    point = svRect.origin;
    point.x = 0;
    point.y = 0;
    
    [scrollView setContentOffset:point animated:YES];
    
    hospitalPicker = [PickerPopup pickerPopupWithPromptText:@"Select Hospital" delegate:self datasource:self confirmTarget:self confirmAction:@selector(confirmPressed:)];
	[hospitalPicker setDelegate:self];
	[hospitalPicker setUIToolbarStyle:UIBarStyleBlackOpaque];
	[hospitalPicker selectRow:0 inUIPickerViewComponent:0 animated:YES];
	[hospitalPicker showInView:self.view];
}

- (IBAction)loginPressed:(id)sender {
    
    if ([userNameTF.text length] > 0 && [passwordTF.text length] > 0 && self.groupCode != nil) {
        self.hud = [MBProgressHUD showHUDAddedTo:self.view animated:YES];
        _hud.labelText = @"Logging in...";
        
        [self dismissKeyboards];
        
        scrollViewOffSet = scrollView.contentOffset;
        CGPoint point;
        CGRect svRect = [self.scrollView bounds];
        svRect = [self.scrollView convertRect:svRect toView:scrollView];
        point = svRect.origin;
        point.x = 0;
        point.y = 0;
        
        [scrollView setContentOffset:point animated:YES];
        
        
        
        BOOL success = [[APIManager sharedApiManager] loginWithUsername:userNameTF.text password:passwordTF.text groupCode:self.groupCode];
        
        if (success) {
            
            [self performSelector:@selector(dismissHUDWithLogInSuccess:) withObject:nil afterDelay:2.0];
        }
        else {
            NSLog(@"failed");
            [self performSelector:@selector(dismissHUDWithLogInFailure:) withObject:nil afterDelay:2.0];
        }
        
    }
    else {
        UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Login Problem" message:@"Please fill in all fields." delegate:self cancelButtonTitle:@"OK" otherButtonTitles:nil];
        [alert show];
        
    }
}

- (void)dismissKeyboards {
    [self.userNameTF resignFirstResponder];
    [self.passwordTF resignFirstResponder];
}

- (void)dismissHUDWithLogInSuccess:(id)arg {
    
    [MBProgressHUD hideHUDForView:self.view animated:YES];
    //self.hud = nil;
    
    [self dismissViewControllerAnimated:YES completion:nil];
}

- (void)dismissHUDWithLogInFailure:(id)arg {
    [MBProgressHUD hideHUDForView:self.view animated:YES];
    //self.hud = nil;
    
    if ([self.userNameTF isHidden]) {
        [self toggleItems];
    }
    
    UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Account Problem" message:@"Please double-check your login information and try again." delegate:self cancelButtonTitle:@"OK" otherButtonTitles:nil];
    [alert show];
    
    [self.userNameTF setText:@""];
    [self.passwordTF setText:@""];
}


#pragma mark - UITextField Delegate Methods

- (void)textFieldDidBeginEditing:(UITextField *)textField {
    scrollViewOffSet = scrollView.contentOffset;
    CGPoint point;
    CGRect tfRect = [textField bounds];
    tfRect = [textField convertRect:tfRect toView:scrollView];
    point = tfRect.origin;
    point.x = 0;
    
    if (textField == self.userNameTF) {
        point.y -= 80;
    }
    else if (textField == self.passwordTF) {
        point.y -= 130;
    }
    
    [scrollView setContentOffset:point animated:YES];
}

- (BOOL)textFieldShouldReturn:(UITextField *)textField {
    [scrollView setContentOffset:scrollViewOffSet animated:YES];
    [textField resignFirstResponder];
    return YES;
}

#pragma mark -
#pragma mark UIPickerViewDelegate Methods

- (void)confirmPressed:(id)sender {
    [sender hide];
}

- (NSString *)pickerView:(UIPickerView *)pickerView titleForRow:(NSInteger)row forComponent:(NSInteger)component {
    if (row == 0) {
        return @"Select Hospital";
    }
    else {
        NSDictionary *hospitalDict = [self.groupCodeArray objectAtIndex:row-1];
        
        NSString *hospital = [hospitalDict objectForKey:@"name"];
        
        [self.selectHospitalButton setTitle:hospital forState:UIControlStateNormal];
        return hospital;
    }
}

- (void)pickerView:(UIPickerView *)pickerView didSelectRow:(NSInteger)row inComponent:(NSInteger)component {
    
    if (row == 0) {
        NSString *hospital = @"Select Hospital";
        [self.selectHospitalButton setTitle:hospital forState:UIControlStateNormal];
        self.groupCode = nil;
    }
    else {
        NSDictionary *hospitalDict = [self.groupCodeArray objectAtIndex:row-1];
        
        NSString *hospital = [hospitalDict objectForKey:@"name"];
        
        [self.selectHospitalButton setTitle:hospital forState:UIControlStateNormal];
        
        self.groupCode = [hospitalDict objectForKey:@"groupcode"];
    }
}

#pragma mark - UIPickerViewDatasource Methods

- (NSInteger)numberOfComponentsInPickerView:(UIPickerView *)pickerView {
	return 1;
}

- (NSInteger)pickerView:(UIPickerView *)pickerView numberOfRowsInComponent:(NSInteger)component {
	return [self.groupCodeArray count] + 1;
}


#pragma mark - PickerPopupDelegate Methods

- (void)pickerPopupWillShow {
	[self.userNameTF setEnabled:NO];
	[self.passwordTF setEnabled:NO];
	[self.loginButton setEnabled:NO];
}

- (void)pickerPopupWillHide {
	[self.userNameTF setEnabled:YES];
	[self.passwordTF setEnabled:YES];
	[self.loginButton setEnabled:YES];
}



@end
