//
//  demoViewController.m
//  demoApp
//
//  Created by Maethee Chongchitnant on 5/20/56 BE.
//  Copyright (c) 2556 Maethee Chongchitnant. All rights reserved.
//

#import "demoViewController.h"

@interface demoViewController ()

@end

@implementation demoViewController

- (void)viewDidLoad
{
    [super viewDidLoad];
	// Do any additional setup after loading the view, typically from a nib.
    
    pb = [[Playbasis alloc] init];
    [pb auth:@"abc" :@"abcde" :self];
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (void)processResponse:(NSDictionary *)jsonResponse withURL:(NSURL *)url
{
    NSLog(@"delegate triggered from URL: %@", [url path]);
    NSLog(@"%@", jsonResponse);
    if(!authed && [[url path] isEqualToString:@"/Auth"])
    {
        authed = YES;
        NSLog(@"authed");
        
        NSString *user = @"1";
        [pb player:user :self];
        //[pb registerUser:@"user123" :self :@"username123" :@"username@email.com" :@"http://imageurl.html", @"first_name=fname", @"last_name=lname", nil];
        [pb login:user :self];
        [pb logout:user :self];
        [pb points:user :self];
        [pb point:user :@"exp" :self];
        [pb actionLastPerformed:user :self];
        [pb actionLastPerformedTime:user :@"like" :self];
        [pb actionPerformedCount:user :@"like" :self];
        [pb badgeOwned:user :self];
        [pb rank:@"exp" :10 :self];
        [pb badges:self];
        [pb badge:@"1" :self];
        [pb badgeCollections:self];
        [pb badgeCollection:@"1" :self];
        [pb actionConfig:self];
        [pb rule:user :@"like" :self, @"url=http://mysite.com/page", nil];
    }
}
@end
