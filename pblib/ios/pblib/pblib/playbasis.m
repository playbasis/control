//
//  playbasis.m
//  playbasis
//
//  Created by Maethee Chongchitnant on 5/14/56 BE.
//  Copyright (c) 2556 Maethee Chongchitnant. All rights reserved.
//

#import "playbasis.h"
#import "JSONKit.h"

static NSString * const BASE_URL = @"https://api.pbapp.net/";

//
// object for handling requests response
//
@implementation PBRequest

-(id)initWithURLRequest:(NSURLRequest *)request
{
    return [self initWithURLRequest:request andDelegate:nil];
}

-(id)initWithURLRequest:(NSURLRequest *)request andDelegate:(id<PBResponseHandler>)delegate
{
    if(!(self = [super init]))
        return nil;
    
    NSURLConnection *connection = [[NSURLConnection alloc] initWithRequest:request delegate:self];
    if(!connection)
        return nil;
    
    url = [request URL];
    [url retain];
#if __has_feature(objc_arc)
    receivedData = [NSMutableData data];
#else
    receivedData = [[NSMutableData data] retain];
#endif
    responseDelegate = delegate;
    state = Started;
    return self;
}

-(void)dealloc
{
    [url release];
    [super dealloc];
}

-(PBRequestState)getRequestState
{
    return state;
}

-(NSDictionary *)getResponse
{
    return jsonResponse;
}

-(void)connection:(NSURLConnection *)connection didReceiveResponse:(NSURLResponse *)response
{
    [receivedData setLength:0];
    state = ResponseReceived;
}

-(void)connection:(NSURLConnection *)connection didReceiveData:(NSData *)data
{
    [receivedData appendData:data];
    state = ReceivingData;
}

-(void)connection:(NSURLConnection *)connection didFailWithError:(NSError *)error
{
#if __has_feature(objc_arc)
    //do nothing
#else
    [connection release];
    [receivedData release];
#endif
    //error inform user of error
    state = FinishedWithError;
    NSLog(@"request from %@ failed: %ld - %@ - %@", [url absoluteString], (long)[error code], [error domain], [error helpAnchor]);
}

-(void)connectionDidFinishLoading:(NSURLConnection *)connection
{
    //process data received
    NSString *response = [[NSString alloc] initWithData:receivedData encoding:NSUTF8StringEncoding];
    jsonResponse = [response objectFromJSONString];
    if(responseDelegate && ([responseDelegate respondsToSelector:@selector(processResponse:withURL:)]))
        [responseDelegate processResponse:jsonResponse withURL:url];

#if __has_feature(objc_arc)
    //do nothing
#else
    [connection release];
    [receivedData release];
#endif
    state = Finished;
    NSLog(@"request from %@ finished", [url absoluteString]);
}
@end

//
// additional interface for private methods
//
@interface Playbasis ()
-(void)setToken:(NSString *)newToken;
@end

//
// delegate object for handling authentication
//
@interface PBAuthDelegate : NSObject <PBResponseHandler>
{
    Playbasis* pb;
    BOOL finished;
    id<PBResponseHandler> finishDelegate;
}
-(id)initWithPlaybasis:(Playbasis*)playbasis andDelegate:(id<PBResponseHandler>)delegate;
-(BOOL)isFinished;
-(void)processResponse:(NSDictionary *)jsonResponse withURL:(NSURL *) url;
@end

@implementation PBAuthDelegate

-(id)initWithPlaybasis:(Playbasis *)playbasis andDelegate:(id<PBResponseHandler>)delegate
{
    if(!(self = [super init]))
        return nil;
    finished = NO;
    pb = playbasis;
    finishDelegate = delegate;
    return self;
}
-(BOOL)isFinished
{
    return finished;
}
-(void)processResponse:(NSDictionary *)jsonResponse withURL:(NSURL *)url
{
    id success = [jsonResponse objectForKey:@"success"];
    if(!success)
    {
        //auth failed
        finished = YES;
        if(finishDelegate && ([finishDelegate respondsToSelector:@selector(processResponse:withURL:)]))
            [finishDelegate processResponse:jsonResponse withURL:url];
        return;
    }
    id response = [jsonResponse objectForKey:@"response"];
    id token = [response objectForKey:@"token"];
    [pb setToken:token];
    finished = YES;
    if(finishDelegate && ([finishDelegate respondsToSelector:@selector(processResponse:withURL:)]))
        [finishDelegate processResponse:jsonResponse withURL:url];
}
@end

//
// The Playbasis Object
//
@implementation Playbasis

-(id)init
{
    if(!(self = [super init]))
        return nil;
    token = nil;
    apiKeyParam = nil;
    authDelegate = nil;
    return self;
}

-(void)dealloc
{
    if(token)
        [token release];
    if(authDelegate)
        [authDelegate release];
    [super dealloc];
}

-(PBRequest *)auth:(NSString *)apiKey :(NSString *)apiSecret :(id<PBResponseHandler>)delegate
{
    apiKeyParam = [[NSString alloc] initWithFormat:@"?api_key=%@", apiKey];
    authDelegate = [[PBAuthDelegate alloc] initWithPlaybasis:self andDelegate:delegate];
    NSString *data = [NSString stringWithFormat:@"api_key=%@&api_secret=%@", apiKey, apiSecret];
    return [self call:@"Auth" withData:data andDelegate:authDelegate];
}

-(PBRequest *)player:(NSString *)playerId :(id<PBResponseHandler>)delegate
{
    NSAssert(token, @"access token is nil");
    NSString *method = [NSString stringWithFormat:@"Player/%@", playerId];
    NSString *data = [NSString stringWithFormat:@"token=%@", token];
    return [self call:method withData:data andDelegate:delegate];
}

//
// @param	...[vararg]     Varargs of String for additional parameters to be sent to the register method.
// 							Each element is a string in the format of key=value, for example: first_name=john
// 							The following keys are supported:
// 							- facebook_id
// 							- twitter_id
// 							- password		assumed hashed
//							- first_name
// 							- last_name
// 							- nickname
// 							- gender		1=Male, 2=Female
// 							- birth_date	format YYYY-MM-DD
//
-(PBRequest *)registerUser:(NSString *)playerId :(id<PBResponseHandler>)delegate :(NSString *)username :(NSString *)email :(NSString *)imageUrl, ...
{
    NSAssert(token, @"access token is nil");
    NSString *method = [NSString stringWithFormat:@"Player/%@/register", playerId];
    NSMutableString *data = [NSMutableString stringWithFormat:@"token=%@&username=%@&email=%@&image=%@", token, username, email, imageUrl];
    
    id optionalData;
    va_list argumentList;
    va_start(argumentList, imageUrl);
    while ((optionalData = va_arg(argumentList, NSString *)))
    {
        [data appendFormat:@"&%@", optionalData];
    }
    va_end(argumentList);
    
    return [self call:method withData:data andDelegate:delegate];
}

-(PBRequest *)login:(NSString *)playerId :(id<PBResponseHandler>)delegate;
{
    NSAssert(token, @"access token is nil");
    NSString *method = [NSString stringWithFormat:@"Player/%@/login", playerId];
    NSString *data = [NSString stringWithFormat:@"token=%@", token];
    return [self call:method withData:data andDelegate:delegate];
}

-(PBRequest *)logout:(NSString *)playerId :(id<PBResponseHandler>)delegate;
{
    NSAssert(token, @"access token is nil");
    NSString *method = [NSString stringWithFormat:@"Player/%@/logout", playerId];
    NSString *data = [NSString stringWithFormat:@"token=%@", token];
    return [self call:method withData:data andDelegate:delegate];
}

-(PBRequest *)points:(NSString *)playerId :(id<PBResponseHandler>)delegate
{
    NSString *method = [NSString stringWithFormat:@"Player/%@/points%@", playerId, apiKeyParam];
    return [self call:method withData:nil andDelegate:delegate];
}

-(PBRequest *)point:(NSString *)playerId :(NSString *)pointName :(id<PBResponseHandler>)delegate
{
    NSString *method = [NSString stringWithFormat:@"Player/%@/point/%@%@", playerId, pointName, apiKeyParam];
    return [self call:method withData:nil andDelegate:delegate];
}

-(PBRequest *)actionLastPerformed:(NSString *)playerId :(id<PBResponseHandler>)delegate
{
    NSString *method = [NSString stringWithFormat:@"Player/%@/action/time%@", playerId, apiKeyParam];
    return [self call:method withData:nil andDelegate:delegate];
}

-(PBRequest *)actionLastPerformedTime:(NSString *)playerId :(NSString *)actionName :(id<PBResponseHandler>)delegate
{
    NSString *method = [NSString stringWithFormat:@"Player/%@/action/%@/time%@", playerId, actionName, apiKeyParam];
    return [self call:method withData:nil andDelegate:delegate];
}

-(PBRequest *)actionPerformedCount:(NSString *)playerId :(NSString *)actionName :(id<PBResponseHandler>)delegate
{
    NSString *method = [NSString stringWithFormat:@"Player/%@/action/%@/count%@", playerId, actionName, apiKeyParam];
    return [self call:method withData:nil andDelegate:delegate];
}

-(PBRequest *)badgeOwned:(NSString *)playerId :(id<PBResponseHandler>)delegate
{
    NSString *method = [NSString stringWithFormat:@"Player/%@/badge%@", playerId, apiKeyParam];
    return [self call:method withData:nil andDelegate:delegate];
}

-(PBRequest *)rank:(NSString *)rankedBy :(unsigned int)limit :(id<PBResponseHandler>)delegate
{
    NSString *method = [NSString stringWithFormat:@"Player/rank/%@/%u%@", rankedBy, limit, apiKeyParam];
    return [self call:method withData:nil andDelegate:delegate];
}

-(PBRequest *)badges :(id<PBResponseHandler>)delegate
{
    NSString *method = [NSString stringWithFormat:@"Badge%@", apiKeyParam];
    return [self call:method withData:nil andDelegate:delegate];
}

-(PBRequest *)badge:(NSString *)badgeId :(id<PBResponseHandler>)delegate
{
    NSString *method = [NSString stringWithFormat:@"Badge/%@%@", badgeId, apiKeyParam];
    return [self call:method withData:nil andDelegate:delegate];
}

-(PBRequest *)badgeCollections :(id<PBResponseHandler>)delegate
{
    NSString *method = [NSString stringWithFormat:@"Badge/collection%@", apiKeyParam];
    return [self call:method withData:nil andDelegate:delegate];
}

-(PBRequest *)badgeCollection:(NSString *)collectionId :(id<PBResponseHandler>)delegate
{
    NSString *method = [NSString stringWithFormat:@"Badge/collection/%@%@", collectionId, apiKeyParam];
    return [self call:method withData:nil andDelegate:delegate];
}

-(PBRequest *)actionConfig :(id<PBResponseHandler>)delegate
{
    NSString *method = [NSString stringWithFormat:@"Engine/actionConfig%@", apiKeyParam];
    return [self call:method withData:nil andDelegate:delegate];
}

//
// @param	...[vararg]     Varargs of String for additional parameters to be sent to the rule method.
// 							Each element is a string in the format of key=value, for example: url=playbasis.com
// 							The following keys are supported:
// 							- url		url or filter string (for triggering non-global actions)
// 							- reward	name of the custom-point reward to give (for triggering rules with custom-point reward)
// 							- quantity	amount of points to give (for triggering rules with custom-point reward)
//
-(PBRequest *)rule:(NSString *)playerId :(NSString *)action :(id<PBResponseHandler>)delegate, ...
{
    NSAssert(token, @"access token is nil");
    NSMutableString *data = [NSMutableString stringWithFormat:@"token=%@&player_id=%@&action=%@", token, playerId, action];
    
    id optionalData;
    va_list argumentList;
    va_start(argumentList, delegate);
    while ((optionalData = va_arg(argumentList, NSString *)))
    {
        [data appendFormat:@"&%@", optionalData];
    }
    va_end(argumentList);
    
    return [self call:@"Engine/rule" withData:data andDelegate:delegate];
}

-(PBRequest *)call:(NSString *)method withData:(NSString *)data andDelegate:(id<PBResponseHandler>)delegate
{
    id request = nil;
    id url = [NSURL URLWithString:[BASE_URL stringByAppendingString:method]];
    if(!data)
    {
        request = [NSURLRequest requestWithURL:url];
    }
    else
    {
        NSData *postData = [data dataUsingEncoding:NSUTF8StringEncoding];
        NSString *postLength = [NSString stringWithFormat:@"%d", [postData length]];
        request = [NSMutableURLRequest requestWithURL:url];
        [request setHTTPMethod:@"POST"];
        [request setValue:@"application/x-www-form-urlencoded charset=utf-8" forHTTPHeaderField:@"Content-Type"];
        [request setValue:postLength forHTTPHeaderField:@"Content-Length"];
        [request setHTTPBody:postData];
    }
    id pbRequest = [[PBRequest alloc] initWithURLRequest:request andDelegate:delegate];
    return [pbRequest autorelease];
}

-(void)setToken:(NSString *)newToken
{
    if(token)
        [token release];
    token = newToken;
    [token retain];
    NSLog(@"token assigned: %@", token);
}

@end
