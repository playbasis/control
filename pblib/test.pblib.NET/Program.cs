using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using pblib.NET;
using System.Diagnostics;
using System.Net;

namespace test.pblib.NET
{
	class Program
	{
		private static void print(string str)
		{
			dynamic obj = Playbasis.JsonToDynamic(str);
			Console.WriteLine(obj);
		}

		public static void printHandler(Object sender, UploadStringCompletedEventArgs e)
		{
			Console.WriteLine(e.Result);
		}

		static void Main(string[] args)
		{
			var pb = new Playbasis();
			if(!pb.auth("abc", "abcde"))
				Debug.Assert(false); //authentication failed

			string id = "1";

			//pb.login_async(id, printHandler);
			//pb.logout_async(id, printHandler);
			//pb.register_async("usr1", "test1", "test@email.com", "http://imageurl.jpg", printHandler, "facebook_id=123456", "first_name=testuser");
			//pb.update_async("usr1", printHandler, "first_name=testuser2");
			//pb.delete_async("usr1", printHandler);
			//pb.rule_async(id, "like", printHandler);

			print(pb.player(id));
			print(pb.register("usr1", "test1", "test@email.com", "http://imageurl.jpg", "facebook_id=123456", "first_name=testuser"));
			print(pb.update("usr1", "first_name=testuser2"));
			print(pb.delete("usr1"));
			print(pb.login(id));
			print(pb.logout(id));
			print(pb.points(id));
			print(pb.point(id, "exp"));
			print(pb.actionLastPerformed(id));
			print(pb.actionLastPerformedTime(id, "like"));
			print(pb.actionPerformedCount(id, "like"));
			print(pb.badgeOwned(id));
			print(pb.rank("exp", 10));
			print(pb.ranks(5));
			print(pb.badges());
			print(pb.badge("1"));
			print(pb.badgeCollections());
			print(pb.badgeCollection("1"));
			print(pb.actionConfig());
			print(pb.rule(id, "like"));
			Console.ReadKey();

			return;
		}
	}
}
