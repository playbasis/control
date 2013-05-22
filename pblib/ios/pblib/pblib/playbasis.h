//
//  playbasis.h
//  playbasis
//
//  Created by Maethee Chongchitnant on 5/14/56 BE.
//  Copyright (c) 2556 Maethee Chongchitnant. All rights reserved.
//

#import <Foundation/Foundation.h>

typedef enum
{
    Started,
    ResponseReceived,
    ReceivingData,
    FinishedWithError,
    Finished
}
PBRequestState;

@protocol PBResponseHandler <NSObject>
-(void)processResponse:(NSDictionary*)jsonResponse withURL:(NSURL *)url;
@end

@interface PBRequest : NSObject
{
    NSURL* url;
    NSMutableData *receivedData;
    NSDictionary *jsonResponse;
    PBRequestState state;
    id<PBResponseHandler> responseDelegate;
}
-(id)initWithURLRequest:(NSURLRequest *)request;
-(id)initWithURLRequest:(NSURLRequest *)request andDelegate:(id<PBResponseHandler>)delegate;
-(void)dealloc;
-(PBRequestState)getRequestState;
-(NSDictionary *)getResponse;

-(void)connection:(NSURLConnection *)connection didReceiveResponse:(NSURLResponse *)response;
-(void)connection:(NSURLConnection *)connection didReceiveData:(NSData *)data;
-(void)connection:(NSURLConnection *)connection didFailWithError:(NSError *)error;
-(void)connectionDidFinishLoading:(NSURLConnection *)connection;
@end

@interface Playbasis : NSObject
{
    NSString *token;
    NSString *apiKeyParam;
    id<PBResponseHandler> authDelegate;
}
-(id)init;
-(void)dealloc;

-(PBRequest *)auth:(NSString *)apiKey :(NSString *)apiSecret :(id<PBResponseHandler>)delegate;
-(PBRequest *)player:(NSString *)playerId :(id<PBResponseHandler>)delegate;
-(PBRequest *)registerUser:(NSString *)playerId :(id<PBResponseHandler>)delegate :(NSString *)username :(NSString *)email :(NSString *)imageUrl, ...;
-(PBRequest *)login:(NSString *)playerId :(id<PBResponseHandler>)delegate;
-(PBRequest *)logout:(NSString *)playerId :(id<PBResponseHandler>)delegate;
-(PBRequest *)points:(NSString *)playerId :(id<PBResponseHandler>)delegate;
-(PBRequest *)point:(NSString *)playerId :(NSString *)pointName :(id<PBResponseHandler>)delegate;
-(PBRequest *)actionLastPerformed:(NSString *)playerId :(id<PBResponseHandler>)delegate;
-(PBRequest *)actionLastPerformedTime:(NSString *)playerId :(NSString *)actionName :(id<PBResponseHandler>)delegate;
-(PBRequest *)actionPerformedCount:(NSString *)playerId :(NSString *)actionName :(id<PBResponseHandler>)delegate;
-(PBRequest *)badgeOwned:(NSString *)playerId :(id<PBResponseHandler>)delegate;
-(PBRequest *)rank:(NSString *)rankedBy :(unsigned int)limit :(id<PBResponseHandler>)delegate;
-(PBRequest *)badges :(id<PBResponseHandler>)delegate;
-(PBRequest *)badge:(NSString *)badgeId :(id<PBResponseHandler>)delegate;
-(PBRequest *)badgeCollections :(id<PBResponseHandler>)delegate;
-(PBRequest *)badgeCollection:(NSString *)collectionId :(id<PBResponseHandler>)delegate;
-(PBRequest *)actionConfig :(id<PBResponseHandler>)delegate;
-(PBRequest *)rule:(NSString *)playerId :(NSString *)action :(id<PBResponseHandler>)delegate, ...;
-(PBRequest *)call:(NSString *)method withData:(NSString *)data andDelegate:(id<PBResponseHandler>)delegate;
@end
