//
//  demoViewController.h
//  demoApp
//
//  Created by Maethee Chongchitnant on 5/20/56 BE.
//  Copyright (c) 2556 Maethee Chongchitnant. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "playbasis.h"

@interface demoViewController : UIViewController <PBResponseHandler>
{
    Playbasis *pb;
    BOOL authed;
}
-(void)processResponse:(NSDictionary *)jsonResponse withURL:(NSURL *)url;
@end
