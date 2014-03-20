package com.playbasis.pblib;

import java.util.Map;
import java.util.List;

import static org.junit.Assert.*;
import static org.hamcrest.CoreMatchers.*;
import org.junit.AfterClass;
import org.junit.BeforeClass;
import org.junit.Test;

/**
 * The PlaybasisTest Object
 * @author thanakij
 */
public class PlaybasisTest {

    public static final String authKey = "abc";
    public static final String authSecret = "abcde";
    public static final String cl_player_id1 = "1";
    public static final String cl_player_id1_exp = "123";
    public static final String cl_player_id1_level = "11";
    public static final String cl_player_id2 = "test_Java";
    public static final String cl_player_id2_exp = "234";
    public static final String cl_player_id2_level = "22";
    public static final String action1 = "like";
    public static final String action1_url = "demo_like";
    public static final int limit = 5;

    private static final Playbasis pb = new Playbasis();

    @BeforeClass
    public static void testSetup() {
        System.out.println("testSetup");
        boolean result = pb.auth(authKey, authSecret);
        System.out.println("url = " + Playbasis.BASE_URL);
        System.out.println("auth = " + result);
        System.out.println("token = " + pb.getToken());
    }

    @AfterClass
    public static void testCleanup() {
        System.out.println("testCleanup");
    }

    @Test
    public void testAuth() {
        boolean result;
        System.out.println("testAuth");

        result = pb.auth(authKey, authSecret + "X"); // POST /Auth - Input wrong 'api_secret'
        assertFalse(result);

        result = pb.auth(authKey, authSecret); // POST /Auth - Input correctly
        String token = pb.getToken();
        assertTrue(result);

        String token_before = token;
        result = pb.auth(authKey, authSecret); // POST /Auth - Input correctly again
        assertTrue(result);
        token = pb.getToken();
        assertEquals(token, token_before);

        result = pb.renew(authKey, authSecret + "X"); // POST /Auth/renew - Input wrong 'api_secret'
        assertFalse(result);

        result = pb.renew(authKey, authSecret); // POST /Auth/renew - Input correctly
        assertTrue(result);
        token = pb.getToken();
        assertThat(token, not(token_before)); // assertNotEquals(token_before, token);, http://stackoverflow.com/questions/1096650/why-doesnt-junit-provide-assertnotequals-methods

        token_before = token;
        result = pb.auth(authKey, authSecret); // POST /Auth - Auth after Renew
        assertTrue(result);
        token = pb.getToken();
        assertEquals(token, token_before);
    }

    @Test
    public void testLevel() {
        Request request;
        System.out.println("testLevel");

        request = pb.level(1); // GET /level/1 - Info about level=1
        assertTrue(request.isSuccess());

        request = pb.levels(); // GET /levels - List info of all levels
        assertTrue(request.isSuccess());
    }

    @Test
    public void testRank() {
        Request request;
        System.out.println("testRank");

        request = pb.rank("point", limit); // GET /rank/point/:limit - Top players by point
        assertTrue(request.isSuccess());

        request = pb.ranks(limit); // GET /ranks/:limit - Top players
        assertTrue(request.isSuccess());
    }

    @Test
    public void testBadge() {
        Request request, request2;
        Map response;
        String badge_id;
        System.out.println("testBadge");

        request = pb.badges(); // GET /Badges - List info of all badges
        assertTrue(request.isSuccess());
        response = (Map) request.getResponse();
        if (response.containsKey("badges")) for (Object entry : (List) response.get("badges")) {
            badge_id = (String) ((Map) entry).get("badge_id");

            request2 = pb.badge(badge_id); // GET /Badge/:badge_id - Info about badge=:badge_id
            assertTrue(request2.isSuccess());

            break; // just once
        }
    }

    @Test
    public void testGoods() {
        Request request, request2;
        Map response;
        String goods_id;
        System.out.println("testGoods");

        request = pb.goodsList(); // GET /Goods - List info of all goods
        assertTrue(request.isSuccess());
        response = (Map) request.getResponse();
        if (response.containsKey("goods")) for (Object entry : (List) response.get("goods")) {
            goods_id = (String) ((Map) entry).get("goods_id");

            request2 = pb.goodInfo(goods_id); // GET /Goods/:goods_id - Info about goods=:goods_id
            assertTrue(request2.isSuccess());

            break; // just once
        }
    }

    @Test
    public void testPlayerQuery() {
        Request request;
        System.out.println("testPlayerQuery");

        request = pb.player(cl_player_id1); // POST /:cl_player_id1 - Info about player
        assertTrue(request.isSuccess());

        request = pb.playerDetail(cl_player_id1); // POST /:cl_player_id1/detail/all - Detailed info
        assertTrue(request.isSuccess());

        request = pb.points(cl_player_id1); // GET /:cl_player_id1/points - Info about exp/point
        assertTrue(request.isSuccess());

        request = pb.point(cl_player_id1, "point"); // GET /:cl_player_id1/point/point - Info about point
        assertTrue(request.isSuccess());

        request = pb.point(cl_player_id1, "exp"); // GET /:cl_player_id1/point/point - Info about exp
        assertTrue(request.isSuccess());

        request = pb.actionLastPerformedTime(cl_player_id1, action1); // GET /:cl_player_id1/action/:action/time - Info about action time
        assertTrue(request.isSuccess());

        request = pb.actionLastPerformed(cl_player_id1); // GET /:cl_player_id1/action/time - Info about action time
        assertTrue(request.isSuccess());

        request = pb.actionPerformedCount(cl_player_id1, action1); // GET /:cl_player_id1/action/:action/count - Info about action count
        assertTrue(request.isSuccess());

        request = pb.badgeOwned(cl_player_id1); // GET /:cl_player_id1/badge - Info about badge
        assertTrue(request.isSuccess());
    }

    @Test
    //@Ignore
    public void testPlayerAction() {
        Request request;
        System.out.println("testPlayerAction");

        request = pb.player(cl_player_id2); // POST /:cl_player_id2 - Info about not existing player
        assertFalse(request.isSuccess());

        request = pb.register(cl_player_id2, cl_player_id2, cl_player_id2 + "@email.com", "https://www.pbapp.net/images/default_profile.jpg"); // POST /:cl_player_id2/register - Register player
        assertTrue(request.isSuccess());

        request = pb.player(cl_player_id2); // POST /:cl_player_id2 - Info about player
        assertTrue(request.isSuccess());

        request = pb.update(cl_player_id2, new String[]{"exp="+cl_player_id2_exp,"level="+cl_player_id2_level}); // POST /:cl_player_id2/update - Update player
        assertTrue(request.isSuccess());

        request = pb.player(cl_player_id2); // POST /:cl_player_id2 - See the changes
        assertTrue(request.isSuccess());

        request = pb.login(cl_player_id2); // POST /:cl_player_id2/login - Login
        assertTrue(request.isSuccess());

        request = pb.logout(cl_player_id2); // POST /:cl_player_id2/logout - Logout
        assertTrue(request.isSuccess());

        request = pb.rule(cl_player_id2, action1, new String[]{"url="+action1_url}); // POST /Engine/rule - Do action
        assertTrue(request.isSuccess());

        request = pb.player(cl_player_id2); // POST /:cl_player_id2 - See the changes due to rewards
        assertTrue(request.isSuccess());

        request = pb.delete(cl_player_id2); // POST /:cl_player_id2/delete - Delete player
        assertTrue(request.isSuccess());

        request = pb.delete(cl_player_id2); // POST /:cl_player_id2/delete - Delete again
        assertFalse(request.isSuccess());

        request = pb.player(cl_player_id2); // POST /:cl_player_id2 - Should not find the player
        assertFalse(request.isSuccess());
    }
}
