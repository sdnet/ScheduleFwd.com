//
//  PickerPopup.m
//  ScheduleForward
//
//  Created by Thomas Smallwood on 8/15/12.
//  Copyright (c) 2012 Forward Intel LLC. All rights reserved.
//

#import "PickerPopup.h"

@implementation PickerPopup

@synthesize target;
@synthesize confirmAction;
@synthesize delegate;

#pragma mark - Class Methods

+ (PickerPopup *)pickerPopup {
	CGSize s = [PickerPopup requiredSize];
	return [[[PickerPopup alloc] initWithFrame:CGRectMake(0.0, 0.0, s.width, s.height)] autorelease];
}

+ (PickerPopup *)pickerPopupWithPromptText:(NSString *)title
									delegate:(id<UIPickerViewDelegate>)delegate
								  datasource:(id<UIPickerViewDataSource>)datasource
							   confirmTarget:(id)target
							   confirmAction:(SEL)sel {
	CGSize s = [PickerPopup requiredSize];
	PickerPopup *p = [[[PickerPopup alloc] initWithFrame:CGRectMake(0.0, 0.0, s.width, s.height)] autorelease];
	[p setPromptText:title];
	[p setUIPickerViewDelegate:delegate];
	[p setUIPickerViewDataSource:datasource];
	p.target = target;
	p.confirmAction = sel;
	return p;
}

+ (CGSize)requiredSize {
	return CGSizeMake(320.0, 260.0);
}

#pragma mark -
#pragma mark Instance Methods

- (id)initWithFrame:(CGRect)frame {
    if ((self = [super initWithFrame:frame])) {
        // Initialization code
		toolbar = [[UIToolbar alloc] initWithFrame:CGRectMake(0.0, 0.0, 320.0, 44.0)];
		confirmButton = [[UIBarButtonItem alloc] initWithTitle:@"Select" style:UIBarButtonItemStyleBordered target:self action:@selector(confirmButtonPressed)];
		UIBarButtonItem *flexSpace = [[UIBarButtonItem alloc] initWithBarButtonSystemItem:UIBarButtonSystemItemFlexibleSpace target:nil action:nil];
		toolbar.items = [NSArray arrayWithObjects:flexSpace, confirmButton, nil];
		[flexSpace release];
		picker = [[UIPickerView alloc] initWithFrame:CGRectMake(0.0, 44.0, 320.0, 216.0)];
		picker.showsSelectionIndicator = YES;
		label = [[UILabel alloc] initWithFrame:CGRectMake(10.0, 7.0, 230.0, 30.0)];
		label.adjustsFontSizeToFitWidth = YES;
		label.backgroundColor = [UIColor clearColor];
		label.font = [UIFont systemFontOfSize:20.0];
		[self addSubview:toolbar];
		[self addSubview:label];
		[self addSubview:picker];
    }
    return self;
}

- (void)showInView:(UIView *)view {
	if (isShowing) {
		return;
	}
	if (self.delegate && [self.delegate respondsToSelector:@selector(pickerPopupWillShow)]) {
		[self.delegate pickerPopupWillShow];
	}
	isShowing = YES;
	CGSize s = [PickerPopup requiredSize];
	offScreenY = view.frame.size.height;
	[self setFrame:CGRectMake(0.0, offScreenY, s.width, s.height)];
	[view addSubview:self];
	[UIView beginAnimations:@"Show PickerPopup" context:nil];
	[UIView setAnimationBeginsFromCurrentState:YES];
	[UIView setAnimationDelegate:self];
	[UIView setAnimationDidStopSelector:@selector(pickerPopupDidShow)];
	[UIView setAnimationDuration:0.4];
	self.frame = CGRectMake(0, offScreenY - s.height, s.width, s.height);
	[UIView commitAnimations];
}

- (void)hide {
	if (isShowing) {
		if (self.delegate && [self.delegate respondsToSelector:@selector(pickerPopupWillHide)]) {
			[self.delegate pickerPopupWillHide];
		}
		isShowing = NO;
		CGSize s = [PickerPopup requiredSize];
		[UIView beginAnimations:@"Hide OCPickerPopup" context:nil];
		[UIView setAnimationBeginsFromCurrentState:YES];
		[UIView setAnimationDelegate:self];
		[UIView setAnimationDidStopSelector:@selector(pickerPopupDidHide)];
		[UIView setAnimationDuration:0.4];
		self.frame = CGRectMake(0, offScreenY, s.width, s.height);
		[UIView commitAnimations];
	}
}

- (void)pickerPopupDidShow {
	if (self.delegate && [self.delegate respondsToSelector:@selector(pickerPopupDidShow)]) {
		[self.delegate pickerPopupDidShow];
	}
}

- (void)pickerPopupDidHide {
	[self removeFromSuperview];
	if (self.delegate && [self.delegate respondsToSelector:@selector(pickerPopupDidHide)]) {
		[self.delegate pickerPopupDidHide];
	}
}

- (BOOL)isShowing {
	return isShowing;
}

- (void)setPromptText:(NSString *)title {
	label.text = title;
}

- (void)setConfirmButtonTitle:(NSString *)title {
	[confirmButton setTitle:title];
}

- (void)setUIPickerViewDelegate:(id<UIPickerViewDelegate>)uiPickerViewDelegate {
	picker.delegate = uiPickerViewDelegate;
}

- (void)setUIPickerViewDataSource:(id<UIPickerViewDataSource>)uiPickerViewDataSource {
	picker.dataSource = uiPickerViewDataSource;
}

- (void)setUIToolbarStyle:(UIBarStyle)barStyle {
	[toolbar setBarStyle:barStyle];
	switch (barStyle) {
		case UIBarStyleBlackOpaque:
			[self setPromptTextColor:[UIColor whiteColor]];
			break;
		case UIBarStyleBlackTranslucent:
			[self setPromptTextColor:[UIColor whiteColor]];
			break;
		case UIBarStyleDefault:
			[self setPromptTextColor:[UIColor blackColor]];
			break;
		default:
			break;
	}
}

- (void)setUIToolbarTintColor:(UIColor *)color {
	[toolbar setTintColor:color];
}

- (void)setPromptTextColor:(UIColor *)color {
	[label setTextColor:color];
}

- (NSInteger)selectedIndexForUIPickerViewComponent:(NSUInteger)component {
	return [picker selectedRowInComponent:component];
}

- (void)selectRow:(NSUInteger)row inUIPickerViewComponent:(NSUInteger)component animated:(BOOL)animated {
	[picker selectRow:row inComponent:component animated:animated];
}

- (void)reloadAllUIPickerViewComponents {
	[picker reloadAllComponents];
}

- (void)reloadUIPickerViewComponent:(NSUInteger)component {
	[picker reloadComponent:component];
}

- (void)setUIPickerViewShowsSelectionIndicator:(BOOL)shows {
	[picker setShowsSelectionIndicator:shows];
}

#pragma mark -
#pragma mark Selector Method

- (void)confirmButtonPressed {
	if (self.target && [self.target respondsToSelector:self.confirmAction]) {
		[self.target performSelector:self.confirmAction withObject:self];
	}
}

#pragma mark -
#pragma mark Memory Management

- (void)dealloc {
	[picker release];
	[toolbar release];
	[confirmButton release];
	[label release];
	[target release];
    [super dealloc];
}


@end
