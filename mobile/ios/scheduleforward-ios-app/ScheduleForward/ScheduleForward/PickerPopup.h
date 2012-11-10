//
//  PickerPopup.h
//  ScheduleForward
//
//  Created by Thomas Smallwood on 8/15/12.
//  Copyright (c) 2012 Forward Intel LLC. All rights reserved.
//

#import <UIKit/UIKit.h>

@protocol PickerPopupDelegate <NSObject>

@optional
- (void)pickerPopupWillShow;
- (void)pickerPopupDidShow;
- (void)pickerPopupWillHide;
- (void)pickerPopupDidHide;

@end


@interface PickerPopup : UIView {
	id target;
	SEL confirmAction;
	id<PickerPopupDelegate> delegate;
	
@private
	UIPickerView *picker;
	UIToolbar *toolbar;
	UIBarButtonItem *confirmButton;
	UILabel *label;
	BOOL isShowing;
	CGFloat offScreenY;
}

@property (nonatomic, retain) id target;
@property (nonatomic) SEL confirmAction;
@property (nonatomic, assign) id<PickerPopupDelegate> delegate;


+ (PickerPopup *)pickerPopup;
+ (PickerPopup *)pickerPopupWithPromptText:(NSString *)title
                                  delegate:(id<UIPickerViewDelegate>)delegate
                                datasource:(id<UIPickerViewDataSource>)datasource
                             confirmTarget:(id)target
                             confirmAction:(SEL)sel;
+ (CGSize)requiredSize;

- (void)showInView:(UIView *)view;
- (void)hide;
- (BOOL)isShowing;

// configure popup
- (void)setPromptText:(NSString *)title;
- (void)setConfirmButtonTitle:(NSString *)title;
- (void)setUIPickerViewDelegate:(id<UIPickerViewDelegate>)uiPickerViewDelegate;
- (void)setUIPickerViewDataSource:(id<UIPickerViewDataSource>)uiPickerViewDataSource;

// facade toolbar methods
- (void)setUIToolbarStyle:(UIBarStyle)barStyle;
- (void)setUIToolbarTintColor:(UIColor *)color;

// facade label methods
- (void)setPromptTextColor:(UIColor *)color;

// facade picker methods
- (NSInteger)selectedIndexForUIPickerViewComponent:(NSUInteger)component;
- (void)selectRow:(NSUInteger)row inUIPickerViewComponent:(NSUInteger)component animated:(BOOL)animated;
- (void)reloadAllUIPickerViewComponents;
- (void)reloadUIPickerViewComponent:(NSUInteger)component;
- (void)setUIPickerViewShowsSelectionIndicator:(BOOL)shows;

@end
